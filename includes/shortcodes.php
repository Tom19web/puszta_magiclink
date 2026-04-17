<?php
/**
 * PusztaPlay Magic Login - Shortcodes
 * A kirakat. Ahol a pórnép találkozik a mágiával.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. A Bejelentkező Űrlap [pusztaplay_login]
 */
add_shortcode('pusztaplay_login', 'pp_render_login_shortcode');

function pp_render_login_shortcode() {
    // Kényelmesen elkapjuk a kimenetet, hogy ne borítsuk fel a WP renderelését
    ob_start();

    // Ha a halandó már be van jelentkezve, fenségesen tájékoztatjuk
    if (is_user_logged_in()) {
        $dashboard_url = home_url('/vezerlopult/');
        $logout_url    = wp_nonce_url(add_query_arg('pp_magic_logout', '1', home_url('/')), 'pp_logout_action', 'pp_nonce');
        
        // Ezt a fájlt neked kell megírnod a templates mappában!
        include PP_MAGIC_DIR . 'templates/frontend-logged-in-card.php';
        return ob_get_clean();
    }

    $show_form = true;
    $message   = '';

    if (isset($_POST['pp_magic_email'])) {
        // Honeypot védelem: ha a bot belelépett a csapdába
        if (!empty($_POST['pp_website_url_catch'])) {
            $show_form = false;
            $message = 'success_fake'; // Sablonban kezeljük a hamis sikert
        } else {
            $email           = sanitize_email($_POST['pp_magic_email']);
            $user_ip         = pp_get_user_ip();
            $ip_block_key    = 'pp_block_' . md5($user_ip);
            $email_limit_key = 'pp_limit_' . md5($email);
            $fail_key        = 'pp_fails_' . md5($user_ip);

            // Blokkolt IP ellenőrzése
            if (get_transient($ip_block_key)) {
                $show_form = false;
                $message   = 'blocked';
            } 
            // Túl gyors újraküldés ellenőrzése
            elseif ($resend_until = (int) get_transient($email_limit_key)) {
                if ($resend_until > time()) {
                    $show_form = false;
                    $message   = 'wait';
                    $remaining = max(0, $resend_until - time());
                }
            } 
            // Jöhet a varázslat!
            else {
                $result = pp_generate_and_send_magic_link($email);
                
                if (is_wp_error($result)) {
                    if ($result->get_error_code() === 'no_user') {
                        $fails = (int) get_transient($fail_key) + 1;
                        if ($fails >= 3) {
                            set_transient($ip_block_key, true, 5 * MINUTE_IN_SECONDS);
                            delete_transient($fail_key);
                            $show_form = false;
                            $message   = 'blocked';
                        } else {
                            set_transient($fail_key, $fails, 5 * MINUTE_IN_SECONDS);
                            $message        = 'not_found';
                            $remaining_try  = 3 - $fails;
                        }
                    } else {
                        $message       = 'error';
                        $error_message = $result->get_error_message();
                    }
                } else {
                    // Micsoda diadal!
                    $resend_until = time() + (5 * MINUTE_IN_SECONDS);
                    set_transient($email_limit_key, $resend_until, 5 * MINUTE_IN_SECONDS);
                    delete_transient($fail_key);
                    $show_form = false;
                    $message   = 'success';
                    $remaining = max(0, $resend_until - time());
                }
            }
        }
        
        // A feldolgozás eredményének megfelelő kártya betöltése
        if ($message) {
            // A sablonodban (frontend-messages.php) a $message változó alapján mutasd a megfelelő designt!
            include PP_MAGIC_DIR . 'templates/frontend-messages.php';
        }
    }

    // Ha minden tiszta, és nem vagyunk tiltva, jöhet a beviteli mező
    if ($show_form && !get_transient('pp_block_' . md5(pp_get_user_ip()))) {
        include PP_MAGIC_DIR . 'templates/frontend-login-form.php';
    }

    return ob_get_clean();
}

/**
 * 2. Az Egyéni Vezérlőpult [pusztaplay_dashboard]
 */
add_shortcode('pusztaplay_dashboard', 'pp_render_dashboard_shortcode');

function pp_render_dashboard_shortcode() {
    if (!is_user_logged_in()) {
        wp_safe_redirect(home_url('/belepes/'));
        exit;
    }

    ob_start();
    
    $current_user      = wp_get_current_user();
    $sub_end_timestamp = get_user_meta($current_user->ID, 'pp_subscription_end', true);
    $package_meta      = get_user_meta($current_user->ID, 'pp_subscription_package', true);
    $client_id_meta    = get_user_meta($current_user->ID, 'pp_client_id', true);
    
    $sub_package = !empty($package_meta) ? $package_meta : 'Nincs aktív csomag';
    $client_id   = !empty($client_id_meta) ? $client_id_meta : 'Nincs megadva';

    if ($sub_end_timestamp) {
        $sub_end_date = date_i18n('Y. F j.', $sub_end_timestamp);
        $sub_status   = (time() > $sub_end_timestamp) ? 'lejárt' : 'aktív';
    } else {
        $sub_end_date = 'Nincs beállítva';
        $sub_status   = 'inaktív';
    }

    // Itt hívjuk be a fenséges HTML sablonodat a vezérlőpulthoz
    include PP_MAGIC_DIR . 'templates/frontend-dashboard.php';
    
    return ob_get_clean();
}

/**
 * 3. Gombok és Prémium Tartalomzárak
 */
add_shortcode('pusztaplay_header_btn', function() {
    $url  = is_user_logged_in() ? home_url('/vezerlopult/') : home_url('/belepes/');
    $text = is_user_logged_in() ? 'PusztaPlay Dashboard' : 'Bejelentkezés';
    return '<a href="' . esc_url($url) . '" class="pp-modern-btn">' . esc_html($text) . '</a>';
});

add_shortcode('pusztaplay_logout_btn', function() {
    if (!is_user_logged_in()) return ''; 
    $logout_url = wp_nonce_url(add_query_arg('pp_magic_logout', '1', home_url('/')), 'pp_logout_action', 'pp_nonce');
    return '<a href="' . esc_url($logout_url) . '" class="pp-modern-btn pp-btn-danger">Kijelentkezés</a>';
});

add_shortcode('pusztaplay_vedett', function($atts, $content = null) {
    if (is_user_logged_in()) {
        return do_shortcode($content); 
    }
    
    ob_start();
    // A csóróknak szóló üzenet, akik fizetés nélkül próbálnak nézelődni
    include PP_MAGIC_DIR . 'templates/frontend-protected-content.php';
    return ob_get_clean();
});

/**
 * 4. Kijelentkezés lekezelése (Kivételesen itt maradhat, mint egy frontend action)
 */
add_action('template_redirect', 'pp_custom_logout_handler');

function pp_custom_logout_handler() {
    if (isset($_GET['pp_magic_logout']) && $_GET['pp_magic_logout'] === '1') {
        if (!isset($_GET['pp_nonce']) || !wp_verify_nonce($_GET['pp_nonce'], 'pp_logout_action')) {
            wp_safe_redirect(home_url('/'));
            exit;
        }

        wp_logout();
        
        // Betöltjük a fenséges kijelentkező splash screent
        include PP_MAGIC_DIR . 'templates/frontend-logout-splash.php';
        exit;
    }
}