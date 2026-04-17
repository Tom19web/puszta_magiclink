<?php if (!defined('ABSPATH')) exit; ?>
<div class="pp-magic-login-box" style="border: 4px solid #1d2327; padding: 25px; border-radius: 15px; box-shadow: 8px 8px 0px #1d2327; background: #fff; max-width: 400px; text-align: center; margin: 20px auto; font-family: inherit;">
    <h3 style="margin-top:0; font-weight: 900; text-transform: uppercase; color: #1d2327;">Már bent vagy! 😎</h3>
    <p style="font-weight: bold; color: #555; margin-bottom: 20px;">Nincs szükség varázslatra, már be vagy jelentkezve.</p>

    <a href="<?php echo esc_url($dashboard_url); ?>" style="display: inline-block; width: 100%; box-sizing: border-box; margin-bottom: 12px; padding: 12px 25px; background: #2271b1; color: #ffffff; font-weight: 900; text-decoration: none; border: 3px solid #1d2327; border-radius: 8px; box-shadow: 4px 4px 0px #1d2327; text-transform: uppercase; transition: all 0.1s;">
        Tovább a PusztaPlay Dashboard-re
    </a>

    <a href="<?php echo esc_url($logout_url); ?>" style="display: inline-block; width: 100%; box-sizing: border-box; padding: 12px 25px; background: #ffcc00; color: #1d2327; font-weight: 900; text-decoration: none; border: 3px solid #1d2327; border-radius: 8px; box-shadow: 4px 4px 0px #1d2327; text-transform: uppercase; transition: all 0.1s;">
        Kijelentkezés
    </a>
</div>