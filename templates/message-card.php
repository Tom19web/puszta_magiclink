<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Belépés megerősítése 🪄</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: system-ui, sans-serif; background: #f4f5f7; margin: 0; padding: 60px; text-align: center; max-width: 500px; margin: auto; }
        h1 { font-size: 2.5em; color: #1d2327; margin-bottom: 20px; }
        p { font-size: 18px; color: #50575e; margin: 30px 0; }
        a { display: inline-block; padding: 18px 40px; background: #2271b1; color: white; text-decoration: none; border-radius: 10px; font-weight: 700; font-size: 18px; box-shadow: 0 6px 20px rgba(34,113,177,.3); transition: all 0.2s; }
        a:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(34,113,177,.4); }
    </style>
</head>
<body>
    <h1>✅ Link érvényes!</h1>
    <p>Túlélted a biztonsági ellenőrzést. Kattints a gombra a diadalmas belépéshez:</p>
    <a href="<?php echo esc_url($confirm_link); ?>">Belépek most! 🚀</a>
</body>
</html>