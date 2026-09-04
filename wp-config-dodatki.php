<?php
/**
 * Wklej TĘ ZAWARTOŚĆ (bez pierwszej linii <?php) do /blog/wp-config.php,
 * PRZED linią zaczynającą się od: "That's all, stop editing!"
 *
 * Ten plik nie jest wdrażany nigdzie sam z siebie — to ściągawka do runbooka
 * BLOG-WORDPRESS.md, krok 6.
 */

/* --- Blokada edytora plików w panelu ---------------------------------
   Najczęstsza droga od „ktoś zdobył hasło do wp-admina" do „ktoś ma
   dowolny kod PHP na serwerze". Motyw i wtyczki edytuje się przez FTP. */
define( 'DISALLOW_FILE_EDIT', true );

/* --- Automatyczne aktualizacje rdzenia, także dużych wydań ----------- */
define( 'WP_AUTO_UPDATE_CORE', true );

/* --- Brak komunikatów o błędach na produkcji ------------------------- */
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );

/* --- Wymuszenie HTTPS w panelu --------------------------------------- */
define( 'FORCE_SSL_ADMIN', true );

/* --- Ograniczenie liczby rewizji wpisów ------------------------------
   Domyślnie WordPress trzyma nieskończenie wiele kopii każdego wpisu.
   Przy blogu to niepotrzebnie puchnąca baza. */
define( 'WP_POST_REVISIONS', 5 );

/* --- Kosz opróżniany po 30 dniach, nie po 30 ------------------------- */
define( 'EMPTY_TRASH_DAYS', 30 );

/* --- Wyłączenie kreatora instalacji motywów/wtyczek przez panel ------
   ⚠️ ZAKOMENTOWANE CELOWO. Włącz TO DOPIERO, gdy zestaw wtyczek
   z kroku 9 jest już zainstalowany — inaczej nie da się ich dodać
   z panelu. Po włączeniu blokuje też automatyczne aktualizacje wtyczek,
   więc rozważ, czy na pewno chcesz. */
// define( 'DISALLOW_FILE_MODS', true );
