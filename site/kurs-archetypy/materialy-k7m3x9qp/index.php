<?php
/* ═══════════════════════════════════════════════════════════════════
   MATERIAŁY KURSU — brama logowania magic linkiem
   marcinpocwiardowski.com/kurs-archetypy/materialy-k7m3x9qp/

   JAK TO DZIAŁA
   1. Wchodzisz → formularz z adresem e-mail
   2. Adres sprawdzany w dozwolone.txt (lista kupujących)
   3. Na maila leci link z tokenem, ważny 30 minut
   4. Klik → ciastko na rok → wchodzisz bez pytania
   5. Nowe urządzenie = nowe logowanie

   Token działa WIELOKROTNIE przez te 30 minut, celowo — firmowe bramki
   pocztowe otwierają linki, zanim zrobi to odbiorca. Szczegóły przy
   samym kodzie, sekcja 1.

   ─────────────────────────────────────────────────────────────────
   CO MUSISZ ZROBIĆ PO WGRANIU (raz):

   • Wpisz adresy kupujących do dozwolone.txt, po jednym w linii
   • Sprawdź, czy katalog dane/ ma prawo zapisu (chmod 700)
   • WYŚLIJ SOBIE TEST na własny adres i sprawdź, czy nie ląduje
     w spamie. To najsłabszy punkt całej konstrukcji.

   ─────────────────────────────────────────────────────────────────
   JAK DODAĆ MODUŁ (co poniedziałek, 8 razy):

   Zjedź do tablicy $MODULY. Znajdź właściwy moduł i wpisz
   identyfikator filmu z YouTube w pole 'yt':

     'yt' => '',              ← przed
     'yt' => 'dQw4w9WgXcQ',   ← po

   ID to część adresu po "v=":
   youtube.com/watch?v=dQw4w9WgXcQ  →  dQw4w9WgXcQ

   Film ustaw na YouTube jako NIEPUBLICZNY (nie prywatny).
   Potem: ./wgraj-kurs.sh
   ═══════════════════════════════════════════════════════════════════ */

// ─── MODUŁY — tu wpisujesz filmy ───────────────────────────────────
$MODULY = [
  ['nr'=>1,'tytul'=>'Rozpoznanie terenu','linia'=>'psychologia Junga i archetypy','data'=>'14 września','yt'=>''],
  ['nr'=>2,'tytul'=>'Wojownik','linia'=>'moje życie to walka','data'=>'21 września','yt'=>''],
  ['nr'=>3,'tytul'=>'Kochanek','linia'=>'a ja zawsze w ogrodzie','data'=>'28 września','yt'=>''],
  ['nr'=>4,'tytul'=>'Archetypy w praktyce','linia'=>'jak wykorzystywać przeciwstawne energie archetypów, na przykładzie Wojownika i Kochanka','data'=>'5 października','yt'=>''],
  ['nr'=>5,'tytul'=>'Mag','linia'=>'napiszę o tym książkę','data'=>'12 października','yt'=>''],
  ['nr'=>6,'tytul'=>'Król','linia'=>'błogosławię','data'=>'19 października','yt'=>''],
  ['nr'=>7,'tytul'=>'Połączenie z archetypem przez ciało','linia'=>'wyjście z czysto intelektualnych rozważań','data'=>'26 października','yt'=>''],
  ['nr'=>8,'tytul'=>'Od psychologii chłopca do dojrzałego mężczyzny','linia'=>'','data'=>'2 listopada','yt'=>''],
];

// ─── USTAWIENIA ────────────────────────────────────────────────────
// WAŻNE: nadawca MUSI być adresem w domenie tego serwera, inaczej
// SPF nie przejdzie i maile z linkiem trafią prosto do spamu.
//
// Oba adresy są celowo takie same (decyzja z 11 VIII 2026). Dzięki temu
// cała korespondencja o kursie ma jeden adres — łatwiej ją filtrować,
// a przekazanie obsługi komuś innemu jest wtedy operacją w panelu
// hostingu, bez dotykania tego pliku.
//
// Dziś kurs@ jest ALIASEM przekierowującym na marcin@techne.pl.
// Przy oddawaniu obsługi trzeba go zamienić na prawdziwą skrzynkę —
// aliasu nie da się nikomu przekazać, można go tylko przekierować.
$NADAWCA      = 'kurs@marcinpocwiardowski.com';
$KONTAKT      = 'kurs@marcinpocwiardowski.com';
$CIASTKO      = 'archetypy_sesja';
$WAZNOSC_LINK = 1800;              // token: 30 minut
$WAZNOSC_SESJ = 365*24*3600;       // ciastko: rok
$ODSTEP_MIN   = 60;                // min. sekund między prośbami o link

