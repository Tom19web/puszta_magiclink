<?php if (!defined('ABSPATH')) exit; ?>
<div class="pp-magic-login-box" style="border: 4px solid #1d2327; padding: 30px 20px; border-radius: 15px; box-shadow: 10px 10px 0px #1d2327; background: #ffffff; max-width: 400px; margin: 20px auto; font-family: inherit;">
    <div style="text-align:center; margin-bottom: 20px;">
        <h3 style="margin: 0; font-weight: 900; text-transform: uppercase; font-size: 26px; color: #1d2327;">Jelentkezz be!</h3>
        <p style="margin: 5px 0 0 0; font-size: 15px; font-weight: bold; color: #555;">Nincs jelszó, csak mágia. 🪄</p>
    </div>
    <form method="post" style="margin: 0;">
        <?php $rd = isset($_REQUEST['redirect_to']) ? esc_url_raw($_REQUEST['redirect_to']) : ''; if ($rd): ?>
        <input type="hidden" name="redirect_to" value="<?php echo esc_attr($rd); ?>">
        <?php endif; ?>
        <div style="position: absolute; left: -9999px; top: -9999px;" aria-hidden="true">
            <input type="text" name="pp_website_url_catch" tabindex="-1" autocomplete="off">
        </div>
        
        <label for="pp_magic_email" style="font-weight:900; text-transform: uppercase; font-size: 14px; color: #1d2327; display: block; margin-bottom: 8px;">E-mail címed:</label>
        <input type="email" name="pp_magic_email" id="pp_magic_email" placeholder="batman@pusztaplay.eu" required style="width: 100%; box-sizing: border-box; padding: 15px; border: 3px solid #1d2327; border-radius: 10px; font-size: 16px; font-weight: bold; margin-bottom: 20px; outline: none; box-shadow: inset 2px 2px 0px rgba(0,0,0,0.1); font-family: inherit;">
        
        <button type="submit" style="width: 100%; padding: 15px; background: #ffcc00; color: #1d2327; font-size: 18px; font-weight: 900; text-transform: uppercase; border: 3px solid #1d2327; border-radius: 10px; cursor: pointer; box-shadow: 5px 5px 0px #1d2327; transition: all 0.1s ease; font-family: inherit;">Küldd a Linket! 🚀</button>
    </form>
</div>