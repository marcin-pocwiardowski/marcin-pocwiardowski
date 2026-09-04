# SESJE — marcinpocwiardowski.com

Log decyzji dotyczących nowej strony i bloga. Zakładany 11 VIII 2026, bo do tej
pory ten wątek nie miał gdzie zapisywać *dlaczego* — `site-structure.md` trzyma
ustalenia, ale nie odrzucone warianty.

---

## 11 VIII 2026 — kierunek wizualny i runbook bloga

### Decyzje

**1. Osobny język wizualny, nie dziedziczony z lowenformen.pl.**
Rozważane były trzy warianty:

| Wariant | Dlaczego odrzucony / wybrany |
|---|---|
| Spójny z `lowenformen.pl` (Figtree, kremowe tło, limonka `#94e130`) | **Odrzucony.** `marcinpocwiardowski.com` jest marką-parasolem nad terapią indywidualną, warsztatem dla kobiet i L4M. Limonkowy, celowo męski i „high-energy" język L4M zawęziłby ten parasol do jednej grupy odbiorców. `DESIGN.md` L4M sam opisuje siebie jako *„confident and masculine"* — to zaleta tam i wada tutaj |
| Ciemny, fotograficzny | **Odrzucony.** Stoi i upada na jakości zdjęć, a decyzja z 24 VII to świadome porzucenie starego katalogu `uploads`. Nie było na czym budować |
| **Redakcyjny i ciepły** | **Wybrany.** Szeryfowa Fraunces + Inter, paleta ziemista (terakota `#9c4a2c`, len `#faf6f0`, mech `#46503c`), hairline'y zamiast cieni, dużo pionowego oddechu. Osobna tożsamość, która nie kłóci się z L4M — bo obie stoją na ciepłym papierze, tylko akcent jest inny |

**2. Prototyp jako jeden samodzielny plik, nie przebudowa całego repo.**
`prototyp/index.html` ma CSS i JS w środku, żeby otwierał się podwójnym kliknięciem
bez serwera. Rozbicie na `assets/style.css` + `partials/` **dopiero po akceptacji
kierunku** — odrzucony był wariant „od razu wszystkie podstrony", bo poprawka
kierunku dotknęłaby wtedy dziesięciu plików zamiast jednego.

**3. Blog: runbook do wyklikania, nie instalacja na żywo.**
Odrzucony wariant przejścia przez panel Zenboxa w przeglądarce — Claude nie ma
dostępu do panelu, a kliknięcia i tak są po stronie Marcina. Runbook:
`BLOG-WORDPRESS.md`.

**4. Kolejność wdrożenia: `/blog` PRZED podmianą katalogu głównego.**
Ustalone przy pisaniu runbooka, wcześniej nigdzie nie zapisane. Powód techniczny:
reguły rewrite starego WordPressa mają `RewriteCond %{REQUEST_FILENAME} !-d`, więc
w momencie, w którym `/blog/` istnieje jako prawdziwy katalog, root przestaje go
przechwytywać. **Dwie instalacje WP mogą stać obok siebie**, o ile mają osobne bazy
i osobne prefiksy tabel. Dzięki temu blog można zbudować i obejrzeć bez ruszania
produkcji.

**5. `robots.txt` nie jest narzędziem do usuwania starych zdjęć z Google.**
Doprecyzowanie decyzji z 24 VII, która mogła być czytana odwrotnie. `Disallow`
blokuje odwiedziny robota, ale zaindeksowany już zasób potrafi w wynikach zostać —
a co gorsza, zablokowanie starego `/wp-content/uploads/` **utrudniłoby** Google
odkrycie 404 i opóźniło usunięcie. Kolejność, która działa: fizyczne 404 + prośba
o usunięcie prefiksu w Search Console. `Disallow` zostaje wyłącznie jako
zabezpieczenie **nowego** katalogu `/blog/wp-content/uploads/`.

### Powstałe pliki

