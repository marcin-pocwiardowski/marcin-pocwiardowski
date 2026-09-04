<?php
/**
 * MAILCHIMP — dopisywanie zapisów z newslettera do listy wysyłkowej.
 *
 * Wzorowane na lowenformen-pl/poczekalnia.php — ten sam mechanizm, ta sama
 * audience („Newsletter Marcina Poćwiardowskiego", jedyna), ten sam klucz API.
 * Różnica: tam wybór poczekalni zależał od pola „temat" w formularzu, tu jest
 * jeden przypadek (newsletter), więc mapa list nie jest potrzebna.
 *
 * ⚠️ Świadomie NIE dotyczy formularzy zapisu na warsztaty. Zapis na płatny
 * warsztat nie jest zgodą na newsletter — dopisanie takiej osoby do listy
 * wysyłkowej bez pytania byłoby naruszeniem RODO, a nie ułatwieniem.
 *
 * Double opt-in: kontakt trafia ze statusem 'pending', więc Mailchimp sam
 * wysyła prośbę o potwierdzenie i sam zapisuje datę oraz IP zgody. Istniejącego,
 * potwierdzonego subskrybenta to NIE cofa do „pending".
 *
 * Konfiguracja: mailchimp-config.php (poza gitem, wgrywany ręcznie na serwer,
 * wykluczony z deployu). Wzór: mailchimp-config.example.php. Klucz i ID są
 * te same, co dla lowenformen.pl — wystarczy skopiować tamten plik.
 *
 * Awaria Mailchimpa nigdy nie blokuje formularza: funkcja zwraca tablicę
 * ['ok' => bool, 'info' => string] i nie rzuca wyjątkiem. Zgłoszenie i tak
 * idzie mailem do Marcina, z dopiskiem, czy dopisanie się udało.
 */

/** Tagi nadawane każdemu zapisowi z tej strony. Źródło, nie cel — sama
 *  audience to już „newsletter", więc powtarzanie tego w tagu nic nie wnosi. */
function mc_tagi_newsletter() {
    return ['www-marcinpocwiardowski'];
}

/**
 * Dopisuje kontakt do Mailchimpa.
 * @return array ['ok' => bool, 'info' => string]
 */
function mc_zapisz($email, $imie = '') {
    $configPath = __DIR__ . '/mailchimp-config.php';
    if (!file_exists($configPath)) {
        return ['ok' => false, 'info' => 'Brak mailchimp-config.php na serwerze — kontakt NIE trafił do Mailchimpa.'];
    }
    require_once $configPath;

    if (!defined('MC_KLUCZ') || !defined('MC_AUDIENCE') || !MC_KLUCZ || !MC_AUDIENCE) {
        return ['ok' => false, 'info' => 'mailchimp-config.php jest niekompletny.'];
    }
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'info' => 'Brak cURL na serwerze.'];
    }

    // Data center to końcówka klucza API, np. "...-us14"
    $mysznik = strrchr(MC_KLUCZ, '-');
    if ($mysznik === false || strlen($mysznik) < 2) {
        return ['ok' => false, 'info' => 'Klucz API nie zawiera oznaczenia data center.'];
    }
    $dc = substr($mysznik, 1);

    $hash = md5(strtolower(trim($email)));
    $baza = 'https://' . $dc . '.api.mailchimp.com/3.0/lists/' . MC_AUDIENCE . '/members/' . $hash;

    // Krok 1: dopisz kontakt (upsert), double opt-in dla nowych.
    $dane = ['email_address' => $email, 'status_if_new' => 'pending'];
    if ($imie !== '') {
        $dane['merge_fields'] = ['FNAME' => $imie];
    }
    $odp = mc_http('PUT', $baza, $dane);
    if (!$odp['ok']) {
        return ['ok' => false, 'info' => 'Mailchimp odrzucił zapis: ' . $odp['info']];
    }

    // Krok 2: tagi (osobny endpoint — przy PUT się ich nie ustawi).
    $tagi = [];
    foreach (mc_tagi_newsletter() as $t) {
        $tagi[] = ['name' => $t, 'status' => 'active'];
    }
    $odpTagi = mc_http('POST', $baza . '/tags', ['tags' => $tagi]);
    if (!$odpTagi['ok']) {
        return ['ok' => false, 'info' => 'Kontakt dopisany, ale tagi się nie nadały: ' . $odpTagi['info']];
    }

    return ['ok' => true, 'info' => 'Dopisany do Mailchimpa, tagi: ' . implode(', ', mc_tagi_newsletter()) . '. Czeka na potwierdzenie (double opt-in).'];
}

/** Pojedyncze zapytanie do API Mailchimpa. */
function mc_http($metoda, $url, $dane) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => $metoda,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_USERPWD        => 'marcinpocwiardowski:' . MC_KLUCZ,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
        CURLOPT_POSTFIELDS     => json_encode($dane, JSON_UNESCAPED_UNICODE),
    ]);
    $body = curl_exec($ch);
    $kod  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $blad = curl_error($ch);
    curl_close($ch);

    if ($body === false) {
        return ['ok' => false, 'info' => 'cURL: ' . $blad];
    }
    if ($kod >= 200 && $kod < 300) {
        return ['ok' => true, 'info' => 'HTTP ' . $kod];
    }

    $detal = '';
    $json = json_decode($body, true);
    if (is_array($json) && isset($json['detail'])) {
        $detal = ' — ' . $json['detail'];
    }
    return ['ok' => false, 'info' => 'HTTP ' . $kod . $detal];
}
