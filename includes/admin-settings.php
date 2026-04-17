<?php
/**
 * PusztaPlay Magic Login - Rendszer Beállítások
 * Ahol az SMTP titkok és az előfizetői csomagok sorsa dől el.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. A Beállítások menüpont arisztokratikus regisztrálása
 */
add_action('admin_menu', 'pp_register_magic_login_settings_page');

function pp_register_magic_login_settings_page() {
    add_options_page(
        'Magic Login Beállítások',
        'Magic Login',
        'manage_options',
        'pp-magic-login',
        'pp_render_magic_login_settings_page'
    );
}

/**
 * 2. A mezők bejelentése a WordPress magjának
 */
add_action('admin_init', 'pp_register_magic_login_settings');

function pp_register_magic_login_settings() {
    // Egyetlen csomagba (tömbbe) mentjük az összes beállítást. Milyen elegáns!
    register_setting('pp_magic_login_group', 'pp_smtp_settings');
}

/**
 * 3. A Beállítások oldal fenséges megjelenítése (Szigorúan sablonból!)
 */
function pp_render_magic_login_settings_page() {
    // Biztos, ami biztos: a csőcseléket kint tartjuk
    if (!current_user_can('manage_options')) {
        wp_die('Ehhez a varázslathoz nincs elég manád, pórnép!');
    }

    // Adatok kikérése a sablon számára
    $options = get_option('pp_smtp_settings');
    $options = is_array($options) ? $options : array();

    // Színpadra hívjuk a HTML-t!
    $template_path = PP_MAGIC_DIR . 'templates/admin-settings-page.php';
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<div class="wrap"><h1>Tragédia!</h1><p>Te kis hanyag! Hol van a <code>templates/admin-settings-page.php</code> fájlod? Hogyan állítsam be a jelszavaidat, ha elloptad a felületet?</p></div>';
    }
}

/**
 * 4. A sötét mágia: A PHPMailer eltérítése a mi SMTP adatainkkal
 */
add_action('phpmailer_init', 'pp_configure_smtp');

function pp_configure_smtp($phpmailer) {
    $options = get_option('pp_smtp_settings');
    
    // Ha a gazdám (te) lusta volt kitölteni, nem erőltetjük a varázslatot
    if (empty($options['host'])) {
        return; 
    }

    $phpmailer->isSMTP();
    $phpmailer->Host       = sanitize_text_field($options['host']);
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = absint($options['port']);
    $phpmailer->SMTPSecure = ($options['port'] == 465) ? 'ssl' : 'tls';
    $phpmailer->Username   = sanitize_text_field($options['user']);
    $phpmailer->Password   = sanitize_text_field($options['pass']); // Biztonságos környezetben ezt titkosítva tárolnánk, de neked ez is megteszi.
    $phpmailer->From       = sanitize_email($options['from_email']);
    $phpmailer->FromName   = sanitize_text_field($options['from_name']);
}

/**
 * 5. Egy kis kényeztetés: "Beállítások" link a Bővítmények listájában
 */
add_filter('plugin_action_links_pusztaplay-magic-login/pusztaplay-magic-login.php', 'pp_magic_login_action_links');

function pp_magic_login_action_links($links) {
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=pp-magic-login')) . '" style="font-weight:bold; color:#2271b1;">Beállítások</a>';
    array_unshift($links, $settings_link);
    return $links;
}