| Plik | Co to |
|---|---|
| `prototyp/index.html` | Samodzielny prototyp strony głównej do oceny |
| `prototyp/img/` | **Zdjęcia tymczasowe** skopiowane z `lowenformen-pl/images/nowe/` — do podmiany |
| `BLOG-WORDPRESS.md` | Runbook świeżej instalacji WP w `/blog`, 12 kroków + lista weryfikacyjna |
| `wp-config-dodatki.php` | Ściągawka hardeningu do wklejenia w `wp-config.php` (krok 6 runbooka) |
| `robots.txt` | Do katalogu głównego domeny; obowiązuje też `/blog` |
| `SESJE.md` | Ten plik |

### Dopisek tego samego dnia — trzecia metoda: psychologia głębi

**Decyzja Marcina:** archetypy wchodzą na stronę jako **trzecia metoda na równi**
z Lowenem i ISTDP, bez różnicowania statusu. Marcin zaznaczył przy tym wprost,
że **nie ma formalnego wyszkolenia w psychologii głębi.**

Rozważane były trzy ramowania:

| Wariant | Los |
|---|---|
| Inna kategoria — „język/mapa, nie metoda" | Odrzucony. Rekomendowany przeze mnie: różnica statusu wynikałaby z samego opisu, bez słowa „certyfikat" |
| Wprost „bez papierów, z praktyki" | Odrzucony — na stronie usługowej brzmi jak dyskwalifikowanie samego siebie |
| **Trzy metody na równi** | **Wybrany przez Marcina.** Odradzałem (sugeruje wyszkolenie, którego nie ma, na stronie psychologa i psychoterapeuty). Decyzja jego, świadoma, podjęta po przeczytaniu zastrzeżenia |

**Jak to rozwiązałem w treści.** Trzecia kolumna dostaje identyczną wagę wizualną,
ale copy **nie stwierdza wyszkolenia** — opisuje, co Marcin faktycznie robi
(*„Traktuję je jak mapę"*, meta: *„Praca w męskich grupach · kurs wideo"*). Nigdzie
nie pada „szkoliłem się", „certyfikat" ani nazwa instytutu. Równość wizualna bez
fałszywej asercji — to było jedyne miejsce, w którym dało się pogodzić decyzję
z ostrożnością.

**Kurs o archetypach na stronie głównej:** tak, z zapisem na wieści o kolejnej
edycji. Karta 06 w Ofercie + pozycja w menu i stopce, wszystko celuje w istniejący
adres `/kurs-archetypy/`.

#### 🔴 Kolizja z parametrami kursu — do rozstrzygnięcia

`kurs-archetypy/STAN_PROJEKTU.md` mówi w dwóch miejscach coś przeciwnego niż
„kolejna edycja":

> *„**Nie jest evergreenem** — zapisy zamykają się raz i się nie otwierają ponownie."*
> *„🔴 Zamknięcie zapisów: 16 VIII 2026, 23:59 — bez przedłużeń, **bez drugiego otwarcia**."*

Da się to czytać dwojako: albo „ta edycja się nie otworzy ponownie" (wtedy druga
edycja kiedyś jest OK), albo „ten produkt sprzedaje się raz w życiu" (wtedy karta
w Ofercie obiecuje coś, czego nie będzie). **Deklaracja o braku przedłużeń była
publiczna**, więc to nie jest kosmetyka. Do decyzji Marcina, zanim strona pójdzie na żywo.

Do czasu rozstrzygnięcia copy karty jest napisane ostrożnie: *„Zapisz się na wieści
o kolejnej edycji"* — bez daty, bez obietnicy, że edycja w ogóle powstanie.

#### Zaczepienie o `TERMINY.md` — 16 VIII

W `TERMINY.md` na 16 VIII stoi już *„zdjąć przycisk zakupu"*. **To jest ta sama
operacja na tej samej stronie**, o czym nigdzie nie było napisane: `/kurs-archetypy/`
przestaje 16 VIII o 23:59 być stroną sprzedażową i w tym samym ruchu może stać się
stroną „sprzedaż zamknięta + zostaw adres". Jeden wjazd na serwer zamiast dwóch.
Mechanizm zapisu (tag w Mailchimpie? osobny formularz?) — **nieustalony.**