$KATALOG  = __DIR__.'/dane';
$TOKENY   = $KATALOG.'/tokeny.json';
$SESJE    = $KATALOG.'/sesje.json';
$DOZWOL   = __DIR__.'/dozwolone.txt';

// ─── NARZĘDZIA ─────────────────────────────────────────────────────
function wczytaj($plik){
  if(!file_exists($plik)) return [];
  $t = json_decode(@file_get_contents($plik), true);
  return is_array($t) ? $t : [];
}
function zapisz($plik,$dane){
  @file_put_contents($plik, json_encode($dane), LOCK_EX);
}
function dozwolone($plik){
  if(!file_exists($plik)) return [];
  $l = file($plik, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);
  $out = [];
  foreach($l as $w){
    $w = mb_strtolower(trim($w));
    if($w==='' || $w[0]==='#') continue;   // puste linie i komentarze pomijamy
    $out[] = $w;
  }
  return $out;
}
function adresStrony(){
  $s = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off') ? 'https' : 'http';
  $p = strtok($_SERVER['REQUEST_URI'],'?');
  return $s.'://'.$_SERVER['HTTP_HOST'].$p;
}
function sprzatnij(&$t,$waznosc){
  $teraz = time();
  foreach($t as $k=>$v){ if(($v['czas'] ?? 0) + $waznosc < $teraz) unset($t[$k]); }
}

// Katalog na tokeny i sesje. Gdyby go zabrakło, tworzymy go RAZEM
// z blokadą dostępu — bez .htaccess tokeny byłyby czytelne z sieci.
if(!is_dir($KATALOG)) @mkdir($KATALOG, 0700, true);
if(!file_exists($KATALOG.'/.htaccess')){
  @file_put_contents($KATALOG.'/.htaccess',
    "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n".
    "<IfModule !mod_authz_core.c>\nOrder allow,deny\nDeny from all\n</IfModule>\n");
}

$blad = $sukces = '';
$email = null;

// ─── 1. KLIKNIĘCIE W LINK Z MAILA ──────────────────────────────────
if(isset($_GET['t'])){
  $tok = preg_replace('/[^a-f0-9]/','', $_GET['t']);
  $tokeny = wczytaj($TOKENY);
  sprzatnij($tokeny, $WAZNOSC_LINK);

  if(isset($tokeny[$tok])){
    $adres = $tokeny[$tok]['email'];
    // ─────────────────────────────────────────────────────────────
    // TOKEN NIE JEST KASOWANY PRZY UŻYCIU. Wygasa sam po 30 minutach
    // (sprzatnij() wyżej). To NIE jest przeoczenie — poprawka z 13 VIII 2026.
    //
    // Wcześniej stało tu `unset($tokeny[$tok])`, czyli token jednorazowy.
    // Wywracało to logowanie każdemu, kto ma pocztę za firmową bramką
    // (Microsoft Safe Links, Proofpoint, Barracuda, ESET). Takie bramki
    // OTWIERAJĄ linki z maili, żeby je przeskanować, ZANIM odbiorca kliknie.
    // Skaner zużywał token, a kupujący dostawał „Ten link wygasł albo
    // został już użyty" — za każdym razem, w każdej przeglądarce.
    //
    // Zgłosił to kupujący z domeny firmowej, jedyny taki wśród trzynastu.
    // Test z 11 VIII tego nie złapał, bo szedł na Fastmaila.
    //
    // Koszt bezpieczeństwa: token działa wielokrotnie przez 30 minut.
    // Skaner, który go otworzy, dostanie ciastko sesji i je wyrzuci —
    // nie utrzymuje stanu. Kupujący dostaje własne ciastko, klikając sam.
    // ─────────────────────────────────────────────────────────────
    zapisz($TOKENY, $tokeny);        // zapis po sprzątnięciu wygasłych

    $sesje = wczytaj($SESJE);
    sprzatnij($sesje, $WAZNOSC_SESJ);
    $sid = bin2hex(random_bytes(32));
    $sesje[$sid] = ['email'=>$adres,'czas'=>time()];
    zapisz($SESJE, $sesje);

    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']!=='off');
    setcookie($CIASTKO, $sid, [
      'expires'  => time()+$WAZNOSC_SESJ,
      'path'     => '/',
      'secure'   => $https,
      'httponly' => true,
      'samesite' => 'Lax',
    ]);
    header('Location: '.adresStrony());
    exit;
  }
  zapisz($TOKENY, $tokeny);
  // Po poprawce z 13 VIII token nie jest już zużywany przy użyciu, więc
  // jedyną przyczyną jest upływ 30 minut. Komunikat mówi teraz dokładnie to.
  $blad = 'Ten link stracił ważność — jest ważny 30 minut. Poproś o nowy, przyjdzie od razu.';
}

