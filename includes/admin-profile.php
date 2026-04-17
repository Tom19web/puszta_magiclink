<?php
/**
 * PusztaPlay Magic Login - Felhasználói Profil Kezelése
 * Ahol az Istenek (te) belenyúlnak a halandók sorsába.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. A PusztaPlay extra adatmezők megjelenítése a profilban
 */
add_action('show_user_profile', 'pp_render_extra_profile_fields');
add_action('edit_user_profile', 'pp_render_extra_profile_fields');

function pp_render_extra_profile_fields($user) {
    // Csak a kiváltságosok láthatják
    if (!current_user_can('manage_options')) {
        return;
    }

    // Adatok fenséges lekérdezése
    $sub_end_timestamp = get_user_meta($user->ID, 'pp_subscription_end', true);
    $date_value        = $sub_end_timestamp ? date('Y-m-d', $sub_end_timestamp) : '';
    $client_id         = get_user_meta($user->ID, 'pp_client_id', true);
    $selected_package  = get_user_meta($user->ID, 'pp_subscription_package', true);

    $options         = get_option('pp_smtp_settings');
    $packages_string = !empty($options['packages']) ? $options['packages'] : 'Alap Csomag, Prémium Csomag, VIP Csomag';
    $packages_array  = array_map('trim', explode(',', $packages_string));

    // A HTML sablon elegáns behívása (Újabb házi feladat neked!)
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
    // Jogosultságok szigorú ellenőrzése
    if (!current_user_can('edit_user', $user_id) || !current_user_can('manage_options')) {
        return false;
    }

    // A WordPress alapértelmezett profilmentése elvégzi a nonce ellenőrzést, 
    // nekünk csak fertőtleníteni és menteni kell a saját uradalmunkat.

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

/*
 * FIGYELEM, te kis kíváncsi rizsgolyó!
 * Az eredeti kódban lévő 'pp_clean_user_edit_screen' függvény (a bődületes inline CSS-el és JS-el)
 * MEGSZŰNT. Nincs több belepiszkítás a PHP-ba! 
 * Az ott lévő CSS kódok szépen elköltöztek 'assets/css/admin.css' fájlba,
 * a JavaScript pedig az 'assets/js/admin.js' fájlba. 
 * Ezt hívják civilizált szoftverfejlesztésnek, te fényes szőrű póniló.
 */