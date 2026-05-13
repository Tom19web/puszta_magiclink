<?php
/**
 * PusztaPlay Auth + CRM — Előfizetés Lejárati Emlékeztető Cron
 * Naponta ellenőrzi a lejáró előfizetéseket és e-mailt küld.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Cron esemény regisztrálása
 */
function pp_reminder_schedule_event() {
    if (!wp_next_scheduled('pp_daily_reminder_check')) {
        wp_schedule_event(time(), 'daily', 'pp_daily_reminder_check');
    }
}

function pp_reminder_clear_schedule() {
    wp_clear_scheduled_hook('pp_daily_reminder_check');
}

// Regisztrálás aktiváláskor (a fő plugin fájl hívja)
// pp_reminder_schedule_event() — register_activation_hook
// pp_reminder_clear_schedule() — register_deactivation_hook

/**
 * A napi ellenőrző — végigmegy az összes useren
 */
add_action('pp_daily_reminder_check', 'pp_run_expiry_reminders');

function pp_run_expiry_reminders() {
    $options = get_option('pp_smtp_settings');
    if (empty($options['reminder_enabled'])) {
        return; // kikapcsolva — csendben kisétálunk
    }

    $days_before = isset($options['reminder_days_before']) ? (int) $options['reminder_days_before'] : 7;
    if ($days_before < 1) $days_before = 7;

    $subject = isset($options['reminder_subject']) && !empty($options['reminder_subject'])
        ? $options['reminder_subject']
        : 'A PusztaPlay előfizetésed hamarosan lejár';

    $body_template = isset($options['reminder_body']) && !empty($options['reminder_body'])
        ? $options['reminder_body']
        : '';

    if (empty($body_template)) return;

    $target_timestamp = strtotime("+{$days_before} days");
    $target_day_start = strtotime(date('Y-m-d 00:00:00', $target_timestamp));
    $target_day_end   = strtotime(date('Y-m-d 23:59:59', $target_timestamp));

    $dashboard_url = home_url('/vezerlopult/');

    // Végigmegyünk az összes useren, akiknek van pp_subscription_end meta
    $users = get_users([
        'meta_key'     => 'pp_subscription_end',
        'meta_compare' => 'EXISTS',
        'number'       => -1,
    ]);

    foreach ($users as $user) {
        $sub_end = (int) get_user_meta($user->ID, 'pp_subscription_end', true);
        if (!$sub_end) continue;

        // Csak azt a felhasználót értesítjük, akinek pont ezen a napon jár le (target window)
        if ($sub_end < $target_day_start || $sub_end > $target_day_end) continue;

        // Duplikáció védelem: egy lejárati időpontra csak egyszer küldünk
        $sent_key = 'pp_reminder_sent_' . $user->ID . '_' . $sub_end;
        if (get_user_meta($user->ID, $sent_key, true)) continue;

        $package     = get_user_meta($user->ID, 'pp_subscription_package', true) ?: 'Nincs megadva';
        $expiry_date = date_i18n('Y. F j.', $sub_end);
        $days_left   = (int) ceil(($sub_end - time()) / DAY_IN_SECONDS);
        if ($days_left < 0) $days_left = 0;

        $name  = !empty($user->display_name) ? $user->display_name : $user->user_email;
        $email = $user->user_email;

        // Helykitöltők cseréje
        $body = str_replace(
            ['{name}', '{email}', '{package}', '{expiry_date}', '{days_left}', '{dashboard_url}'],
            [esc_html($name), esc_html($email), esc_html($package), esc_html($expiry_date), (string) $days_left, esc_url($dashboard_url)],
            $body_template
        );

        $headers = ['Content-Type: text/html; charset=UTF-8'];

        $sent = wp_mail($email, $subject, $body, $headers);

        if ($sent) {
            update_user_meta($user->ID, $sent_key, time());
            pp_magic_log('reminder_sent', [
                'user_id'      => $user->ID,
                'email'        => $email,
                'expiry'       => $expiry_date,
                'days_left'    => $days_left,
            ]);
        }
    }
}

/**
 * Manuális teszt indítás — admin kérésre (add_query_arg)
 */
function pp_reminder_manual_test() {
    if (!current_user_can('manage_options')) return;
    if (!isset($_GET['pp_test_reminder'])) return;
    if (!wp_verify_nonce($_GET['_wpnonce'] ?? '', 'pp_test_reminder_nonce')) return;

    $user_id = absint($_GET['pp_test_reminder']);
    if (!$user_id) return;

    // Use a transient lock instead of temporarily modifying global options
    $lock = get_transient('pp_reminder_test_lock');
    if ($lock) {
        echo '<div class="notice notice-warning"><p>Egy másik teszt már fut. Várj pár másodpercet.</p></div>';
        return;
    }
    set_transient('pp_reminder_test_lock', true, 30);

    // Override the enabled check by temporarily filtering the option
    add_filter('pre_option_pp_smtp_settings', function($false) use ($user_id) {
        global $wpdb;
        $options = get_option('pp_smtp_settings');
        if (!is_array($options)) $options = [];
        $options['reminder_enabled'] = 1;
        return $options;
    });

    pp_run_expiry_reminders();

    remove_all_filters('pre_option_pp_smtp_settings');
    delete_transient('pp_reminder_test_lock');

    echo '<div class="notice notice-success"><p>Emlékeztető teszt lefutott. Ellenőrizd a felhasználó e-mail fiókját.</p></div>';
}
add_action('admin_notices', 'pp_reminder_manual_test');
