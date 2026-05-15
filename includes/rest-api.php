<?php
/**
 * PusztaPlay Magic Login — REST API
 * QR-kódos TV bejelentkezés, profil szinkronizáció
 */

if (!defined('ABSPATH')) exit;

add_action('rest_api_init', 'pp_register_rest_routes');

/**
 * Permission callback: requires valid api_key in query params.
 */
function pp_rest_require_api_key($request) {
  $api_key = $request->get_param('api_key');
  if (!$api_key) {
    return new WP_Error('missing_key', 'API kulcs megadása kötelező.', ['status' => 401]);
  }
  $user_id = pp_validate_api_key($api_key);
  return !is_wp_error($user_id);
}

function pp_register_rest_routes() {
  // QR kód igénylés (publikus)
  register_rest_route('pusztaplay/v1', '/qr-request', [
    'methods'             => 'POST',
    'callback'            => 'pp_rest_qr_request',
    'permission_callback' => '__return_true',
  ]);

  // QR kód poll (publikus — a TV app lekérdezi a QR státuszát)
  register_rest_route('pusztaplay/v1', '/qr-poll', [
    'methods'             => 'GET',
    'callback'            => 'pp_rest_qr_poll',
    'permission_callback' => '__return_true',
    'args' => [
      'api_key' => [
        'required'          => true,
        'sanitize_callback' => 'sanitize_text_field',
        'validate_callback' => function($v) { return strlen($v) <= 64; },
      ],
      'masked' => [
        'sanitize_callback' => 'rest_sanitize_boolean',
      ],
    ],
  ]);

  // Profilok lekérése / mentése (api_key kötelező)
  register_rest_route('pusztaplay/v1', '/profiles', [
    'methods'             => 'GET',
    'callback'            => 'pp_rest_get_profiles',
    'permission_callback' => 'pp_rest_require_api_key',
    'args' => [
      'api_key' => [
        'required'          => true,
        'sanitize_callback' => 'sanitize_text_field',
        'validate_callback' => function($v) { return strlen($v) <= 64; },
      ],
    ],
  ]);

  // Egyedi profil műveletek (api_key kötelező)
  register_rest_route('pusztaplay/v1', '/profile', [
    'methods'             => 'POST',
    'callback'            => 'pp_rest_save_single_profile',
    'permission_callback' => 'pp_rest_require_api_key',
    'args' => [
      'api_key' => [
        'required'          => true,
        'sanitize_callback' => 'sanitize_text_field',
        'validate_callback' => function($v) { return strlen($v) <= 64; },
      ],
    ],
  ]);
}

/**
 * API key validálás — visszaadja a user_id-t vagy hibát
 * Ellenőrzi a visszavonást is (pp_api_key_revoked).
 */
function pp_validate_api_key($api_key) {
  global $wpdb;
  $user_id = $wpdb->get_var($wpdb->prepare(
    "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'pp_api_key' AND meta_value = %s LIMIT 1",
    $api_key
  ));
  if (!$user_id) {
    return new WP_Error('invalid_key', 'Érvénytelen API kulcs.', ['status' => 401]);
  }
  $revoked = get_user_meta($user_id, 'pp_api_key_revoked', true);
  if ($revoked) {
    return new WP_Error('revoked_key', 'API kulcs visszavonva.', ['status' => 403]);
  }
  return (int) $user_id;
}

/**
 * Sanitize nested array: limit depth to 4, size to 500KB, re-encode for safety.
 */
function pp_sanitize_nested_array($data) {
  if (!is_array($data)) return [];
  $encoded = wp_json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
  if (strlen($encoded) > 500 * 1024) return []; // 500KB max
  $decoded = json_decode($encoded, true);
  return is_array($decoded) ? $decoded : [];
}

// ─── QR kódok ─────────────────────────────────────

function pp_rest_qr_request() {
  $code = strtoupper(wp_generate_password(8, false));

  $data = [
    'status'     => 'pending',
    'created_at' => time(),
  ];
  set_transient('pp_tv_' . $code, $data, 5 * MINUTE_IN_SECONDS);

  $auth_url = add_query_arg('pp_tv', $code, home_url('/'));

  return new WP_REST_Response([
    'code'     => $code,
    'auth_url' => $auth_url,
    'expires_in' => 300,
  ], 200);
}

function pp_rest_qr_poll($request) {
  $code          = $request->get_param('code');
  $transient_key = 'pp_tv_' . $code;
  $data          = get_transient($transient_key);

  if (!$data) {
    return new WP_REST_Response(['status' => 'expired'], 200);
  }

  if ($data['status'] === 'authenticated') {
    $response = [
      'status'      => 'authenticated',
      'xtream_user' => $data['xtream_user'],
      'xtream_pass' => $data['xtream_pass'],
      'user_email'  => $data['user_email'] ?? '',
      'nickname'    => $data['nickname'] ?? '',
      'phone'       => $data['phone'] ?? '',
      'api_key'     => $data['api_key'] ?? '',
      'package'     => $data['package'] ?? '',
      'sub_end'     => $data['sub_end'] ?? 0,
    ];
    delete_transient($transient_key);
    return new WP_REST_Response($response, 200);
  }

  return new WP_REST_Response(['status' => 'pending'], 200);
}

