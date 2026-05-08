<?php
/**
 * PusztaPlay Auth + CRM — AJAX Handlerek
 * Profil törlés, kedvencek törlése, megnézendők törlése a dashboardról.
 * WordPress nonce + current_user_can védelemmel.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Közös validáció: nonce + bejelentkezett user
 */
function pp_ajax_verify_request() {
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'pp_profile_manager_nonce')) {
        wp_send_json_error(['message' => 'Biztonsági hiba: érvénytelen nonce.']);
    }

    $user_id = get_current_user_id();
    if (!$user_id) {
        wp_send_json_error(['message' => 'Bejelentkezés szükséges.']);
    }

    return $user_id;
}

/**
 * Közös profil validáció: megkeresi a profilt a user_meta-ban
 */
function pp_ajax_get_profile($user_id, $profile_id) {
    $profiles = get_user_meta($user_id, 'pp_profiles', true);
    if (empty($profiles) || !is_array($profiles)) {
        wp_send_json_error(['message' => 'Nincsenek profilok.']);
    }

    $found = false;
    $profile = null;
    $index = -1;

    foreach ($profiles as $i => $p) {
        if ($p['id'] === $profile_id) {
            $found = true;
            $profile = $p;
            $index = $i;
            break;
        }
    }

    if (!$found) {
        wp_send_json_error(['message' => 'Profil nem található.']);
    }

    return [$profiles, $profile, $index];
}

// ─── Profil törlése ──────────────────────────────

add_action('wp_ajax_pp_delete_profile', 'pp_ajax_delete_profile');

function pp_ajax_delete_profile() {
    $user_id = pp_ajax_verify_request();
    $profile_id = isset($_POST['profile_id']) ? sanitize_text_field($_POST['profile_id']) : '';

    if (empty($profile_id)) {
        wp_send_json_error(['message' => 'Hiányzó profile_id.']);
    }

    [$profiles] = pp_ajax_get_profile($user_id, $profile_id);

    $filtered = array_values(array_filter($profiles, function($p) use ($profile_id) {
        return $p['id'] !== $profile_id;
    }));

    update_user_meta($user_id, 'pp_profiles', $filtered);
    wp_send_json_success(['message' => 'Profil törölve.', 'profiles' => $filtered]);
}

// ─── Kedvencek törlése egy profilból ────────────

add_action('wp_ajax_pp_clear_favorites', 'pp_ajax_clear_favorites');

function pp_ajax_clear_favorites() {
    $user_id = pp_ajax_verify_request();
    $profile_id = isset($_POST['profile_id']) ? sanitize_text_field($_POST['profile_id']) : '';

    if (empty($profile_id)) {
        wp_send_json_error(['message' => 'Hiányzó profile_id.']);
    }

    [$profiles, $profile, $index] = pp_ajax_get_profile($user_id, $profile_id);

    $profiles[$index]['favorites'] = [];
    update_user_meta($user_id, 'pp_profiles', $profiles);
    wp_send_json_success(['message' => 'Kedvencek törölve.', 'fav_count' => 0]);
}

// ─── Megnézendők törlése egy profilból ──────────

add_action('wp_ajax_pp_clear_watch_later', 'pp_ajax_clear_watch_later');

function pp_ajax_clear_watch_later() {
    $user_id = pp_ajax_verify_request();
    $profile_id = isset($_POST['profile_id']) ? sanitize_text_field($_POST['profile_id']) : '';

    if (empty($profile_id)) {
        wp_send_json_error(['message' => 'Hiányzó profile_id.']);
    }

    [$profiles, $profile, $index] = pp_ajax_get_profile($user_id, $profile_id);

    $profiles[$index]['watch_later'] = [];
    update_user_meta($user_id, 'pp_profiles', $profiles);
    wp_send_json_success(['message' => 'Megnézendők törölve.', 'wl_count' => 0]);
}
