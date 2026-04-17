<?php
/*
Plugin Name: PusztaPlay Magic Login
Description: Biztonságos bejelentkezés jelszó nélkül, e-mailben küldött egyszer használatos linkkel. (Golyóálló és Elegáns verzió)
Version: 2.0
Author: PusztaPlay
*/

// Közvetlen hozzáférés tiltása - mert nem vagyunk barbárok, akik nyitva hagyják az ajtót
if (!defined('ABSPATH')) {
    exit;
}

// Alapvető konstansok definiálása, hogy ne kelljen folyton elérési utakkal bohóckodnunk
define('PP_MAGIC_VERSION', '2.0');
define('PP_MAGIC_DIR', plugin_dir_path(__FILE__));
define('PP_MAGIC_URL', plugin_dir_url(__FILE__));

/**
 * A Csodálatos, Új Modulok Betöltése
 * Logikai sorrendben, ahogy a kis pedáns lelked kívánja.
 */

// 1. Segédfüggvények (IP lekérdezés, központi logolás)
require_once PP_MAGIC_DIR . 'includes/helpers.php';

// 2. Assetek (CSS, JS) kulturált regisztrálása
require_once PP_MAGIC_DIR . 'includes/assets.php';

// 3. Autentikáció és beléptetés (A sötét mágia motorja)
require_once PP_MAGIC_DIR . 'includes/auth.php';

// 4. Shortcode-ok (A frontend megjelenés és a csillogás)
require_once PP_MAGIC_DIR . 'includes/shortcodes.php';

// 5. Adminisztrációs modulok - Micsoda elegancia: csak akkor töltjük be, ha az adminban vagyunk!
if (is_admin()) {
    require_once PP_MAGIC_DIR . 'includes/admin-settings.php';
    require_once PP_MAGIC_DIR . 'includes/admin-profile.php';
    require_once PP_MAGIC_DIR . 'includes/admin-user-list.php';
    require_once PP_MAGIC_DIR . 'includes/admin-members.php';
}

/**
 * 6. Aktivációs hook (A telepítő)
 * Ezt is ki lehetne szervezni, de egyelőre elfér itt, hogy meglegyen a "belepes" és "vezerlopult" oldalad.
 */
register_activation_hook(__FILE__, 'pp_magic_login_activate');

function pp_magic_login_activate() {
    $author_id = 1; 
    
    if (!get_page_by_path('belepes')) {
        wp_insert_post(array(
            'post_title'   => 'Belépés', 
            'post_name'    => 'belepes', 
            'post_content' => '[pusztaplay_login]', 
            'post_status'  => 'publish', 
            'post_type'    => 'page', 
            'post_author'  => $author_id
        ));
    }
    
    if (!get_page_by_path('vezerlopult')) {
        wp_insert_post(array(
            'post_title'   => 'Vezérlőpult', 
            'post_name'    => 'vezerlopult', 
            'post_content' => '[pusztaplay_dashboard]', 
            'post_status'  => 'publish', 
            'post_type'    => 'page', 
            'post_author'  => $author_id
        ));
    }
}