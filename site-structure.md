# Struktura nowej strony marcinpocwiardowski.com

Ustalona 2026-07-24 na podstawie audytu treści (`content-audit.md`).

1. **Strona główna** — skrócone intro, CTA (Umów się), 2-3 nadchodzące wydarzenia (bez powielania sekcji na każdej podstronie)
2. **Kim jestem** — pełne bio wydzielone z homepage (`kim-jestem.md`)
3. **Oferta**
   - LOWEN for MEN (flagowy warsztat) — link do lowenformen.pl (osobna marka, zostaje)
   - LOWEN for MEN Advanced
   - Warsztat dla kobiet (poprawić opinie — obecnie od mężczyzn)
   - Trening terapeutyczny
   - Sesje indywidualne / ISTDP (dziś tylko wzmianka na homepage — potrzebuje własnej sekcji/podstrony)
4. **Wywiady / Media**
5. **Blog** — nowy, zostaje na WordPressie w podkatalogu `/blog` (patrz niżej)
6. **Newsletter**
7. **Kontakt** (wchłania dzisiejszą `/sesje/`, która jest niemal pusta)
8. **Szkoła Męskich Grup** — archiwum, bez CTA do zapisów (nieaktywna od 2025)
9. Stopka: Polityka prywatności

## Decyzje
- lowenformen.pl pozostaje osobną domeną/marką — link zewnętrzny w menu, tak jak dziś.
- Szkoła Męskich Grup: archiwum jako dowód doświadczenia, nie usuwamy.
- Do ustalenia później: /sklep/, /koszyk/, /products/ (WooCommerce) — sprawdzić czy używane.

## Blog
Decyzja (2026-07-24): blog zostaje na WordPressie, reszta strony przechodzi
na statyczny HTML. Adres: **podkatalog `/blog`** (nie subdomena) — Google
traktuje treść w podkatalogu jako część autorytetu głównej domeny, co przy
mniejszej witrynie ma znaczenie. Subdomena była rozważana jako opcja prostsza
technicznie, ale tylko gdyby statyczna strona lądowała na hostingu bez PHP —
nie nasz przypadek.

**Aktualizacja (2026-07-24):** zamiast przenosić starą instalację WP (wiele
lat, nadmiar wtyczek), robimy **świeży, czysty install WordPressa w `/blog`**.
Stara instalacja nie jest migrowana — jest tylko źródłem materiałów do
wyciągnięcia. Bloga i tak jeszcze nie ma (brak wpisów w sitemapie), więc nie
tracimy żadnej zaindeksowanej treści blogowej.

**Aktualizacja (2026-07-24) — obrazki:** decyzja to celowy świeży start, NIE
przenosimy `wp-content/uploads/` ze starej instalacji. Powód: w starym media
managerze zaindeksowały się w Google wszystkie zdjęcia z katalogu media,
których Marcin nie chciał mieć publicznie widocznych. To nie jest coś do
naprawienia przekierowaniem — to efekt, którego chcemy się pozbyć.

Konsekwencje i plan:

1. Stare adresy `https://marcinpocwiardowski.com/wp-content/uploads/...`
   przestaną istnieć (404) — to jest zamierzone, nie błąd migracji.
2. Ponieważ część z nich jest już zaindeksowana w Google Grafika wbrew
   intencji, samo 404 wystarczy w końcu do wypadnięcia z indeksu, ale to
   może potrwać tygodnie/miesiące. Szybciej: użyć **Google Search Console →
   Usuwanie → nowa prośba o usunięcie** z prefiksem
   `marcinpocwiardowski.com/wp-content/uploads/` (działa też dla wyszukiwania
   grafiki) — usuwa z widoczności na ok. 6 miesięcy praktycznie od razu,
   docelowo znika trwale gdy Google odkryje 404.
3. Przy nowym, czystym WordPressie w `/blog` i przy budowie statycznej
   strony: świadomie decydować, co trafia do publicznie dostępnego folderu
   mediów. Domyślnie WordPress robi każdy upload publicznym plikiem pod
   przewidywalnym adresem (`/blog/wp-content/uploads/RRRR/MM/...`) —
   niezależnie od ustawień "noindex" na stronie załącznika, sam plik
   zawsze ma adres URL. Jeśli mają być zdjęcia nie do znalezienia w
   wyszukiwarce: (a) dodać `Disallow: /wp-content/uploads/` /
   `/blog/wp-content/uploads/` w `robots.txt` dla folderów, których nie
   chcemy widzieć w indeksie, i/lub (b) po prostu nie wgrywać tam zdjęć,
   które mają zostać prywatne — trzymać je poza publicznym katalogiem
   mediów (np. wysyłane bezpośrednio, nie przez media library).
4. Zainstalować świeży WordPress w `/blog` (nowa baza, nowy prefiks tabel,
   tylko potrzebne wtyczki — patrz niżej).
5. Minimalny zestaw wtyczek do rozważenia (do potwierdzenia): SEO (Yoast lub
   lżejsza alternatywa), cache/wydajność, backup, podstawowe bezpieczeństwo.
   Rezygnujemy z reszty starych wtyczek (WooCommerce, WPForms itd. — te
   funkcje przenosimy do statycznej strony albo do zewnętrznych narzędzi,
   np. formularz kontaktowy, cal.com).
6. Stare strony WP (Kontakt, Media, warsztaty itd.) **nie są migrowane w
   żadnej formie** — ich treść i tak żyje teraz jako statyczny HTML w
   katalogu głównym. Do nowych stron statycznych potrzebne będą **nowe
   zdjęcia** (albo świadomie wybrany, mały zestaw ponownie wgranych ze
   starych, jeśli któreś mają zostać) zamiast hurtowego przenoszenia
   całego starego katalogu uploads.
7. Plik statyczny `index.html` (i reszta strony) ląduje w katalogu głównym
   hostingu, tam gdzie dziś jest `index.php` starego WordPressa.
8. Root `.htaccess` (ten w repo) ma celowo wąskie reguły — nie koliduje z
   własnym `/blog/.htaccess`, który wygeneruje świeży WordPress (Apache
   scala reguły katalogów niezależnie).
9. Link "Blog" dodany do głównego menu (`partials/nav.html`) → `/blog/`.

## Następne kroki
- Napisać treść `kim-jestem.md`
- Zdecydować o `geo.md` (strategia GEO / widoczność w AI)
- Zbudować szkielet HTML wg powyższej struktury do podglądu lokalnego
- Złożyć w Google Search Console prośbę o usunięcie starych
  `/wp-content/uploads/` z wyników (Grafika + Web)
- Wybrać/przygotować nowe zdjęcia do statycznych stron (nie przenosić
  hurtowo starego katalogu uploads)
- Zainstalować świeży WordPress w `/blog`, ustalić finalną minimalną listę
  wtyczek, ustawić `robots.txt` dla folderów mediów które mają zostać
  niepubliczne
