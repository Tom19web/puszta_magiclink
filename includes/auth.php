<?php
/**
 * PusztaPlay Magic Login - Autentikációs Logika
 * Itt történik a varázslat. Szigorúan, golyóállóan, elegánsan, RAJOSAN!!! XDD
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Varázs-link generálása és HTML E-mail kiküldése
 */
function pp_generate_and_send_magic_link($email) {
    $user = get_user_by('email', $email);
    if (!$user) {
        return new WP_Error('no_user', 'Ezzel az e-mail címmel nincs regisztrált felhasználó. Próbálkozz máshol, halandó!');
    }

    // 1. A BIZTONSÁG OLTÁRA: Token generálás és HASH-elés
    $raw_token = wp_generate_password(32, false);
    $hashed_token = wp_hash($raw_token); // Soha nem mentünk nyers tokent!
    
    // Transient használata a biztonságos tároláshoz a hashelt verzióval
    set_transient('pp_magic_token_' . $hashed_token, $user->ID, 15 * MINUTE_IN_SECONDS);
    
    // A felhasználónak kiküldött link viszont a nyers tokent tartalmazza
    $magic_link = add_query_arg('pp_magic_token', $raw_token, home_url('/'));

    // Bárhol definiálhatjuk az e-mailhez szükséges változókat, a sablon látni fogja őket
    $options = get_option('pp_smtp_settings');
    $options = is_array($options) ? $options : array();
    $logo_url = !empty($options['logo_url']) ? esc_url($options['logo_url']) : '';
    
    // 2. AZ ELEGANCIA: HTML Template betöltése Output Buffering segítségével
    ob_start();
    include PP_MAGIC_DIR . 'templates/email-magic-link.php';
    $message = ob_get_clean();

    $subject = 'Vár a PusztaPlay! Itt a bejelentkezési linked 🚀';
    $headers = array('Content-Type: text/html; charset=UTF-8');
    
    $sent = wp_mail($email, $subject, $message, $headers);

    if (!$sent) {
        return new WP_Error('mail_failed', 'Hiba történt az e-mail küldésekor. A szervered elvérzett a feladatban.');
    }

    return true;
}

/**
 * A beérkező link ellenőrzése és a diadalmas beléptetés
 */
add_action('init', 'pp_verify_magic_link');

function pp_verify_magic_link() {
    if (isset($_GET['pp_magic_token'])) {
        $raw_token = sanitize_text_field($_GET['pp_magic_token']);
        
        // Visszafejtjük a hashelt formát, hogy ellenőrizhessük a transient-et
        $hashed_token = wp_hash($raw_token);
        $user_id = get_transient('pp_magic_token_' . $hashed_token);
        
        // Linkszkenner védelem: A "Kattints a belépéshez" köztes állomás
        if (!isset($_GET['pp_confirm']) && $user_id) {
            ob_start();
            $confirm_link = add_query_arg(['pp_magic_token' => $raw_token, 'pp_confirm' => '1']);
            include PP_MAGIC_DIR . 'templates/message-card.php'; // Ide jön az a kártya, ami eddig a stringben rohadt
            echo ob_get_clean();
            exit;
        }

        // Sikeres megerősítés esetén
        if ($user_id) {
            // A token azonnali megsemmisítése, hogy ne lehessen újra felhasználni
            delete_transient('pp_magic_token_' . $hashed_token);
            
            // Fenséges beléptetés
            wp_clear_auth_cookie();
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            
            // Elegáns átirányítás a vezérlőpultra (vagy egy splash screen sablon betöltése)
            wp_safe_redirect(home_url('/vezerlopult/'));
            exit;
        } else {
            // Ha a link lejárt, vagy valaki okoskodni próbált
            wp_redirect(home_url('/belepes/?error=invalid'));
            exit;
        }
    }
}