<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kijelentkezés...</title>
    <meta http-equiv="refresh" content="3;url=<?php echo esc_url(home_url('/')); ?>">
    <style>
        body { font-family: sans-serif; background-color: #f4f5f7; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .logout-box { background: #fff; padding: 40px 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center; max-width: 400px; border-top: 5px solid #2271b1; width: 90%; }
        .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #2271b1; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; margin: 0 auto 20px auto; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="logout-box">
        <div class="spinner"></div>
        <h2 style="color:#1d2327; margin-top:0;">Sikeresen kijelentkeztél!</h2>
        <p style="color:#50575e; margin-bottom:0;">Néhány pillanat és átirányítunk a nyers valóságba...</p>
    </div>
    <script>setTimeout(function(){ window.location.href = "<?php echo esc_url(home_url('/')); ?>"; }, 3000);</script>
</body>
</html>