### Dopisek — korekta stażu, 11 VIII

Marcin: *„napisałeś 15 lat doświadczenia, a ja pracuję terapeutycznie od 2009 r.
Warsztaty prowadzę od 2015."*

**Skąd wzięło się 15.** Z `content-audit.md`, czyli z audytu **starej strony**, gdzie
stało „15 lat pracy indywidualnej". Przepisałem to jako fakt. Audyt jest zapisem tego,
co strona **mówi**, nie tego, jak **jest** — do tego pliku dopisane ostrzeżenie, żeby
nikt nie powtórzył tego ruchu.

**Twarde punkty odniesienia (potwierdzone przez Marcina):** praca terapeutyczna **od 2009**,
warsztaty **od 2015**.

**Zabezpieczenie techniczne, nie tylko poprawka.** Liczby lat nie są już wpisane na sztywno.
Znaczniki mają `data-since="2009"` / `data-since="2015"`, a skrypt liczy różnicę względem
bieżącego roku i podmienia treść. W HTML zostaje poprawna liczba jako fallback dla
wyłączonego JS i dla robotów. Powód: **„17 lat" wpisane dziś jest nieprawdą od 1 stycznia
2027** i nikt tego nie zauważy — dokładnie tak, jak nikt nie zauważył piętnastki.

**Usunięte przy okazji:** z karty warsztatu zniknęło „dziewiąty rok" — liczba pochodziła
z tego samego źródła i nie została potwierdzona.

⚠️ **Do sprawdzenia gdzie indziej:** stara strona twierdziła też *„20 lat pracy z mężczyznami,
9 lat LOWEN for MEN"* (cytowane w `../L4M/STAN_PROJEKTU.md` i na `lowenformen.pl`).
Nie wiadomo, czy te dwie liczby przeszły weryfikację, czy pochodzą z tego samego
nieprawdziwego zestawu. **Nie ruszałem ich** — do potwierdzenia przez Marcina.

### Otwarte na kolejną sesję

- **Zdjęcia.** Marcin ma wskazać folder — dopóki go nie ma, w prototypie stoją
  zdjęcia z L4M i to widać (są warsztatowe, nie portretowe).
- **Akceptacja kierunku wizualnego** → dopiero potem rozbicie na `assets/` +
  `partials/` i budowa podstron.
- **Treść `kim-jestem.md`** — plik istnieje, jest pusty od 23 V.
- **`geo.md`** — pusty, decyzja o strategii widoczności w AI niepodjęta.
- **Commit repo.** 20+ pozycji niezacommitowanych od 24 VII, teraz więcej.
  Wisi w `TERMINY.md` w sekcji „terminy bez daty".
- **Czy kurs o archetypach będzie miał drugą edycję** — patrz kolizja wyżej.
  Bez tej decyzji karta 06 w Ofercie wisi w powietrzu.
- **Mechanizm listy oczekujących** na kurs — tag w Mailchimpie czy osobny formularz.
- **`/sklep/`, `/koszyk/`, `/products/`** — czy WooCommerce był w ogóle używany.
- **Weryfikacja ścieżki katalogu domeny na Zenboxie** — w `OPERACJE.md` mamy
  zapisaną tylko ścieżkę dla `lowenformen.pl`.

### Czego NIE zweryfikowano

Prototyp nie został obejrzany w przeglądarce — w sandboksie nie ma silnika
renderującego. Sprawdzone zostały: bilans znaczników HTML (46 div, 40 span,
5 section, 42 a — wszystko domknięte), poprawność adresu Google Fonts (odpytany
na żywo), zbilansowanie komentarzy w `wp-config-dodatki.php`. **Ocena wizualna
jest po stronie Marcina** i to jest jedyny sensowny test tej roboty.

---

## 11 VIII 2026 — `/warsztat-dla-kobiet`: nowa treść do wklejenia

**Powstały dwa pliki w korzeniu tego katalogu:**

