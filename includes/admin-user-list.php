<?php
/**
 * PusztaPlay Magic Login - Felhasználói Lista (Admin)
 * Ahol a plebset rangsorolod, szűröd és megítéled.
 */

if (!defined('ABSPATH')) {
    exit;
}

// 1. Az új, fenséges oszlopok regisztrálása
add_filter('manage_users_columns', 'pp_add_custom_user_columns');

function pp_add_custom_user_columns($columns) {
    $new_columns = array();

    foreach ($columns as $key => $label) {
        $new_columns[$key] = $label;

        // Bepofátlankodunk a mi értékes adatainkkal a Név oszlop után
        if ($key === 'name') {
            $new_columns['pp_client_id']            = 'Ügyfélkód';
            $new_columns['pp_subscription_package'] = 'Csomag';
            $new_columns['pp_subscription_end']     = 'Lejárat';
            $new_columns['pp_subscription_status']  = 'Státusz';
        }
    }

    return $new_columns;
}

// 2. Az oszlopok megtöltése élettel (és egy csipetnyi tűrt HTML-lel)
add_filter('manage_users_custom_column', 'pp_populate_custom_user_columns', 10, 3);

function pp_populate_custom_user_columns($output, $column_name, $user_id) {
    switch ($column_name) {

        case 'pp_client_id':
            $client_id = get_user_meta($user_id, 'pp_client_id', true);
            return !empty($client_id) ? '<strong style="font-family:monospace; color:#d63638; background:#f0f0f1; padding:2px 6px; border-radius:4px;">' . esc_html($client_id) . '</strong>' : '<span style="color:#8c8f94;">—</span>';

        case 'pp_subscription_package':
            $package = get_user_meta($user_id, 'pp_subscription_package', true);
            return !empty($package) ? '<span style="background:#2271b1; color:#fff; padding:3px 8px; border-radius:4px; font-size:12px; font-weight:bold;">' . esc_html($package) . '</span>' : '<span style="color:#8c8f94;">—</span>';

        case 'pp_subscription_end':
            $timestamp = get_user_meta($user_id, 'pp_subscription_end', true);
            return !empty($timestamp) ? esc_html(date_i18n('Y. m. d.', (int) $timestamp)) : '<span style="color:#8c8f94;">—</span>';

        case 'pp_subscription_status':
            $timestamp = get_user_meta($user_id, 'pp_subscription_end', true);
            
            if (empty($timestamp)) {
                return '<span style="color:#f56e28; font-weight:bold;">Inaktív</span>';
            }
            if (time() > (int) $timestamp) {
                return '<span style="color:#d63638; font-weight:bold;">Lejárt</span>';
            }
            return '<span style="color:#108548; font-weight:bold;">Aktív</span>';
    }

    return $output;
}

// 3. Tegyük rendezhetővé az oszlopokat, hogy kényed-kedved szerint rángathasd őket!
add_filter('manage_users_sortable_columns', 'pp_make_custom_user_columns_sortable');

function pp_make_custom_user_columns_sortable($columns) {
    $columns['pp_client_id']            = 'pp_client_id';
    $columns['pp_subscription_package'] = 'pp_subscription_package';
    $columns['pp_subscription_end']     = 'pp_subscription_end';
    
    return $columns;
}

// 4. A szortírozás sötét mágiája (Az adatbázis-lekérdezés mesteri eltérítése)
add_action('pre_get_users', 'pp_sort_custom_user_columns');

function pp_sort_custom_user_columns($query) {
    global $pagenow;

    // Ha nem az adminban vagyunk, és nem a felhasználók listáján, azonnal visszavonulunk
    if (!is_admin() || $pagenow !== 'users.php') {
        return;
    }

    $orderby = $query->get('orderby');

    if ($orderby === 'pp_client_id') {
        $query->set('meta_key', 'pp_client_id');
        $query->set('orderby', 'meta_value');
    }

    if ($orderby === 'pp_subscription_package') {
        $query->set('meta_key', 'pp_subscription_package');
        $query->set('orderby', 'meta_value');
    }

    if ($orderby === 'pp_subscription_end') {
        $query->set('meta_key', 'pp_subscription_end');
        // Külön figyelem: meta_value_num kell, hiszen a timestamp egy szám! Ebbe régen biztos beletört a fogad.
        $query->set('orderby', 'meta_value_num'); 
    }
}