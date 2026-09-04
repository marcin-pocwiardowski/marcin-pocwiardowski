# Świeży WordPress w `/blog` — runbook

**Ustalono:** 24 VII 2026 (`site-structure.md`) · **Runbook napisany:** 11 VIII 2026

Wszystko poniżej klikasz Ty w panelu Zenboxa — nie mam do niego dostępu.
Kolejność ma znaczenie: kroki 1–7 wykonuje się **przy starym WordPressie wciąż
działającym na produkcji**, i to jest bezpieczne. Podmiana katalogu głównego na
statyczny HTML to osobna operacja (krok 12), robiona dopiero gdy `/blog` już stoi.

---

## Dlaczego to jest bezpieczne przy żywej stronie

Stary WordPress w katalogu głównym ma w `.htaccess` reguły:

```
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
```

„Przepisuj tylko wtedy, gdy taki plik ani katalog **nie istnieje**". W momencie,
w którym `/blog/` staje się prawdziwym katalogiem, root przestaje go przechwytywać
i oddaje obsługę temu, co jest w środku. **Dwie instalacje WordPressa mogą stać
obok siebie**, pod warunkiem że mają osobne bazy i osobne prefiksy tabel — dlatego
krok 2 i krok 5 są nienegocjowalne.

---

## Przed startem — trzy rzeczy do sprawdzenia

| Co | Gdzie | Po co |
|---|---|---|
| Ścieżka katalogu domeny | panel → Domeny. Prawdopodobnie `/home/technest/domains/marcinpocwiardowski.com/public_html` — **potwierdź**, bo w `OPERACJE.md` mamy zapisany tylko odpowiednik dla `lowenformen.pl` | Bez tego reszta kroków celuje w niewłaściwe miejsce |
| Wersja PHP | panel → PHP | WP 6.x chce **8.1+**. Stary WP może siedzieć na 7.4 — jeśli tak, PHP ustawia się per katalog albo poczekaj z podbiciem do kroku 12 |
| Czy serwer to LiteSpeed | panel → informacje o serwerze, albo nagłówek odpowiedzi `Server:` | Decyduje o wtyczce cache w kroku 9 |

---

## Krok 1 — pełny backup starej instalacji

Zanim cokolwiek. Panel → Kopie zapasowe → kopia na żądanie, **pliki + baza**.
Nie po to, żeby migrować (nie migrujemy), tylko po to, żeby dało się cofnąć
i żeby stare treści zostały jako źródło do wyciągania materiału.

Osobno pobierz na dysk eksport wpisów starego WP:
**Narzędzia → Eksport → Wszystkie treści** → plik XML. To jedyna rzecz ze starej
instalacji, która ma jeszcze wartość.

---

## Krok 2 — nowa baza danych

Panel → Bazy danych → Utwórz.

- **Nazwa bazy:** np. `technest_blog2026`
- **Nowy użytkownik**, wyłącznie do tej bazy — nie ten od starego WP
- **Hasło:** wygenerowane w 1Password, 32 znaki, zapisane od razu jako pozycja „WP blog — baza"

> Współdzielenie bazy między instalacjami jest technicznie możliwe (różne
> prefiksy) i jest złym pomysłem: skasowanie starego WP wtedy zabiera bloga.

---

## Krok 3 — katalog `/blog`

Menedżer plików → w katalogu domeny utwórz `blog`. Uprawnienia `755`.

---

## Krok 4 — wgranie WordPressa

Pobierz `https://pl.wordpress.org/latest-pl_PL.zip`, wgraj do `/blog`, rozpakuj.
Po rozpakowaniu pliki bywają w zagnieżdżonym `blog/wordpress/` — **przenieś zawartość
poziom wyżej**, tak żeby `wp-admin`, `wp-content`, `wp-includes` leżały bezpośrednio
w `/blog`. Pusty `wordpress/` skasuj.

---

## Krok 5 — instalator

Wejdź na `https://marcinpocwiardowski.com/blog/`.

| Pole | Wartość |
|---|---|
| Nazwa bazy / użytkownik / hasło | z kroku 2 |
| Serwer bazy | `localhost` (Zenbox) |
| **Prefiks tabel** | **NIE `wp_`.** Ustaw np. `mpb7q_` |
| Tytuł witryny | Blog — Marcin Poćwiardowski |
| **Nazwa użytkownika** | **NIE `admin`, NIE `marcin`.** Coś nieoczywistego |
| Hasło | wygenerowane, do 1Password |
| E-mail | `marcin@techne.pl` |
| „Proś wyszukiwarki o nieindeksowanie" | **zaznacz teraz**, odznaczysz w kroku 11 |

Prefiks tabel i nazwa administratora to nie paranoja — to dwa najczęstsze
założenia botów skanujących WordPressy. Nie zatrzymają ukierunkowanego ataku,
zatrzymują ruch masowy, którego jest 99%.

---

## Krok 6 — hardening `wp-config.php`

Edytuj `/blog/wp-config.php`. Wklej **przed** linią `/* That's all, stop editing! */`
zawartość pliku `wp-config-dodatki.php` z tego repo (obok tego runbooka).

