# Audyt treści – marcinpocwiardowski.com (obecna wersja WordPress)

Zebrane z sitemapy i stron na żywo, 2026-07-24.

> 🔴 **Ostrzeżenie dopisane 11 VIII 2026 — ten plik jest zapisem tego, co MÓWI stara
> strona, nie zapisem faktów.** Co najmniej jedna liczba na starej stronie była
> nieprawdziwa i została stąd bezrefleksyjnie przepisana do nowego prototypu:
> **„15 lat pracy indywidualnej"**. Marcin (11 VIII): *„pracuję terapeutycznie od 2009 r.,
> warsztaty prowadzę od 2015"* — czyli **17 i 11 lat**, nie 15.
>
> **Nie przepisuj stąd liczb, dat ani kwalifikacji bez potwierdzenia u Marcina.**
> Twarde punkty odniesienia, potwierdzone: **praca terapeutyczna od 2009, warsztaty od 2015.**

## Strona główna (/)
- H1: Marcin Poćwiardowski / "Pomagam zmieniać życia"
- Bio: psycholog, psychoterapeuta, trener, mentor. Analiza Bioenergetyczna (Lowen) + ISTDP. 15 lat pracy indywidualnej, setki godzin terapii własnej/superwizji.
- Praca z ciałem metodą Lowena (opis).
- ISTDP online indywidualnie (opis).
- LOWEN for MEN – warsztaty rozwojowe dla mężczyzn.
- Szkoła Męskich Grup – kształci prowadzących grupy męskie.
- CTA: "Umów się" → cal.com/marcinpocwiardowski
- Sekcja "Nadchodzące wydarzenia" (powtarza się na każdej podstronie)
- Osadzony kalendarz Luma, 2 filmy YouTube

## /sesje/
Prawie pusta – sam tytuł + duplikat "Nadchodzące wydarzenia" + filmy. Brak unikalnej treści.

## /kontakt/
CTA do cal.com + duplikat sekcji wydarzeń. Brak danych kontaktowych (telefon/adres) poza mailem widocznym gdzie indziej (marcin@techne.pl).

## /media/ (w menu: "Wywiady")
- Podcast u Wojciecha Herry (Herra On Line)
- Konferencja PO MĘSKU w Łodzi
- Wywiad w książce "Sztuka męskości"
- Kilka dalszych filmów YouTube
- Linki do 2 artykułów: "Dla mężczyzny ciało jest bardzo ważne", "Zachować pogodę ducha"

## /szkola-meskich-grup/
Historyczne info: 2 edycje rocznego szkolenia dla prowadzących grupy męskie (2022–2023, 2023–2025). Obecnie nieaktywna/zakończona – brak wezwania do zapisu.

## /trening-terapeutyczny/
Trening w małej grupie (8 osób) dla osób z doświadczeniem grupy lowenowskiej. Opis formuły. Zapisy: marcin@techne.pl. Kolejna edycja: jesień 2026.

## /warsztat-dla-kobiet/
Pierwszy warsztat dla kobiet (dotąd tylko mężczyźni). Pełny opis oferty, korzyści, opinie uczestników (obecnie tylko męskie cytaty – niespójność), termin 18–20.09.2026, Warszawa, 1500 zł. Zapisy mailowo.

## /lowen-for-men-advanced/
Warsztat pogłębiający, wyłącznie dla absolwentów LOWEN for MEN. Termin 13–18.04.2027, Podlasie. Koszty rozbite (pobyt 1500 zł + warsztat 3100 zł). Formularz zapisu (JS).

## /lowen-for-men-moj-meski-rod-i-ja/
Flagowy warsztat męski – długi tekst sprzedażowy o relacji z męskim rodem/ojcem, opis metody, korzyści. Dwa terminy 2026 (czerwiec, wrzesień), Warszawa, ceny 1990/1900–2300 zł. Formularz zapisu. Bio prowadzącego (54 lata, 20 lat pracy z mężczyznami, 9 lat LOWEN for MEN).

## /newsletter/
Tylko pole email + przycisk zapisu. Brak opisu wartości newslettera.

## Inne strony w sitemapie
- /sklep/, /koszyk/, /products/ – WooCommerce, prawdopodobnie nieaktywne/do weryfikacji
- /polityka-prywatnosci/ – standardowa polityka prywatności

## Zewnętrzne
- lowenformen.pl – osobna domena/strona dla marki LOWEN for MEN, linkowana jako pierwsza pozycja w menu głównym

## Obserwacje
1. Sekcja "Nadchodzące wydarzenia" + 2 filmy YouTube powtarzają się identycznie na każdej podstronie (sztywny fragment szablonu) – warto to zrobić jako współdzielony komponent, nie kopiować treści.
2. /sesje/ i /kontakt/ mają niemal identyczną, szczątkową treść – kandydaci do połączenia.
3. Brak osobnej strony "Kim jestem" – bio jest wtopione w stronę główną (stąd puste kim-jestem.md w repo – do wydzielenia).
4. Oferta warsztatowa rozjechana po 4 osobnych stronach bez wspólnego rodzica ("Oferta"/"Warsztaty").
5. Opinie na /warsztat-dla-kobiet/ pochodzą od mężczyzn – niespójność do poprawienia w nowej treści.
6. Relacja z lowenformen.pl (osobna domena) do ustalenia – czy to ma być submarka, czy scalamy.