// ─── Profilok — GET (összes profil lekérése) ──────

function pp_rest_get_profiles($request) {
  $api_key = $request->get_param('api_key');
  $user_id = pp_validate_api_key($api_key);
  if (is_wp_error($user_id)) {
    return new WP_REST_Response(['error' => $user_id->get_error_message()], $user_id->get_error_data()['status']);
  }

  $profiles = get_user_meta($user_id, 'pp_profiles', true);
  if (empty($profiles)) {
    $profiles = [];
  }

  // Also include user-level watch progress (VOD/series positions)
  $watch_progress = get_user_meta($user_id, 'pp_watch_progress', true);
  if (empty($watch_progress)) {
    $watch_progress = [];
  }

  return new WP_REST_Response([
    'profiles'       => $profiles,
    'watch_progress' => $watch_progress,
  ], 200);
}

// ─── Profilok — POST (összes profil cseréje) ──────

function pp_rest_save_profiles($request) {
  $api_key = $request->get_param('api_key');
  $user_id = pp_validate_api_key($api_key);
  if (is_wp_error($user_id)) {
    return new WP_REST_Response(['error' => $user_id->get_error_message()], $user_id->get_error_data()['status']);
  }

  $body = json_decode($request->get_body(), true);
  if (!$body) {
    return new WP_REST_Response(['error' => 'Érvénytelen JSON.'], 400);
  }

  if (isset($body['profiles'])) {
    if (!is_array($body['profiles'])) {
      return new WP_REST_Response(['error' => 'A profiles mező csak tömb lehet.'], 400);
    }
    $body['profiles'] = array_map(function($p) {
      if (!is_array($p)) return $p;
      if (isset($p['name']))  $p['name']  = sanitize_text_field($p['name']);
      if (isset($p['color'])) $p['color'] = sanitize_text_field($p['color']);
      if (isset($p['avatar'])) $p['avatar'] = sanitize_text_field($p['avatar']);
      return $p;
    }, $body['profiles']);
    update_user_meta($user_id, 'pp_profiles', pp_sanitize_nested_array($body['profiles']));
  }

  if (isset($body['watch_progress'])) {
    update_user_meta($user_id, 'pp_watch_progress', pp_sanitize_nested_array($body['watch_progress']));
  }

  return new WP_REST_Response(['success' => true], 200);
}

// ─── Profil — POST (egyedi profil mentése / létrehozás / törlés) ──

function pp_rest_save_single_profile($request) {
  $api_key    = $request->get_param('api_key');
  $user_id    = pp_validate_api_key($api_key);
  if (is_wp_error($user_id)) {
    return new WP_REST_Response(['error' => $user_id->get_error_message()], $user_id->get_error_data()['status']);
  }

  $body       = json_decode($request->get_body(), true) ?? [];
  $action     = $body['action'] ?? 'save';
  $profile_id = $body['profile_id'] ?? '';

  if (empty($profile_id) && $action !== 'create') {
    return new WP_REST_Response(['error' => 'Hiányzó profile_id.'], 400);
  }

  $profiles = get_user_meta($user_id, 'pp_profiles', true);
  if (empty($profiles) || !is_array($profiles)) {
    $profiles = [];
  }

  switch ($action) {
    case 'create':
      if (count($profiles) >= 3) {
        return new WP_REST_Response(['error' => 'Maximum 3 profil hozható létre.'], 400);
      }
      $new_id = 'prof_' . uniqid();
      $profiles[] = [
        'id'            => $new_id,
        'name'          => $body['name'] ?? ('Profil ' . (count($profiles) + 1)),
        'color'         => $body['color'] ?? '#ffcc00',
        'favorites'     => [],
        'watch_later'   => [],
        'watch_progress' => [],
      ];
      update_user_meta($user_id, 'pp_profiles', $profiles);
      return new WP_REST_Response(['profile_id' => $new_id, 'profiles' => $profiles], 200);

    case 'delete':
      $profiles = array_values(array_filter($profiles, function($p) use ($profile_id) {
        return $p['id'] !== $profile_id;
      }));
      update_user_meta($user_id, 'pp_profiles', $profiles);
      return new WP_REST_Response(['success' => true, 'profiles' => $profiles], 200);

    case 'save':
    default:
      $found = false;
      foreach ($profiles as &$p) {
        if ($p['id'] === $profile_id) {
          if (isset($body['name']))  $p['name']  = sanitize_text_field($body['name']);
          if (isset($body['color'])) $p['color'] = sanitize_text_field($body['color']);
          if (isset($body['favorites']))     $p['favorites']     = $body['favorites'];
          if (isset($body['watch_later']))   $p['watch_later']   = $body['watch_later'];
          if (isset($body['watch_progress'])) $p['watch_progress'] = $body['watch_progress'];
          $found = true;
          break;
        }
      }
      unset($p);

      if (!$found) {
        return new WP_REST_Response(['error' => 'Profil nem található.'], 404);
      }

      update_user_meta($user_id, 'pp_profiles', $profiles);

      // Sync watch_progress to global key too (for cross-profile resume)
      $all_progress = [];
      foreach ($profiles as $pf) {
        foreach ($pf['watch_progress'] ?? [] as $wp) {
          $key = $wp['key'] ?? '';
          if ($key) $all_progress[$key] = $wp;
        }
      }
      update_user_meta($user_id, 'pp_watch_progress', array_values($all_progress));

      return new WP_REST_Response(['success' => true], 200);
  }
}