Sprawdź przy okazji, czy sekcja `AUTH_KEY … NONCE_SALT` ma prawdziwe losowe wartości
(instalator zwykle wypełnia je sam). Jeśli są puste albo wyglądają na wzorcowe,
wygeneruj świeże na `https://api.wordpress.org/secret-key/1.1/salt/` i podmień.

---

## Krok 7 — sprzątanie domyślnej instalacji

- **Wtyczki:** usuń Akismet i Hello Dolly. Zostaje pusto.
- **Motywy:** zostaw **jeden** (Twenty Twenty-Five), resztę usuń. Nieaktywny motyw
  też dostaje aktualizacje bezpieczeństwa i też bywa wektorem.
- **Treści:** usuń wpis „Witaj świecie!", stronę „Przykładowa strona", komentarz przykładowy.
- **Ustawienia → Dyskusja:** odznacz „Zezwalaj na komentarze" — chyba że świadomie
  chcesz je moderować. Komentarze na nowym blogu to głównie spam do obsługi.
- **Ustawienia → Media:** odznacz „Porządkuj pliki w katalogach miesięcy i lat", jeśli
  wolisz płaską strukturę. Bez znaczenia funkcjonalnie, ułatwia panowanie nad tym,
  co leży publicznie.

---

## Krok 8 — permalinki

**Ustawienia → Bezpośrednie odnośniki → Nazwa wpisu.**
Adresy wyjdą jako `marcinpocwiardowski.com/blog/tytul-wpisu/`.

WordPress wygeneruje przy tym `/blog/.htaccess`. **Nie ruszaj go i nie kopiuj jego
reguł do roota** — Apache scala reguły katalogów niezależnie, a root `.htaccess`
w tym repo jest celowo wąski (dotyczy wyłącznie `sesje`), właśnie po to, żeby
niczego z `/blog/*` nie przechwycił.

---

## Krok 9 — wtyczki, minimalny zestaw

Cztery funkcje, nie cztery wtyczki „na wszelki wypadek". Każda kolejna to kolejna
powierzchnia ataku i kolejna rzecz do aktualizowania — a aktualizowanie jest tym,
o czym się zapomina.

| Funkcja | Rekomendacja | Uwaga |
|---|---|---|
| **SEO** | **Slim SEO** albo **SEOPress (free)** | Yoast robi znacznie więcej, niż potrzebuje blog na kilkanaście wpisów, i dokłada panele wszędzie. Jeśli wolisz znane narzędzie — Yoast też jest OK, to nie jest błąd |
| **Backup** | **UpdraftPlus (free)** | Ustaw harmonogram: pliki tygodniowo, baza codziennie, docelowo na zewnętrzny dysk/chmurę. Kopia leżąca na tym samym serwerze co strona nie jest kopią |
| **Bezpieczeństwo** | **Limit Login Attempts Reloaded** | Lekka, robi jedną rzecz. Pełne pakiety (Wordfence, Solid) są ciężkie i gadatliwe mailowo — przy blogu bez sklepu to nadmiar |
| **Cache** | **LiteSpeed Cache** — tylko jeśli serwer to LiteSpeed | Jeśli nie: **pomiń**. Blog o niskim ruchu na przyzwoitym hostingu nie potrzebuje cache, a źle skonfigurowany cache generuje więcej zgłoszeń „strona pokazuje stare rzeczy" niż oszczędza czasu |

**Ustawienia → Ogólne: włącz automatyczne aktualizacje** rdzenia (minor i major)
oraz wtyczek. Przy tak małym zestawie ryzyko, że aktualizacja coś zepsuje, jest
mniejsze niż ryzyko, że niezaktualizowana wtyczka wpuści kogoś do środka.

---

## Krok 10 — `robots.txt` i sprawa starych zdjęć

To jest ten krok, dla którego cała ta decyzja została podjęta.

WordPress serwuje **wirtualny** `robots.txt` tylko wtedy, gdy w katalogu głównym
domeny nie ma fizycznego pliku. Po podmianie roota na statyczny HTML **fizyczny
plik będzie** — i to on obowiązuje dla całej domeny, razem z `/blog`.
Plik `/blog/robots.txt` nie jest przez roboty w ogóle czytany.

Wgraj do katalogu głównego domeny plik `robots.txt` z tego repo.

**Osobno i niezależnie od robots.txt** — stare adresy
`marcinpocwiardowski.com/wp-content/uploads/...` mają zacząć zwracać 404 i to jest
zamierzone. Żeby przyspieszyć wypadnięcie z Google Grafiki:

> Google Search Console → **Usuwanie** → *Nowa prośba* → **Usuń wszystkie adresy URL
> z tym prefiksem** → `marcinpocwiardowski.com/wp-content/uploads/`

Ukrywa z wyników praktycznie od razu, na około 6 miesięcy; trwałe usunięcie
następuje, gdy Google odkryje 404. Zrób to **po** kroku 12, nie przed — dopóki
stary WP żyje, te adresy jeszcze odpowiadają 200.