// ─── 2. WYLOGOWANIE ────────────────────────────────────────────────
if(isset($_GET['wyloguj'])){
  if(isset($_COOKIE[$CIASTKO])){
    $sesje = wczytaj($SESJE);
    unset($sesje[$_COOKIE[$CIASTKO]]);
    zapisz($SESJE, $sesje);
  }
  setcookie($CIASTKO,'',['expires'=>time()-3600,'path'=>'/']);
  header('Location: '.adresStrony());
  exit;
}

// ─── 3. SPRAWDZENIE CIASTKA ────────────────────────────────────────
if(isset($_COOKIE[$CIASTKO])){
  $sesje = wczytaj($SESJE);
  sprzatnij($sesje, $WAZNOSC_SESJ);
  $sid = $_COOKIE[$CIASTKO];
  if(isset($sesje[$sid])){
    $adres = mb_strtolower($sesje[$sid]['email']);
    // adres musi NADAL być na liście kupujących
    if(in_array($adres, dozwolone($DOZWOL), true)) $email = $adres;
  }
}

// ─── 4. PROŚBA O LINK ──────────────────────────────────────────────
if($_SERVER['REQUEST_METHOD']==='POST' && !$email){
  $podany = mb_strtolower(trim($_POST['email'] ?? ''));

  if(!filter_var($podany, FILTER_VALIDATE_EMAIL)){
    $blad = 'To nie wygląda na adres e-mail.';
  } elseif(!in_array($podany, dozwolone($DOZWOL), true)){
    // Świadomie mówimy wprost. Uzasadnienie w notatkach projektu:
    // przy kilkunastu kupujących literówka w adresie jest znacznie
    // częstsza niż ktoś sprawdzający, kto kupił kurs.
    $blad = 'Nie znajduję tego adresu wśród kupujących. Sprawdź, czy to ten sam, którym płaciłeś. Jeśli tak — napisz na '.$KONTAKT.'.';
  } else {
    $tokeny = wczytaj($TOKENY);
    sprzatnij($tokeny, $WAZNOSC_LINK);

    $ostatni = 0;
    foreach($tokeny as $v){ if($v['email']===$podany) $ostatni = max($ostatni,$v['czas']); }

    if(time() - $ostatni < $ODSTEP_MIN){
      $blad = 'Link został już wysłany. Sprawdź skrzynkę, także folder ze spamem.';
    } else {
      $tok = bin2hex(random_bytes(24));
      $tokeny[$tok] = ['email'=>$podany,'czas'=>time()];
      zapisz($TOKENY, $tokeny);

      $link = adresStrony().'?t='.$tok;
      $tresc = "Cześć,\n\nkliknij, żeby wejść na materiały kursu:\n\n"
             . $link."\n\n"
             . "Link jest ważny 30 minut. Po wejściu przeglądarka\n"
             . "zapamięta Cię na rok.\n\n"
             . "Gdyby link się nie otwierał, skopiuj cały adres powyżej\n"
             . "i wklej go w pasek przeglądarki.\n\nMarcin";
      // MIME-Version i Content-Transfer-Encoding dopisane 13 VIII 2026.
      // Bez nich treść z polskimi znakami jest 8-bitowa i nieopisana, więc
      // bramka pocztowa może ją przekodować na quoted-printable — a to łamie
      // linie po 76 znakach. Adres z tokenem ma 117 znaków, czyli złamanie
      // wypada w środku tokenu i link przestaje działać. Deklaracja 8bit
      // mówi serwerom wprost, że przekodowywać nie trzeba.
      $naglowki = "From: Marcin Poćwiardowski <$NADAWCA>\r\n"
                . "Reply-To: $KONTAKT\r\n"
                . "MIME-Version: 1.0\r\n"
                . "Content-Type: text/plain; charset=UTF-8\r\n"
                . "Content-Transfer-Encoding: 8bit\r\n";

      @mail($podany, 'Wejście na materiały kursu o archetypach', $tresc, $naglowki);
      $sukces = 'Link został wysłany na '.htmlspecialchars($podany).'. Sprawdź skrzynkę — jeśli go nie widzisz, zajrzyj do spamu.';
    }
  }
}