- `warsztat-dla-kobiet_KOD_DO_WKLEJENIA.html` — gotowy blok HTML do Gutenberga,
  wzorem `media_KOD_DO_WKLEJENIA.html` z 1 VIII. Zawiera instrukcję wklejania
  i **dwa miejsca oznaczone `UZUPEŁNIĆ`**: adres sali oraz opcjonalna liczba wolnych miejsc.
- `warsztat-dla-kobiet_NOTATKA.md` — co zmienione i dlaczego, z tabelą poprawek
  faktograficznych.

**Zakres:** przepisanie całej treści w głosie z `../L4M/landing_page_weekend_v6_glos_marcin.md`
(decyzja Marcina). Nieruszone: cena 1 500 zł, termin, godziny, przeciwwskazania, mail
jako jedyna droga zapisu.

**Trzy zmiany, które mają znaczenie poza samą stroną:**

1. **Manifest „dlaczego ja, mężczyzna, prowadzę warsztat dla kobiet"** wchodzi jako drugi
   blok strony. Zamyka temat otwarty w `../L4M/strategia_akwizycji_2026.md` §6A od maja.
2. **Trzy opinie od mężczyzn zdjęte** — zastąpione sekcją „Pierwsza edycja — mówię wprost".
   To był punkt otwarty w `../L4M/STAN_PROJEKTU.md` („opinie od mężczyzn, do wymiany przed
   startem L4W"). 🟢 **Sekcja ma datę ważności — po 20 IX wchodzą głosy uczestniczek.**
3. **Liczby wyprostowane wg `../FAKTY.md`:** „ponad 8 lat" → 9 lat L4M i 11 lat zajęć
   lowenowskich, „ponad 600 facetów" → 700+, dołożone 17 lat pracy terapeutycznej i ISTDP.

⚠️ **Ten katalog ma 25 niezacommitowanych pozycji od 24 VII** — dwa nowe pliki dochodzą
do tej sterty. `bin/stan.py` alarmuje o tym przy każdym uruchomieniu i słusznie.

**Pełny kontekst sesji:** `../L4M/SESJE.md`, 11 VIII 2026.

---

## 04 IX 2026 — Zwrot: dokładna kopia żywej strony, nie redesign

**Decyzja Marcina, zastępująca dotychczasowy kierunek na razie.** Zapytany od
czego zacząć (prototyp / `kim-jestem.md` / coś innego), odpowiedział: *„inaczej
— robimy kopię tego co jest, dokładną, z treścią i urlami i blogiem w
katalogu /blog (tam zainstaluję nowego wordpressa) — a potem będę sobie to
powoli zmieniał. Nie mam teraz zasobów na zmienianie wszystkiego, chcę móc
potem po kawałeczku udoskonalać."**

To nie jest kontynuacja pracy z 11 VIII (kierunek wizualny, przeprojektowane
menu, `/oferta/`, `/kim-jestem/`) — to osobny, prostszy krok przed nią.
Redesign nie jest odrzucony, jest odłożony.

### Co zrobione

Sprawdzona żywa sitemapa (`page-sitemap.xml`) zamiast polegania na
`content-audit.md` z 24 VII — okazał się nieaktualny (menu na żywo ma dziś
np. link **ARCHETYPY**, którego audyt nie znał). 16 adresów na sitemapie;
Marcin zdecydował pominąć trzy puste skorupki WooCommerce (`/sklep/`,
`/koszyk/`, `/products/` — sprawdzone, zero produktów). Pozostałych **13
stron skopiowanych dosłownie** (tekst, obrazki, linki) z produkcji:
`/`, `/sesje/`, `/kontakt/`, `/media/` + 2 podstrony artykułów,
`/newsletter/`, `/polityka-prywatnosci/`, `/szkola-meskich-grup/`,
`/trening-terapeutyczny/`, `/warsztat-dla-kobiet/`, `/lowen-for-men-advanced/`,
`/lowen-for-men-moj-meski-rod-i-ja/`.

**Draft redesignu z 11 VIII zabezpieczony, nie skasowany** —
`_redesign-draft-backup-11VIII2026/` (kopia `index.html`, `kontakt/`,
`media/`, `newsletter/`, `polityka-prywatnosci/`, `szkola-meskich-grup/`,
`trening-terapeutyczny/`, `warsztat-dla-kobiet/`, obu `lowen-for-men-*/`,
`partials/`). Foldery `oferta/`, `kim-jestem/`, `terapia-indywidualna/`
zostały na miejscu bez zmian — nie kolidują z żadnym adresem z żywej strony.

**`.htaccess`:** reguła `/sesje/ → /kontakt/ [301]` z 24 VII była założona pod
redesign (scalenie stron). Na produkcji `/sesje/` wciąż istnieje osobno —
przy dokładnej kopii ta reguła złamałaby cel. **Zawieszona, nie usunięta**
(zakomentowana z datowaną notatką) — wraca, jeśli/gdy scalenie zostanie
podjęte świadomie.

Obrazki: ściągnięte tylko te faktycznie linkowane na tych 13 stronach (10
plików, `assets/img/`) — nie cały `wp-content/uploads/`, zgodnie z decyzją
z 24 VII o nieprzenoszeniu całego starego katalogu mediów (bez nowej
ekspozycji: to i tak to, co dziś jest publiczne).

Struktura: wspólne partiale `partials/nav.html`, `partials/footer.html` +
nowy `partials/events.html` (sekcja „Nadchodzące wydarzenia" była
identycznym powtórzeniem na każdej podstronie na żywo — teraz jeden plik,
ta sama treść). Nav ujednolicony wg wersji z homepage (na żywo menu jest
niespójne między podstronami — część ma link ARCHETYPY, część nie; wybrana
pełniejsza wersja, bez zmiany żadnej informacji).

### Świadome luki — nieukryte

- **Formularze zapisu** (`/lowen-for-men-advanced/`, `/lowen-for-men-moj-meski-rod-i-ja/`,
  WPForms na żywo) i **formularz newslettera** (Mailchimp na żywo) nie mają
  dziś zaplecza — zastąpione przyciskiem/linkiem `mailto:` z widoczną
  żółtą notatką na stronie. Wymaga decyzji później: Formspree/inny odbiornik
  formularzy, czy zostawić mailto.
- **Cookie banner** z żywej strony (WordpressowY plugin) nie odtworzony —
  strona dziś nie stawia własnych ciasteczek śledzących (brak pikseli), więc
  na razie mniejsze ryzyko niż brak banera przy aktywnym trackingu.
- Stopka **bez** „Dumnie wspierane przez WordPress" (nieprawda dla statycznej
  kopii) — jedyna świadoma różnica treściowa względem żywej strony.
