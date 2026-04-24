<?php
/**
 * PusztaPlay Magic Login - Segédfüggvények
 * Ahol a mindennapi rabszolgamunka és a sötét kukkolás zajlik.
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

function pp_get_xtream_creds($user_id) {
    $user_id = (int) $user_id;
    $xtream_user = get_user_meta($user_id, 'pp_client_id', true);
    $encrypted   = get_user_meta($user_id, 'pp_xtream_pass', true);
    $xtream_pass = $encrypted ? pp_decrypt_pass($encrypted) : '';
    $package     = get_user_meta($user_id, 'pp_subscription_package', true);
    $sub_end     = get_user_meta($user_id, 'pp_subscription_end', true);
    return [
        'xtream_user' => $xtream_user ?: '',
        'xtream_pass' => $xtream_pass,
        'package'     => $package ?: '',
        'sub_end'     => $sub_end ? (int) $sub_end : 0,
    ];
}

/**
 * 1. IP cím fenséges és szigorú lekérdezése
 * Mert nem dőlünk be minden ócska proxy hamisításnak!
 */
function pp_get_user_ip() {
    $ip_keys = array(
        'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
        'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'
    );
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                // Validáljuk is azt a nyamvadt IP-t, nehogy egy SQL injekciót kapjunk az arcunkba!
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * 2. Központi logolás - De csak ha nagyon muszáj!
 */
function pp_magic_log($event, $data = []) {
    // Drágaságom, nem szemeteljük tele a szervert minden egyes gombnyomással!
    // Csak akkor logolunk, ha a WP_DEBUG be van kapcsolva.
    if (!defined('WP_DEBUG') || !WP_DEBUG) {
        return;
    }
    
    $context = [
        'timestamp' => current_time('Y-m-d H:i:s'),
        'ip'        => pp_get_user_ip(), // Látod? Itt már a fenséges IP lekérdezést használjuk, nem a buta $_SERVER-t.
        'event'     => $event
    ];
    
    $log_entry = wp_json_encode(array_merge($context, $data));
    error_log('[PP_MAGIC_LOGIN] ' . $log_entry);
}

/**
 * 3. A WP-LOGIN.PHP eltüntetése - Sötét mágia a kíváncsi szemek ellen
 */
function pp_get_login_page_url($redirect = '') {
    $url = home_url('/belepes/');
    if (!empty($redirect)) {
        $url = add_query_arg('redirect_to', rawurlencode($redirect), $url);
    }
    return $url;
}

// Lecseréljük a WordPress alapértelmezett login URL-jét
add_filter('login_url', 'pp_redirect_to_magic_login', 10, 3);

function pp_redirect_to_magic_login($login_url, $redirect, $force_reauth) {
    $custom_url = pp_get_login_page_url($redirect);
    if ($force_reauth) {
        $custom_url = add_query_arg('reauth', '1', $custom_url);
    }
    return $custom_url;
}

// Brutálisan kizárunk mindenkit, aki a régi wp-login.php-t keresi
add_action('init', 'pp_block_default_login_page');

function pp_block_default_login_page() {
    global $pagenow;

    if ($pagenow === 'wp-login.php' && !is_admin()) {
        $action = isset($_REQUEST['action']) ? sanitize_text_field($_REQUEST['action']) : '';
        
        // A jelszóvisszaállítást meghagyjuk a nyomorultaknak
        $allowed_actions = array('logout', 'lostpassword', 'rp', 'resetpass', 'postpass');

        if (!in_array($action, $allowed_actions, true)) {
            $redirect_to = isset($_REQUEST['redirect_to']) ? esc_url_raw($_REQUEST['redirect_to']) : '';
            wp_safe_redirect(pp_get_login_page_url($redirect_to));
            exit;
        }
    }
}

/**
 * 4. Admin sáv (Toolbar) kegyetlen eltüntetése a pórnép elől
 */
add_filter('show_admin_bar', 'pp_hide_admin_bar_for_plebs');

function pp_hide_admin_bar_for_plebs($show) {
    // Ha nem admin, de előfizető, ne lássa az uralkodók sávját!
    if (!is_admin() && current_user_can('subscriber')) {
        return false;
    }
    return $show;
}