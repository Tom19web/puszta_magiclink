<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TV bejelentkezés — PusztaPlay</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: system-ui, -apple-system, sans-serif;
      background: #f4f5f7;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    .pp-tv-card {
      background: #fff;
      border: 4px solid #1d2327;
      border-radius: 16px;
      box-shadow: 10px 10px 0 #1d2327;
      max-width: 420px;
      width: 100%;
      padding: 40px 28px;
      text-align: center;
    }
    .pp-tv-card h2 {
      font-size: 24px;
      font-weight: 900;
      text-transform: uppercase;
      color: #1d2327;
      margin-bottom: 12px;
    }
    .pp-tv-card p {
      font-size: 15px;
      color: #555;
      font-weight: 600;
      margin-bottom: 28px;
      line-height: 1.5;
    }
    .pp-tv-card .pp-tv-code {
      display: inline-block;
      background: #ffcc00;
      border: 3px solid #1d2327;
      border-radius: 10px;
      padding: 12px 24px;
      font-size: 28px;
      font-weight: 900;
      letter-spacing: 4px;
      color: #1d2327;
      margin-bottom: 28px;
      box-shadow: 4px 4px 0 #1d2327;
    }
    .pp-tv-card .pp-btn-primary {
      display: inline-block;
      width: 100%;
      padding: 16px;
      background: #2271b1;
      color: #fff;
      font-size: 18px;
      font-weight: 900;
      text-transform: uppercase;
      border: 3px solid #1d2327;
      border-radius: 10px;
      cursor: pointer;
      box-shadow: 5px 5px 0 #1d2327;
      transition: all 0.1s ease;
      text-decoration: none;
    }
    .pp-tv-card .pp-btn-primary:active {
      transform: translate(3px, 3px);
      box-shadow: 2px 2px 0 #1d2327;
    }
    .pp-tv-card .pp-success {
      font-size: 48px;
      margin-bottom: 12px;
    }
    .pp-tv-card .pp-success-text {
      color: #108548;
      font-weight: 900;
      font-size: 20px;
      text-transform: uppercase;
    }
  </style>
</head>
<body>
  <div class="pp-tv-card">
    <?php if (isset($data['status']) && $data['status'] === 'authenticated'): ?>
      <div class="pp-success">✅</div>
      <h2>Siker!</h2>
      <p>A TV-d már használhatja a PusztaPlay-t. Térj vissza a TV-hez!</p>
      <div class="pp-tv-code">✔️</div>
      <p style="font-size:13px;color:#8c8f94;">Ha nem történik semmi, indítsd újra a TV alkalmazást.</p>
    <?php else: ?>
      <h2>📺 TV bejelentkezés</h2>
      <p>Kattints a gombra, hogy jóváhagyd a bejelentkezést a TV-d számára.</p>
      <div class="pp-tv-code"><?php echo esc_html($code); ?></div>
      <a href="<?php echo esc_url($confirm_link); ?>" class="pp-btn-primary">✅ Jóváhagyom</a>
      <p style="margin-top:20px;font-size:13px;color:#8c8f94;">Senkinek ne add meg ezt a kódot!</p>
    <?php endif; ?>
  </div>
</body>
</html>
