<?php
/**
 * Wspólna końcówka dla trzech formularzy na marcinpocwiardowski.com:
 * newsletter, zapis LOWEN for MEN, zapis LOWEN for MEN Advanced.
 * Wzorowane na kontakt.php z lowenformen.pl (PHPMailer + SMTP Zenbox),
 * ale dane logowania trzymane osobno w mail-config.php (poza gitem).
 */

header('Content-Type: application/json; charset=utf-8');

if (!file_exists(__DIR__ . '/mail-config.php')) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Formularz nieskonfigurowany (brak mail-config.php).']);
    exit;
}
require_once __DIR__ . '/mail-config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Niedozwolona metoda.']);
    exit;
}

// Honeypot — pole "www" musi zostać puste (ukryte w CSS, boty je wypełniają)
if (!empty($_POST['www'] ?? '')) {
    echo json_encode(['ok' => true]); // udajemy sukces, nic nie wysyłamy
    exit;
}

$formularz = trim($_POST['formularz'] ?? ''); // 'newsletter' | 'l4m' | 'l4m-advanced'
$email     = trim($_POST['email']     ?? '');
$imie      = trim($_POST['imie']      ?? '');
$termin    = trim($_POST['termin']    ?? '');
$wiadomosc = trim($_POST['wiadomosc'] ?? '');

if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'error' => 'Podaj poprawny adres e-mail.']);
    exit;
}

$etykiety = [
    'newsletter'    => 'Zapis na newsletter',
    'l4m'           => 'Zapis: LOWEN for MEN — Mój męski ród i ja',
    'l4m-advanced'  => 'Zapis: LOWEN for MEN Advanced',
];
$etykieta = $etykiety[$formularz] ?? ('Formularz: ' . htmlspecialchars($formularz));

// ── Mailchimp — TYLKO newsletter ───────────────────────────────────────
// Zapis na płatny warsztat nie jest zgodą na newsletter, więc formularze
// warsztatowe świadomie tu nie wchodzą (szczegóły: mailchimp.php).
// Awaria Mailchimpa nie może zablokować zgłoszenia — dlatego wynik tylko
// dopisujemy do maila, a nie przerywamy nim wysyłki.
$mc = null;
if ($formularz === 'newsletter') {
    require_once __DIR__ . '/mailchimp.php';
    $mc = mc_zapisz($email, $imie);
}

require_once __DIR__ . '/phpmailer/Exception.php';
require_once __DIR__ . '/phpmailer/PHPMailer.php';
require_once __DIR__ . '/phpmailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USERNAME;
    $mail->Password   = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = SMTP_PORT;
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom(MAIL_FROM, 'marcinpocwiardowski.com');
    $mail->addAddress(MAIL_TO, 'Marcin Poćwiardowski');
    if ($imie) {
        $mail->addReplyTo($email, $imie);
    } else {
        $mail->addReplyTo($email);
    }

    // Prefiks w temacie mówi od razu, czy trzeba coś zrobić ręcznie —
    // ten sam zwyczaj co „POCZEKALNIA OK:" / „POCZEKALNIA — RĘCZNIE:"
    // w lowenformen-pl/kontakt.php.
    $prefiks = '';
    if ($mc !== null) {
        $prefiks = $mc['ok'] ? 'MAILCHIMP OK: ' : 'MAILCHIMP — RĘCZNIE: ';
    }
    $mail->Subject = '[marcinpocwiardowski.com] ' . $prefiks . $etykieta . ($imie ? ' — ' . $imie : '');

    $body = "Formularz: $etykieta\n";
    if ($imie)      $body .= "Imię: $imie\n";
    $body .= "E-mail: $email\n";
    if ($termin)    $body .= "Wybrany termin: $termin\n";
    if ($wiadomosc) $body .= "\nWiadomość:\n$wiadomosc\n";
    if ($mc !== null) {
        $body .= "\nMailchimp: " . ($mc['ok'] ? 'OK' : 'NIE UDAŁO SIĘ') . " — " . $mc['info'] . "\n";
    }

    $mail->Body = $body;

    $mail->send();
    echo json_encode(['ok' => true]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Błąd wysyłki. Spróbuj jeszcze raz lub napisz bezpośrednio na marcin@techne.pl.']);
}