- Nie wdrożone nigdzie. Ścieżka katalogu domeny na Zenboksie dla
  `marcinpocwiardowski.com` nieznana (mamy tylko dla `lowenformen.pl` —
  `../OPERACJE.md`) — do ustalenia przed pierwszym wdrożeniem.
- `/blog` nietknięty — czeka na świeży WordPress Marcina.

### Zweryfikowane programowo

Bilans znaczników HTML, obecność wszystkich obrazków referencjonowanych
przez strony, wszystkie linki wewnętrzne wskazują na istniejące pliki —
zero rozjazdów. **Ocena wizualna w przeglądarce nadal po stronie Marcina**
(sandbox nie renderuje, jak w notatce z 11 VIII).

---

## 04 IX 2026 (ciąg dalszy) — poprawki wizualne + formularze

Po pierwszym podglądzie lokalnym wyszły trzy błędy, wszystkie poprawione tego
samego dnia:

1. **Zdjęcie grupowe pomylone z logo** — próba wciśnięcia banera 868×226 w
   miejsce małego logo. Poprawka: tekstowy tytuł wrócił, zdjęcie jest osobnym
   pełnej szerokości banerem pod menu (`partials/banner.html`), tak jak na
   żywo — potwierdzone zrzutem ekranu z produkcji.
2. **`.site-header{display:flex}` zostawiony po nieudanej próbie z logo** —
   ściskał tytuł, tagline i menu w jeden rząd. Usunięty, nagłówek
   wyśrodkowany (`text-align:center`).
