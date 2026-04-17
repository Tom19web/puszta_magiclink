<?php
/**
 * PusztaPlay Magic Login - Új Tag (Mini CRM)
 * Itt osztjuk a kegyet és a VIP csomagokat a halandóknak.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Regisztráljuk a menüpontot a WordPress adminban
add_action('admin_menu', 'pp_register_new_member_page');
function pp_register_new_member_page() {
    add_users_page('Új PusztaPlay Tag', 'Új PusztaPlay Tag', 'create_users', 'pp-new-member', 'pp_render_new_member_page');
}

// A feldolgozó logika és a nézet betöltése
function pp_render_new_member_page() {
    // Ha egy alantas lény tévedne ide
    if (!current_user_can('create_users')) {
        wp_die('Ehhez a varázslathoz nincs elég manád, pórnép!');
    }

    $message = '';

    // Űrlap feldolgozása, ha az elküldés gombra tenyereltek
    if (isset($_POST['pp_new_member_submit'])) {
        
        // 1. A KÖTELEZŐ PAJZS: Nonce ellenőrzés! (Tanuld meg egy életre!)
        if (!isset($_POST['pp_new_member_nonce']) || !wp_verify_nonce($_POST['pp_new_member_nonce'], 'pp_create_member_action')) {
            $message = '<div class="notice notice-error"><p>Biztonsági hiba! Hiányzó vagy érvénytelen nonce. Csak nem trükközni próbálsz?</p></div>';
        } else {
            // Adatok elegáns fertőtlenítése
            $email     = sanitize_email($_POST['email']);
            $nickname  = sanitize_text_field($_POST['nickname']);
            $package   = sanitize_text_field($_POST['package']);
            $expiry    = sanitize_text_field($_POST['expiry']);
            $client_id = sanitize_text_field($_POST['client_id']);

            if (!is_email($email)) {
                $message = '<div class="notice notice-error"><p>Érvénytelen e-mail cím! Talán ellenőrizd, hova pötyögsz.</p></div>';
            } else {
                $user = get_user_by('email', $email);
                
                if (!$user) {
                    // Új jövevény teremtése
                    $user_id = wp_create_user($email, wp_generate_password(24, false), $email);
                    wp_new_user_notification($user_id, null, 'user');
                    $message = '<div class="notice notice-success"><p>Fenséges! Az új tag megszületett, a hírnök elrepült az e-maillel.</p></div>';
                } else {
                    // Már létező lény adatainak felülírása
                    $user_id = $user->ID;
                    $message = '<div class="notice notice-info"><p>Ez a halandó már létezett, de az adatait kegyesen felülírtuk.</p></div>';
                }

                // Ha nem történt katasztrófa, mentjük a metaadatokat
                if (!is_wp_error($user_id)) {
                    if (!empty($nickname)) {
                        update_user_meta($user_id, 'nickname', $nickname);
                        wp_update_user(['ID' => $user_id, 'display_name' => $nickname]);
                    }
                    
                    update_user_meta($user_id, 'pp_subscription_package', $package);
                    update_user_meta($user_id, 'pp_client_id', $client_id);
                    
                    if (!empty($expiry)) {
                        update_user_meta($user_id, 'pp_subscription_end', strtotime($expiry . ' 23:59:59'));
                    } else {
                        delete_user_meta($user_id, 'pp_subscription_end');
                    }
                }
            }
        }
    }

    // Csomagok előkészítése a sablon számára
    $options = get_option('pp_smtp_settings');
    $packages_string = !empty($options['packages']) ? $options['packages'] : 'Alap Csomag, Prémium Csomag, VIP Csomag';
    $packages_array = array_map('trim', explode(',', $packages_string));

    // 2. AZ ELEGÁNS MEGJELENÍTÉS: Sablon behúzása
    $template_path = PP_MAGIC_DIR . 'templates/admin-new-member.php';
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<div class="wrap"><h1>Katasztrófa!</h1><p>Elfelejtetted létrehozni a <code>templates/admin-new-member.php</code> fájlt, te hanyag kis teremtés!</p></div>';
    }
}