header('X-Robots-Tag: noindex, nofollow', true);
?><!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow, noarchive">
<title>Męskie archetypy w działaniu — materiały</title>
<style>
  :root{
    --bg:#faf8f5; --ink:#1f1c19; --muted:#5f574d;
    --accent:#7c3a2d; --rule:#e2dbd1; --panel:#f2ece4;
  }
  *{box-sizing:border-box}
  body{
    margin:0;background:var(--bg);color:var(--ink);
    font-family:"Iowan Old Style","Palatino Linotype",Palatino,Georgia,serif;
    font-size:18px;line-height:1.6;
  }
  .wrap{max-width:720px;margin:0 auto;padding:0 24px}

  header{padding:64px 0 30px;border-bottom:1px solid var(--rule)}
  h1{font-size:1.9rem;line-height:1.25;font-weight:600;margin:0 0 14px}
  .sub{color:var(--muted);font-style:italic;margin:0}

  .kto{
    display:flex;justify-content:space-between;align-items:baseline;
    gap:16px;flex-wrap:wrap;
    padding:14px 0;border-bottom:1px solid var(--rule);
    font-family:system-ui,-apple-system,sans-serif;
    font-size:.85rem;color:var(--muted);
  }
  .kto b{color:var(--ink);font-weight:600}
  .kto a{color:var(--muted)}

  /* Zdjęcie nagłówkowe — ten sam plik, co otwiera landing.
     Leży w ../archetypy-img/, czyli piętro wyżej: nie dublujemy go.
     Szerokość ograniczona do .wrap (720px), inaczej rozjeżdżałoby się
     z resztą strony — na landingu jest pełnoekranowe, bo tam cała
     kompozycja jest szersza. */
  .hero{
    display:block;width:100%;height:auto;
    border-radius:3px;margin:34px 0 6px;
  }

  .info{
    background:var(--panel);border-radius:3px;
    padding:20px 24px;margin:30px 0 44px;font-size:.96rem;
  }
  .info p{margin:0 0 .7em}
  .info p:last-child{margin:0}

  ol.mods{list-style:none;padding:0;margin:0}
  ol.mods > li{padding:30px 0;border-bottom:1px solid var(--rule)}
  ol.mods > li:first-child{border-top:1px solid var(--rule)}

  .mod-head{display:flex;gap:16px;align-items:baseline}
  .num{font-family:system-ui,-apple-system,sans-serif;font-size:.78rem;
       font-weight:700;color:var(--accent);min-width:22px}
  .mod-title{font-weight:700;font-size:1.12rem}
  .mod-line{color:var(--muted);font-style:italic;font-size:1rem}

  .film{position:relative;padding-bottom:56.25%;height:0;
        margin:18px 0 0 38px;background:#000;border-radius:3px;overflow:hidden}
  .film iframe{position:absolute;top:0;left:0;width:100%;height:100%;border:0}

  .wkrotce{margin:12px 0 0 38px;color:var(--muted);
           font-family:system-ui,-apple-system,sans-serif;font-size:.86rem}
  li.locked .mod-title,li.locked .mod-line{opacity:.45}

  /* logowanie */
  .brama{max-width:440px;margin:0 auto;padding:60px 24px 80px;text-align:left}
  .brama h2{font-size:1.25rem;margin:0 0 14px;font-weight:600}
  .brama p{color:var(--muted);font-size:.98rem;margin:0 0 24px}
  .brama input[type=email]{
    width:100%;padding:14px 16px;font-size:1rem;font-family:inherit;
    border:1px solid var(--rule);border-radius:3px;background:#fff;
    color:var(--ink);margin-bottom:12px;
  }
  .brama button{
    width:100%;padding:15px;background:var(--accent);color:#fff;border:0;
    border-radius:3px;font-family:system-ui,-apple-system,sans-serif;
    font-size:1rem;font-weight:600;cursor:pointer;
  }
  .brama button:hover{background:#632d23}
  .komunikat{
    padding:14px 16px;border-radius:3px;margin-bottom:20px;font-size:.94rem;
  }
  .zle{background:#f6e7e3;color:#7c3a2d}
  .ok{background:var(--panel);color:var(--ink)}

  footer{margin-top:46px;padding:28px 0 60px;border-top:1px solid var(--rule);
         color:var(--muted);font-size:.86rem;
         font-family:system-ui,-apple-system,sans-serif}
  footer p{margin:0 0 .6em}

  @media (max-width:600px){
    body{font-size:16.5px}
    header{padding:44px 0 24px}
    h1{font-size:1.5rem}
    .film,.wkrotce{margin-left:0}
  }
</style>
</head>
<body>

<?php if(!$email): /* ══ EKRAN LOGOWANIA ══ */ ?>

<div class="brama">
  <h2>Materiały kursu o archetypach</h2>
  <p>Podaj adres, którym płaciłeś. Wyślę na niego link wejściowy —
  potem przeglądarka zapamięta Cię na rok.</p>

  <?php if($blad): ?><div class="komunikat zle"><?=htmlspecialchars($blad)?></div><?php endif; ?>
  <?php if($sukces): ?><div class="komunikat ok"><?=$sukces?></div><?php endif; ?>

  <?php if(!$sukces): ?>
  <form method="post">
    <input type="email" name="email" placeholder="twój@adres.pl" required autofocus>
    <button type="submit">Wyślij link</button>
  </form>
  <?php endif; ?>
</div>

<?php else: /* ══ MATERIAŁY ══ */ ?>

<header>
  <div class="wrap">
    <h1>Męskie archetypy w działaniu,<br>jako droga do dojrzałości</h1>
    <p class="sub">Materiały kursu</p>
  </div>
</header>

<div class="wrap">

  <img class="hero" src="../archetypy-img/4archetypy.jpg" width="1500" height="845"
       alt="Cztery archetypy męskie w malarstwie: Wojownik, Kochanek, Król, Mag">

  <div class="kto">
    <span>Materiały dla <b><?=htmlspecialchars($email)?></b></span>
    <a href="?wyloguj=1">wyloguj</a>
  </div>

  <div class="info">
    <p>Nowy moduł pojawia się tutaj w każdy poniedziałek, od 14 września
    do 2 listopada. Nie musisz nic robić — wystarczy wrócić na tę stronę.</p>
    <p>Materiały są dostępne do końca 2027 roku.</p>
  </div>

  <ol class="mods">
  <?php foreach($MODULY as $m): $jest = $m['yt']!==''; ?>
    <li class="<?= $jest ? '' : 'locked' ?>">
      <div class="mod-head">
        <span class="num"><?=$m['nr']?></span>
        <div>
          <span class="mod-title"><?=htmlspecialchars($m['tytul'])?></span>
          <?php if($m['linia']): ?><br><span class="mod-line"><?=htmlspecialchars($m['linia'])?></span><?php endif; ?>
        </div>
      </div>
      <?php if($jest): ?>
        <div class="film"><iframe
          src="https://www.youtube-nocookie.com/embed/<?=htmlspecialchars($m['yt'])?>"
          title="Moduł <?=$m['nr']?>" allowfullscreen loading="lazy"></iframe></div>
      <?php else: ?>
        <p class="wkrotce">Dostępny <?=$m['data']?></p>
      <?php endif; ?>
    </li>
  <?php endforeach; ?>
  </ol>

  <footer>
    <p>Materiały do oglądania online, bez pobierania. Dostęp do końca 2027 roku.</p>
    <p>Jeśli coś nie działa, napisz na
    <a href="mailto:<?=$KONTAKT?>"><?=$KONTAKT?></a>.</p>
  </footer>

</div>

<?php endif; ?>

</body>
</html>
