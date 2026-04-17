<?php
/**
 * PusztaPlay Magic Login - Felhasználói Profil Kezelése
 * Ahol az Istenek (te) belenyúlnak a halandók sorsába.
 */

if (!defined('ABSPATH')) {
    exit;
}

// ── Xtream jelszó titkosítás/dekriptálás ─────────────────────

function pp_encrypt_pass($plain) {
    $key = defined('AUTH_KEY') ? AUTH_KEY : wp_salt('auth');
    $iv  = substr(md5($key), 0, 16);
    return base64_encode(openssl_encrypt($plain, 'AES-128-CBC', substr($key, 0, 16), 0, $iv));
}

function pp_decrypt_pass($encrypted) {
    if (empty($encrypted)) return '';
    $key = defined('AUTH_KEY') ? AUTH_KEY : wp_salt('auth');
    $iv  = substr(md5($key), 0, 16);
    return openssl_decrypt(base64_decode($encrypted), 'AES-128-CBC', substr($key, 0, 16), 0, $iv);
}

function pp_get_xtream_pass($user_id) {
    $encrypted = get_user_meta($user_id, 'pp_xtream_pass', true);
    if (!$encrypted) return '';
    return pp_decrypt_pass($encrypted);
}

/**
 * 1. A PusztaPlay extra adatmezők megjelenítése a profilban
 */
add_action('show_user_profile', 'pp_render_extra_profile_fields');
add_action('edit_user_profile', 'pp_render_extra_profile_fields');

function pp_render_extra_profile_fields($user) {
    if (!current_user_can('manage_options')) {
        return;
    }

    $sub_end_timestamp = get_user_meta($user->ID, 'pp_subscription_end', true);
    $date_value        = $sub_end_timestamp ? date('Y-m-d', $sub_end_timestamp) : '';
    $client_id         = get_user_meta($user->ID, 'pp_client_id', true);
    $selected_package  = get_user_meta($user->ID, 'pp_subscription_package', true);

    $options         = get_option('pp_smtp_settings');
    $options         = is_array($options) ? $options : array();
    $packages_string = !empty($options['packages']) ? $options['packages'] : 'Alap Csomag, Prémium Csomag, VIP Csomag';
    $packages_array  = array_map('trim', explode(',', $packages_string));

    $template_path = PP_MAGIC_DIR . 'templates/admin-profile-fields.php';
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<div class="notice notice-error"><p>Micsoda hanyagság! Hiányzik a <code>templates/admin-profile-fields.php</code> fájl.</p></div>';
    }
}

/**
 * 2. Az extra adatmezők kíméletlen elmentése
 */
add_action('personal_options_update', 'pp_save_extra_profile_fields');
add_action('edit_user_profile_update', 'pp_save_extra_profile_fields');

function pp_save_extra_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id) || !current_user_can('manage_options')) {
        return false;
    }

    if (isset($_POST['pp_subscription_end'])) {
        $end_date = sanitize_text_field($_POST['pp_subscription_end']);
        if (!empty($end_date)) {
            update_user_meta($user_id, 'pp_subscription_end', strtotime($end_date . ' 23:59:59'));
        } else {
            delete_user_meta($user_id, 'pp_subscription_end');
        }
    }

    if (isset($_POST['pp_client_id'])) {
        update_user_meta($user_id, 'pp_client_id', sanitize_text_field($_POST['pp_client_id']));
    }

    // Xtream jelszó titkosítva mentése
    if (isset($_POST['pp_xtream_pass']) && !empty($_POST['pp_xtream_pass'])) {
        $plain = sanitize_text_field($_POST['pp_xtream_pass']);
        update_user_meta($user_id, 'pp_xtream_pass', pp_encrypt_pass($plain));
    }

    if (isset($_POST['pp_subscription_package'])) {
        $package = sanitize_text_field($_POST['pp_subscription_package']);
        if (!empty($package)) {
            update_user_meta($user_id, 'pp_subscription_package', $package);
        } else {
            delete_user_meta($user_id, 'pp_subscription_package');
        }
    }
}

/**
 * 3. A Diktátor Tisztogatása - Felesleges mezők eltüntetése a backendből
 */
add_filter('user_contactmethods', 'pp_ruthlessly_clean_contact_methods');

function pp_ruthlessly_clean_contact_methods($methods) {
    $junk_methods = [
        'aim', 'yim', 'jabber', 'twitter', 'facebook', 'instagram',
        'linkedin', 'myspace', 'pinterest', 'soundcloud', 'tumblr',
        'wikipedia', 'youtube', 'mastodon'
    ];

    foreach ($junk_methods as $junk) {
        unset($methods[$junk]);
    }

    return $methods;
}