// ─── Felhasználói adatok lekérése (apiKey alapján) ──
function pp_rest_get_user($request) {
  $api_key = $request->get_param('api_key');
  $user_id = pp_validate_api_key($api_key);
  if (is_wp_error($user_id)) {
    return new WP_REST_Response(['error' => $user_id->get_error_message()], $user_id->get_error_data()['status']);
  }

  $user = get_userdata($user_id);
  $creds = pp_get_xtream_creds($user_id);
  $xtream_pass = $creds['xtream_pass'];

  // Masked mode: return only first 4 + last 4 characters
  if ($request->get_param('masked') && strlen($xtream_pass) > 8) {
    $xtream_pass = substr($xtream_pass, 0, 4) . '****' . substr($xtream_pass, -4);
  }

  return new WP_REST_Response([
    'email'       => $user ? $user->user_email : '',
    'nickname'    => get_user_meta($user_id, 'nickname', true) ?: $user->display_name,
    'phone'       => get_user_meta($user_id, 'pp_phone', true) ?: '',
    'xtream_user' => $creds['xtream_user'],
    'xtream_pass' => $xtream_pass,
    'package'     => $creds['package'],
    'sub_end'     => $creds['sub_end'],
  ], 200);
}

// ─── Közvetlen bejelentkezés email + WP jelszóval ───
function pp_rest_direct_auth($request) {
  $body = json_decode($request->get_body(), true) ?? [];
  $email = sanitize_email($body['email'] ?? '');
  $password = $body['password'] ?? '';

  if (!is_email($email) || empty($password)) {
    return new WP_REST_Response(['error' => 'Email és jelszó megadása kötelező.'], 400);
  }

  // Rate limiting: max 5 próbálkozás / 5 perc IP-nként
  $ip = pp_get_user_ip();
  $rate_key = 'pp_auth_fails_' . md5($ip);
  $attempts = (int) get_transient($rate_key);
  $max_attempts = apply_filters('pp_magic_rate_limit_attempts', 5);
  $window = apply_filters('pp_magic_rate_limit_window', 5 * MINUTE_IN_SECONDS);
  if ($attempts >= $max_attempts) {
    return new WP_REST_Response(['error' => 'Túl sok próbálkozás. Várj 5 percet.'], 429);
  }

  $user = wp_authenticate($email, $password);
  if (is_wp_error($user)) {
    set_transient($rate_key, $attempts + 1, $window);
    return new WP_REST_Response(['error' => 'Érvénytelen email vagy jelszó.'], 401);
  }

  // Sikeres belépés: töröljük a próbálkozás számlálót
  delete_transient($rate_key);

  $user_id = $user->ID;
  $creds = pp_get_xtream_creds($user_id);

  if (empty($creds['xtream_user']) || empty($creds['xtream_pass'])) {
    return new WP_REST_Response(['error' => 'Nincsenek Xtream credentials-ek ehhez a fiókhoz.'], 404);
  }

  // Generate or reuse apiKey
  $api_key = get_user_meta($user_id, 'pp_api_key', true);
  if (empty($api_key)) {
    $api_key = wp_generate_password(32, false);
    update_user_meta($user_id, 'pp_api_key', $api_key);
  }

  return new WP_REST_Response([
    'xtream_user' => $creds['xtream_user'],
    'xtream_pass' => $creds['xtream_pass'],
    'email'       => $user->user_email,
    'nickname'    => get_user_meta($user_id, 'nickname', true) ?: $user->display_name,
    'phone'       => get_user_meta($user_id, 'pp_phone', true) ?: '',
    'api_key'     => $api_key,
    'package'     => $creds['package'],
    'sub_end'     => $creds['sub_end'],
  ], 200);
}
