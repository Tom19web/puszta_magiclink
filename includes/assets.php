<?php
/**
 * PusztaPlay Magic Login - Assetek (CSS & JS) betöltése
 * Itt száműzzük az inline formázást a történelem szemétdombjára.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Frontend stílusok és scriptek fenséges regisztrálása
add_action('wp_enqueue_scripts', 'pp_enqueue_frontend_assets');

function pp_enqueue_frontend_assets() {
    // A frontend.css betöltése - a harsány, pop-art gombjaid otthona
    wp_enqueue_style(
        'pp-magic-frontend-style',
        PP_MAGIC_URL . 'assets/css/frontend.css',
        array(),
        PP_MAGIC_VERSION
    );

    // A frontend.js betöltése az időzítőknek és az interakcióknak
    wp_enqueue_script(
        'pp-magic-frontend-script',
        PP_MAGIC_URL . 'assets/js/frontend.js',
        array(), 
        PP_MAGIC_VERSION,
        true // Fenségesen a footerbe száműzzük, hogy ne akassza meg a renderelést
    );
}

// 2. Admin stílusok és scriptek sebészi pontosságú betöltése
add_action('admin_enqueue_scripts', 'pp_enqueue_admin_assets');

function pp_enqueue_admin_assets($hook) {
    // Kíméletlen szűrő: nem szemeteljük tele a teljes WordPress admint!
    // Csak ott engedjük be a stílusokat, ahol a mi varázslatunk uralkodik.
    $allowed_hooks = array(
        'settings_page_pp-magic-login',
        'users_page_pp-new-member',
        'user-edit.php',
        'profile.php',
        'users.php'
    );

    if (!in_array($hook, $allowed_hooks)) {
        return; // Ha nem a mi felségterületünk, csendben visszavonulunk.
    }

    // Az admin.css, amivel eltünteted az Elementor és Yoast idegesítő dobozait
    wp_enqueue_style(
        'pp-magic-admin-style',
        PP_MAGIC_URL . 'assets/css/admin.css',
        array(),
        PP_MAGIC_VERSION
    );

    // Az admin.js, ha netán DOM-manipulációval akarsz tisztogatni a profil oldalon
    wp_enqueue_script(
        'pp-magic-admin-script',
        PP_MAGIC_URL . 'assets/js/admin.js',
        array(),
        PP_MAGIC_VERSION,
        true
    );
}