⚠️ **`Disallow` w robots.txt nie usuwa z indeksu.** Blokuje odwiedziny robota, a już
zaindeksowany zasób potrafi w wynikach zostać. Kolejność, która działa, to:
najpierw 404 (czyli fizyczne zniknięcie plików) + prośba o usunięcie w GSC,
a `Disallow` dopiero jako zabezpieczenie na przyszłość dla **nowego** katalogu
`/blog/wp-content/uploads/`.

---

## Krok 11 — wygląd bloga

To jest punkt, w którym blog albo staje się częścią strony, albo zostaje
doklejonym WordPressem w innym kroju pisma. Do zrobienia po akceptacji prototypu
strony głównej:

1. **Motyw potomny** Twenty Twenty-Five (`/blog/wp-content/themes/mp-blog/`),
   ze `style.css` importującym tokeny z `assets/style.css` strony statycznej:
   Fraunces + Inter, ta sama paleta (`--clay`, `--paper`, `--ink`), te same hairline'y.
2. **Nagłówek i stopka** bloga wizualnie identyczne z resztą serwisu — to samo
   menu, ten sam przycisk „Umów się". Utrzymywane ręcznie w dwóch miejscach; przy
   siedmiu pozycjach menu to akceptowalny koszt.
3. Odznacz **„Proś wyszukiwarki o nieindeksowanie"** (Ustawienia → Czytanie) —
   dopiero teraz, gdy blog wygląda jak trzeba.

Zrobię ten motyw, kiedy design strony głównej będzie zatwierdzony.

---

## Krok 12 — podmiana katalogu głównego na statyczny HTML

Osobna operacja, po tym jak `/blog` działa i wygląda.

1. Zrób backup (znowu — inny stan niż w kroku 1).
2. Wgraj statyczne pliki do katalogu głównego domeny.
3. Usuń z katalogu głównego: `index.php`, `wp-admin/`, `wp-includes/`,
   `wp-content/`, `wp-*.php`, `xmlrpc.php`, stary `.htaccess`.
   **Nie dotykaj `/blog/`.**
4. Wgraj `.htaccess` z tego repo jako główny.
5. Sprawdź, że `DirectoryIndex` podaje `index.html` — po usunięciu `index.php`
   zwykle działa domyślnie, ale przy dziwnej konfiguracji dopisz w `.htaccess`:
   `DirectoryIndex index.html index.php`.
6. Bazę starego WP **zostaw na miesiąc**, dopiero potem usuń.

---

## Weryfikacja — lista do odhaczenia

- [ ] `marcinpocwiardowski.com/blog/` otwiera się i wygląda jak reszta strony
- [ ] Pojedynczy wpis ma ładny adres `/blog/tytul/`, nie `?p=123`
- [ ] `marcinpocwiardowski.com/blog/wp-admin/` loguje bez błędów
- [ ] `marcinpocwiardowski.com/robots.txt` zwraca **nowy** plik (nie wirtualny WP)
- [ ] `marcinpocwiardowski.com/wp-content/uploads/cokolwiek.jpg` → **404**
- [ ] Stare adresy podstron (`/warsztat-dla-kobiet/` itd.) → **200**, nie 404
- [ ] `/sesje/` → **301** na `/kontakt/`
- [ ] Panel bazy: dwie osobne bazy, prefiks bloga **nie** `wp_`
- [ ] UpdraftPlus wykonał pierwszą kopię i widać ją poza serwerem
- [ ] Prośba o usunięcie `/wp-content/uploads/` złożona w Search Console
- [ ] Sitemapa z wtyczki SEO zgłoszona w Search Console

---

## Czego świadomie nie robimy

| Nie robimy | Dlaczego |
|---|---|
| Migracji starej instalacji | Lata wtyczek, WooCommerce, WPForms. Czysty start jest tańszy niż sprzątanie |
| Przeniesienia `wp-content/uploads/` | **To jest sedno decyzji z 24 VII** — w starym media managerze zaindeksowały się zdjęcia, które nie miały być publiczne. Przeniesienie ich odtwarza dokładnie ten problem |
| Bloga na subdomenie | Treść w podkatalogu buduje autorytet głównej domeny. Subdomena miała sens tylko, gdyby statyczna strona lądowała na hostingu bez PHP — nie nasz przypadek |
| Przeniesienia stron WP (Kontakt, Media, warsztaty) | Ich treść żyje już jako statyczny HTML w katalogu głównym |
| WooCommerce, formularzy WP | Formularz kontaktowy i zapisy idą przez cal.com i Mailchimpa — poza WordPressem |

---

## Otwarte, do decyzji przy okazji

- `/sklep/`, `/koszyk/`, `/products/` w starej sitemapie — sprawdzić, czy cokolwiek
  z tego było używane, zanim zniknie razem ze starym WP.
- Czy blog ma wysyłać powiadomienia do newslettera (Mailchimp RSS-to-email), czy
  wpisy anonsujesz ręcznie. Automat jest wygodny, ale wysyła **każdy** wpis.
