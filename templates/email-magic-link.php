<?php if (!defined('ABSPATH')) exit; ?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Helvetica, Arial, sans-serif; background-color: #f4f5f7; padding: 40px 20px; margin: 0;">
    <div style="max-width: 500px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; padding: 40px 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center;">
        
        <?php if (!empty($logo_url)): ?>
            <img src="<?php echo esc_url($logo_url); ?>" alt="PusztaPlay" style="max-height: 50px; display: block; margin: 0 auto;">
        <?php else: ?>
            <h1 style="color: #1d2327; margin-top: 0; font-size: 28px; letter-spacing: -0.5px;">PusztaPlay</h1>
        <?php endif; ?>

        <p style="color: #50575e; font-size: 16px; line-height: 1.6; margin-top: 30px; margin-bottom: 35px;">
            Szia <strong><?php echo esc_html($user->display_name); ?></strong>!<br><br>
            Kaptunk egy kérést, hogy szeretnél bejelentkezni a fiókodba. Kattints az alábbi gombra az azonnali belépéshez:
        </p>
        <a href="<?php echo esc_url($magic_link); ?>" style="display: inline-block; background-color: #2271b1; color: #ffffff; text-decoration: none; font-size: 16px; font-weight: bold; padding: 14px 35px; border-radius: 6px; letter-spacing: 0.5px;">
            Bejelentkezés
        </a>
        <p style="color: #8c8f94; font-size: 13px; margin-top: 35px; line-height: 1.5;">
            Ez a biztonsági link <strong>15 percig</strong> érvényes, utána a semmibe vész.<br>
        </p>
        <hr style="border: none; border-top: 1px solid #e2e4e7; margin: 30px 0;">
        <p style="color: #a7aaad; font-size: 12px; margin: 0;">&copy; <?php echo date('Y'); ?> PusztaPlay.</p>
    </div>
</body>
</html>