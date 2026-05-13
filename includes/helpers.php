<?php
/**
 * PusztaPlay Magic Login - Segédfüggvények
 * Ahol a mindennapi rabszolgamunka és a sötét kukkolás zajlik.
 */

if (!defined('ABSPATH')) {
    exit;
}

// ── Xtream jelszó titkosítás/dekriptálás ─────────────────────
// AES-256-GCM authenticated encryption.
// Meglévő AES-128-CBC adatok automatikusan migrálódnak mentéskor.

function pp_encrypt_pass($plain) {
    if (empty($plain)) return '';
    $key = defined('AUTH_KEY') ? AUTH_KEY : wp_salt('auth');
    $key = hash('sha256', $key, true);
    $iv  = openssl_random_pseudo_bytes(12);
    $tag = '';
    $ciphertext = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    if ($ciphertext === false) return '';
    return base64_encode('2:' . $iv . $tag . $ciphertext);
}

function pp_decrypt_pass($encrypted) {
    if (empty($encrypted)) return '';
    $key = defined('AUTH_KEY') ? AUTH_KEY : wp_salt('auth');

    // Try new format (AES-256-GCM, prefixed with "2:")
    if (strpos($encrypted, '2:') === 0) {
        $data   = base64_decode(substr($encrypted, 2));
        $key256 = hash('sha256', $key, true);
        $iv     = substr($data, 0, 12);
        $tag    = substr($data, 12, 16);
        $ct     = substr($data, 28);
        $dec    = openssl_decrypt($ct, 'aes-256-gcm', $key256, OPENSSL_RAW_DATA, $iv, $tag);
        if ($dec !== false) return $dec;
    }

    // Fallback to legacy AES-128-CBC format
    $iv = substr(md5($key), 0, 16);
    return openssl_decrypt(base64_decode($encrypted), 'AES-128-CBC', substr($key, 0, 16), 0, $iv);
}

// backward compat wrapper
function pp_get_xtream_pass($user_id) {
    $creds = pp_get_xtream_creds($user_id);
    return $creds['xtream_pass'];
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

// ── Xtream szerveroldali account info lekérése (5 perces cache) ──
function pp_fetch_xtream_account_info($user_id) {
    $user_id = (int) $user_id;
    $cache_key = 'pp_xtream_info_' . $user_id;
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    $creds = pp_get_xtream_creds($user_id);
    if (empty($creds['xtream_user']) || empty($creds['xtream_pass'])) {
        return new WP_Error('no_creds', 'Nincsenek Xtream credentials-ek.');
    }

    $server = defined('PP_XTREAM_SERVER') ? PP_XTREAM_SERVER : 'https://live.pusztaplay.eu';
    $url = trailingslashit($server) . 'player_api.php?username=' . urlencode($creds['xtream_user']) . '&password=' . urlencode($creds['xtream_pass']);

    $response = wp_remote_get($url, [
        'timeout'     => 10,
        'user-agent'  => 'PusztaPlay/1.0',
        'httpversion' => '1.1',
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $code = wp_remote_retrieve_response_code($response);
    if ($code !== 200) {
        return new WP_Error('api_error', 'Xtream API hiba: HTTP ' . $code);
    }

    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);
    if (!$data || !isset($data['user_info'])) {
        return new WP_Error('parse_error', 'Érvénytelen Xtream API válasz.');
    }

    $info = [
        'username'              => $data['user_info']['username'] ?? $creds['xtream_user'],
        'password'              => $data['user_info']['password'] ?? '',
        'message'               => $data['user_info']['message'] ?? '',
        'auth'                  => !empty($data['user_info']['auth']),
        'status'                => $data['user_info']['status'] ?? 'Unknown',
        'exp_date'              => $data['user_info']['exp_date'] ?? '',
        'is_trial'              => !empty($data['user_info']['is_trial']),
        'active_cons'           => $data['user_info']['active_cons'] ?? '0',
        'created_at'            => $data['user_info']['created_at'] ?? '',
        'max_connections'       => $data['user_info']['max_connections'] ?? '0',
        'allowed_output_formats'=> isset($data['user_info']['allowed_output_formats']) ? (array) $data['user_info']['allowed_output_formats'] : [],
        'server_timezone'       => $data['server_info']['timezone'] ?? '',
        'server_time'           => isset($data['server_info']['timestamp_now']) ? date_i18n('Y-m-d H:i:s', (int) $data['server_info']['timestamp_now']) : '',
    ];

    set_transient($cache_key, $info, 5 * MINUTE_IN_SECONDS);
    return $info;
}

/**
 * 1. IP cím fenséges és szigorú lekérdezése
 * Mert nem dőlünk be minden ócska proxy hamisításnak!
 */
function pp_get_user_ip() {
    $ip_keys = array(
        'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
        'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'
    );
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
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