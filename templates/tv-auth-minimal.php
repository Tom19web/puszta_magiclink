<?php if (!defined('ABSPATH')) exit;

/**
 * PusztaPlay TV Hitelesítés — Minimal Auth Oldal
 * NINCS WordPress fejléc, lábléc, menü, külső link.
 * Csak 4 állapot: email űrlap → link elküldve → jóváhagyás → sikeres.
 */

$code = sanitize_text_field($_GET['pp_tv'] ?? '');
$transient_key = 'pp_tv_' . $code;
$data = get_transient($transient_key);

// State detection
$state = 'form'; // default: show email form
if ($data && $data['status'] === 'authenticated') {
    $state = 'success';
} elseif (is_user_logged_in()) {
    $state = 'confirm';
} elseif (isset($_POST['pp_tv_email']) && !empty($_POST['pp_tv_email'])) {
    // Email submitted — process
    $email = sanitize_email($_POST['pp_tv_email']);
    if (is_email($email)) {
        $redirect_to = add_query_arg('pp_tv', $code);
        $result = pp_generate_and_send_magic_link($email, $redirect_to);
        $state = is_wp_error($result) ? 'error' : 'sent';
    } else {
        $state = 'invalid_email';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="referrer" content="no-referrer">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>PusztaPlayer — TV Hitelesítés</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: system-ui, -apple-system, sans-serif;
      background: #0a0a0a;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .pp-card {
      background: #141414;
      border: 2px solid #000;
      border-radius: 18px;
      box-shadow: 8px 8px 0 #000;
      max-width: 420px;
      width: 100%;
      padding: 36px 28px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .pp-title { color: #f6c800; font-size: 28px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px; }
    .pp-subtitle { color: #555; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; }
    .pp-divider { height: 2px; background: #1a1a1a; margin: 0 -28px 20px; }
    .pp-code {
      display: inline-block;
      background: #f6c800;
      border: 3px solid #000;
      border-radius: 10px;
      padding: 12px 24px;
      font-size: 28px;
      font-weight: 900;
      letter-spacing: 4px;
      color: #000;
      margin-bottom: 20px;
      box-shadow: 4px 4px 0 #000;
      font-family: monospace;
    }
    .pp-emoji { font-size: 52px; margin-bottom: 12px; }
    .pp-text { color: #888; font-size: 14px; margin-bottom: 8px; line-height: 1.5; }
    .pp-text-bold { color: #fff; font-weight: 700; }
    .pp-input {
      display: block;
      width: 100%;
      background: #0d0d0d;
      border: 2px solid #1a1a1a;
      border-radius: 8px;
      padding: 14px 16px;
      color: #fff;
      font-size: 15px;
      font-family: inherit;
      margin-bottom: 12px;
      outline: none;
      transition: border-color 0.2s;
    }
    .pp-input:focus { border-color: #f6c800; }
    .pp-btn {
      display: inline-block;
      width: 100%;
      padding: 14px;
      background: #f6c800;
      color: #000;
      font-size: 15px;
      font-weight: 900;
      text-transform: uppercase;
      letter-spacing: 1px;
      border: 3px solid #000;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 5px 5px 0 #000;
      transition: all 0.1s ease;
      text-decoration: none;
      font-family: inherit;
    }
    .pp-btn:active { transform: translate(3px, 3px); box-shadow: 2px 2px 0 #000; }
    .pp-link {
      color: #888;
      font-size: 13px;
      margin-top: 16px;
      text-decoration: none;
    }
    .pp-hint { color: #555; font-size: 11px; margin-top: 8px; }
    .pp-error { color: #ff4d57; font-size: 13px; font-weight: 700; margin-bottom: 12px; }
    .pp-spacer { height: 8px; }
  </style>
</head>
<body>
  <div class="pp-card">

    <?php if ($state === 'success'): ?>
      <div class="pp-emoji">✅</div>
      <div class="pp-title" style="margin-bottom:12px;">SIKER!</div>
      <div class="pp-text">A TV-d már használhatja a PusztaPlay-t.</div>
      <div class="pp-spacer"></div>
      <div class="pp-code">✔️</div>
      <div class="pp-hint">Térj vissza a TV-hez — az alkalmazás automatikusan bejelentkezik.</div>

    <?php elseif ($state === 'confirm'): ?>
      <div class="pp-emoji">📺</div>
      <div class="pp-title">TV BEJELENTKEZÉS</div>
      <div class="pp-subtitle">HAGYD JÓVÁ A TV-NEK</div>
      <div class="pp-divider"></div>
      <div class="pp-text" style="margin-bottom:16px;">Kattints a gombra, hogy jóváhagyd a bejelentkezést a TV-d számára.</div>
      <div class="pp-code"><?php echo esc_html($code); ?></div>
      <a href="<?php echo esc_url(add_query_arg(['pp_tv' => $code, 'pp_tv_confirm' => '1'])); ?>" class="pp-btn">✅ JÓVÁHAGYOM</a>
      <div class="pp-hint">Senkinek ne add meg ezt a kódot!</div>

    <?php elseif ($state === 'sent'): ?>
      <div class="pp-emoji">✉️</div>
      <div class="pp-title">LINK ELKÜLDVE</div>
      <div class="pp-subtitle">ELLENŐRIZD AZ EMAILED</div>
      <div class="pp-divider"></div>
      <div class="pp-text">Belépési linket küldtünk a megadott e-mail címre.</div>
      <div class="pp-spacer"></div>
      <div class="pp-text">Kattints a linkre a leveledben, majd térj vissza ide — a jóváhagyás után a TV automatikusan bejelentkezik.</div>
      <div class="pp-hint" style="margin-top:16px;">Nem kaptál e-mailt? Ellenőrizd a spam mappát.</div>

    <?php elseif ($state === 'error'): ?>
      <div class="pp-emoji">⚠️</div>
      <div class="pp-title">HIBA</div>
      <div class="pp-subtitle">PRÓBÁLD ÚJRA</div>
      <div class="pp-divider"></div>
      <div class="pp-error">Ezzel az e-mail címmel nincs regisztrált fiók.</div>
      <div class="pp-hint" style="margin-top:8px;">Használd a PusztaPlay regisztrációkor megadott e-mail címet.</div>

    <?php else: ?>
      <div class="pp-emoji">📱</div>
      <div class="pp-title">PUSZTAPLAYER</div>
      <div class="pp-subtitle">TV HITELESÍTÉS</div>
      <div class="pp-divider"></div>
      <div class="pp-code"><?php echo esc_html($code); ?></div>
      <div class="pp-text" style="margin-bottom:16px;">Hasonlítsd össze a <span class="pp-text-bold">TV képernyőjén</span> látható kóddal, majd jelentkezz be.</div>
      <form method="post" action="">
        <?php if (isset($code)): ?>
          <input type="hidden" name="pp_tv" value="<?php echo esc_attr($code); ?>">
        <?php endif; ?>
        <input type="email" name="pp_tv_email" class="pp-input" placeholder="E-mail címed" required autocomplete="email" inputmode="email">
        <button type="submit" class="pp-btn">TOVÁBB</button>
      </form>
    <?php endif; ?>

  </div>
</body>
</html>
