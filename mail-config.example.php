<?php
/**
 * Szablon konfiguracji SMTP dla formularzy marcinpocwiardowski.com.
 *
 * NIE edytuj tego pliku danymi produkcyjnymi — to wersja wzorcowa, trafia do gita.
 * Skopiuj go jako `mail-config.php` (ten plik jest w .gitignore, więc hasło
 * nigdy nie trafi do repozytorium) i uzupełnij prawdziwymi danymi.
 *
 * Skąd wziąć dane: panel Zenbox → Poczta → skrzynka na tej domenie.
 * Dziś istnieje tylko alias kurs@marcinpocwiardowski.com (przekierowanie,
 * nie skrzynka) — SMTP wymaga prawdziwej skrzynki z hasłem, np.
 * kontakt@marcinpocwiardowski.com. Trzeba ją założyć w panelu, zanim
 * formularze zaczną realnie wysyłać.
 */

define('SMTP_HOST',     'smtp.zenbox.pl');
define('SMTP_PORT',     587);
define('SMTP_USERNAME', 'kontakt@marcinpocwiardowski.com'); // realna skrzynka, nie alias
define('SMTP_PASSWORD', 'WKLEJ_HASLO_TUTAJ');
define('MAIL_FROM',     'kontakt@marcinpocwiardowski.com');
define('MAIL_TO',       'marcin@techne.pl');
