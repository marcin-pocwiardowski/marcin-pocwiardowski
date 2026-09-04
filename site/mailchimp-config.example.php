<?php
/**
 * WZÓR konfiguracji Mailchimpa dla marcinpocwiardowski.com.
 *
 * NAJPROSTSZA DROGA: to ten sam Mailchimp i ta sama audience, co dla
 * lowenformen.pl — skopiuj gotowy plik z serwera zamiast szukać wartości
 * w panelu:
 *
 *   cp domains/lowenformen.pl/public_html/mailchimp-config.php \
 *      domains/marcinpocwiardowski.com/public_html/mailchimp-config.php
 *
 * Ręcznie, gdyby trzeba było od zera:
 * 1. Skopiuj ten plik jako  mailchimp-config.php
 * 2. Wpisz wartości (skąd je wziąć — niżej)
 * 3. Wgraj mailchimp-config.php na serwer, do katalogu z formularz.php
 *
 * WAŻNE: mailchimp-config.php jest w .gitignore ORAZ w EXCLUDE deployu.
 * Wgrywasz go raz, ręcznie. Deploy go nie nadpisze ani nie skasuje,
 * a klucz API nie trafia na GitHub.
 */

// Klucz API — Mailchimp: Profile > Extras > API keys > Create A Key.
// Kończy się myślnikiem i oznaczeniem data center, np. "-us10".
// To oznaczenie jest odczytywane automatycznie, nie usuwaj go.
define('MC_KLUCZ', 'TUTAJ-KLUCZ-API-Z-PANELU-MAILCHIMPA');

// ID audience — Mailchimp: Audience > All contacts > Settings >
// Audience name and defaults > pole "Audience ID". Ciąg ~10 znaków.
define('MC_AUDIENCE', 'TUTAJ-ID-AUDIENCE');