3. **„Nadchodzące wydarzenia" przeniesione na dół zamiast zostać jako
   sidebar** — Marcin: „nadchodzące wydarzenia chcę mieć na górze, tak jak
   jest obecnie, nie róbmy jednokolumnowego układu". Poprawka: układ
   dwukolumnowy (`content-grid` + `aside.sidebar`, sticky), spada do jednej
   kolumny poniżej 820px.

**Formularze** (newsletter, zapis L4M, zapis L4M Advanced) — Marcin wybrał
PHP+PHPMailer jak `kontakt.php` na `lowenformen.pl`, z jednym świadomym
odstępstwem: dane SMTP w `mail-config.php` (poza gitem), nie w kodzie —
bo przy szukaniu wzorca wyszło, że `kontakt.php` ma **hasło SMTP jawnie
zacommitowane do gita** (zgłoszone Marcinowi, zapisane w `rejestr/ZOBOWIAZANIA.md`
i `rejestr/DECYZJE.md`, nie naprawiane tutaj — to inny projekt).

Zbudowane: `formularz.php` (wspólna końcówka), `phpmailer/` (skopiowana
biblioteka z `lowenformen-pl`, bez sekretów), `mail-config.example.php`
(szablon do repo) + `mail-config.php` (gitignored, placeholder do
uzupełnienia), `assets/forms.js` (fetch + honeypot + status w UI). Trzy
formularze podłączone.

**Nie działa jeszcze naprawdę** — brakuje realnej skrzynki SMTP na
`marcinpocwiardowski.com` (dziś tylko alias `kurs@`, bez DKIM). Zapisane
jako otwarte zobowiązanie.

Nie mam jak sam obejrzeć strony w przeglądarce — moja przeglądarka działa
w innym środowisku niż komputer Marcina i nie dosięga jego `localhost`;
próba uruchomienia headless Chromium we własnym sandboksie też nie wyszła
(brak uprawnień root do bibliotek systemowych). Cała weryfikacja wizualna
szła przez zrzuty ekranu od Marcina + jeden zrzut z produkcji (mój browser
dosięga internetu, nie jego lokalnego serwera).

---

## 04 IX 2026 (ciąg dalszy) — kopia dogania ruchomy cel

WordPress był edytowany na żywo w trakcie tej sesji (Marcin, w innym oknie).
Trzy strony zdjęte z kopii, w kolejności zgłaszania:

1. **`/lowen-for-men-moj-meski-rod-i-ja/`** — „wypada, linki mają linkować na
   lowenformen.pl". 301 w `.htaccess`.
2. **`/warsztat-dla-kobiet/`, `/trening-terapeutyczny/`** — usunięte z
   WordPressa tydzień wcześniej (nie przeze mnie zauważone — moje pierwsze
   ściągnięcie zwróciło ich treść mimo to, prawdopodobnie cache). Zdjęte bez
   przekierowania — na żywo to zwykłe 404.
3. **`/sesje/`** — „wypada", scalona z `/kontakt/`. To wraca do pierwotnej
   decyzji redesignu z 24 VII (`.htaccess` miał tę regułę od początku,
   zawiesiłem ją wcześniej dziś z ostrożności, teraz odwieszona).

Wszystkie trzy zarchiwizowane w `_redesign-draft-backup-11VIII2026/` z
dopiskiem `-KOPIA-ZYWEJ-04IX`, nie skasowane. `partials/events.html`
oczyszczony z martwego wpisu o warsztacie dla kobiet.

**Stan na koniec sesji: 9 aktywnych stron** (`/`, `/kontakt/`, `/media/` +2
podstrony, `/newsletter/`, `/polityka-prywatnosci/`, `/szkola-meskich-grup/`,
`/lowen-for-men-advanced/`) + formularze (`formularz.php`, patrz wyżej) +
trzy przekierowania w `.htaccess`. Weryfikacja programowa (tagi, obrazy,
martwe linki) czysta.
