<?php
/*
Plugin Name: PusztaPlay Auth + CRM Plugin
Description: Biztonságos jelszó nélküli bejelentkezés, QR TV auth, előfizetés-kezelő CRM, profil szinkronizáció, emlékeztetők és számlázás. (Golyóálló és Elegáns verzió)
Version: 2.2
Author: PusztaPlay
*/

// Közvetlen hozzáférés tiltása
if (!defined('ABSPATH')) {
    exit;
}

// Alapvető konstansok
define('PP_MAGIC_VERSION', '2.2');
define('PP_MAGIC_DIR', plugin_dir_path(__FILE__));
define('PP_MAGIC_URL', plugin_dir_url(__FILE__));
define('PP_XTREAM_SERVER', 'https://live.pusztaplay.eu');

/**
 * A Csodálatos, Új Modulok Betöltése
 */

// 1. Segédfüggvények
require_once PP_MAGIC_DIR . 'includes/helpers.php';

// 2. Assetek (CSS, JS)
require_once PP_MAGIC_DIR . 'includes/assets.php';

// 3. Autentikáció
require_once PP_MAGIC_DIR . 'includes/auth.php';

// 4. REST API
require_once PP_MAGIC_DIR . 'includes/rest-api.php';

// 5. Shortcode-ok
require_once PP_MAGIC_DIR . 'includes/shortcodes.php';

// 6. AJAX handlerek (profil törlés, kedvencek törlése a dashboardról)
require_once PP_MAGIC_DIR . 'includes/ajax-handlers.php';

// 7. Cron emlékeztetők (előfizetés lejárat)
require_once PP_MAGIC_DIR . 'includes/cron-reminders.php';

// 8. Adminisztrációs modulok
if (is_admin()) {
    require_once PP_MAGIC_DIR . 'includes/admin-settings.php';
    require_once PP_MAGIC_DIR . 'includes/admin-profile.php';
    require_once PP_MAGIC_DIR . 'includes/admin-user-list.php';
    require_once PP_MAGIC_DIR . 'includes/admin-members.php';
}

/**
 * 9. Aktivációs hook + Cron regisztrálás
 */
register_activation_hook(__FILE__, 'pp_magic_login_activate');
register_deactivation_hook(__FILE__, 'pp_reminder_clear_schedule');

function pp_magic_login_activate() {
    // Cron ütemezés
    pp_reminder_schedule_event();

    $author_id = get_current_user_id() ?: 1;

    if (!(new WP_Query(['pagename' => 'belepes', 'post_type' => 'page', 'fields' => 'ids']))->have_posts()) {
        wp_insert_post(array(
            'post_title'   => 'Belépés',
            'post_name'    => 'belepes',
            'post_content' => '[pusztaplay_login]',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => $author_id
        ));
    }

    if (!(new WP_Query(['pagename' => 'vezerlopult', 'post_type' => 'page', 'fields' => 'ids']))->have_posts()) {
        wp_insert_post(array(
            'post_title'   => 'Vezérlőpult',
            'post_name'    => 'vezerlopult',
            'post_content' => "[pusztaplay_dashboard]\n\n[pusztaplay_service_info]\n\n[pusztaplay_profile_manager]",
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => $author_id
        ));
    }
}