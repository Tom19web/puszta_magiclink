<?php if (!defined('ABSPATH')) exit; ?>

<?php if ($message === 'success'): ?>
    <div class="pp-magic-login-box" style="border: 4px solid #1d2327; padding: 40px 20px; border-radius: 15px; box-shadow: 10px 10px 0px #1d2327; background: #ffffff; max-width: 400px; text-align: center; margin: 20px auto; position: relative; overflow: hidden; font-family: inherit;">
        <div style="font-size: 60px; margin-bottom: 10px; line-height: 1; transform: rotate(-10deg);">💌💨</div>
        <h3 style="color:#1d2327; font-weight: 900; text-transform: uppercase; font-size: 28px; margin-top: 0; margin-bottom: 15px;">BUMM! Úton van!</h3>
        <p style="color:#1d2327; font-size: 16px; font-weight: bold; margin-bottom: 25px;">Kilőttük a kőkemény belépő linket ide:<br>
            <span style="display:inline-block; margin-top:10px; padding: 5px 10px; background: #ffcc00; border: 3px solid #1d2327; border-radius: 5px; transform: rotate(2deg); font-size: 18px; font-weight: 900;"><?php echo esc_html($email); ?></span>
        </p>
        <div style="background: #f6f7f7; padding: 15px; border: 3px solid #1d2327; border-radius: 10px; font-size: 14px; font-weight: bold; color: #1d2327; box-shadow: 4px 4px 0px #1d2327;">
            Csekkold a postafiókod!
        </div>
        <div style="margin-top: 18px;">
            <p style="color:#555; font-size:13px; font-weight:bold; margin: 0 0 10px 0;">Új link kérhető ennyi idő múlva:</p>
            <div id="pp-resend-timer-success" data-remaining="<?php echo esc_attr($remaining); ?>" style="display:inline-block; padding:10px 16px; background:#ffcc00; border:3px solid #1d2327; border-radius:10px; font-size:24px; font-weight:900; color:#1d2327; box-shadow:4px 4px 0px #1d2327;">--:--</div>
        </div>
    </div>

<?php elseif ($message === 'wait'): ?>
    <div class="pp-magic-login-box" style="border: 4px solid #1d2327; padding: 20px; border-radius: 15px; box-shadow: 8px 8px 0px #ffcc00; background: #fff; max-width: 400px; text-align: center; margin: 20px auto;">
        <h3 style="color:#1d2327; font-weight: 900; text-transform: uppercase; margin-top:0; font-size:24px;">⏳ Várj egy kicsit!</h3>
        <p style="font-weight:bold; color: #1d2327; margin-bottom: 12px;">Erre az e-mail címre nemrég már küldtünk egy linket.</p>
        <p style="color:#555; font-weight:bold; margin-bottom: 16px;">Új link kérhető ennyi idő múlva:</p>
        <div id="pp-resend-timer" data-remaining="<?php echo esc_attr($remaining); ?>" style="display:inline-block; padding:10px 16px; background:#ffcc00; border:3px solid #1d2327; border-radius:10px; font-size:24px; font-weight:900; color:#1d2327; box-shadow:4px 4px 0px #1d2327;">--:--</div>
    </div>

<?php elseif ($message === 'blocked'): ?>
    <div class="pp-magic-login-box" style="border: 4px solid #1d2327; padding: 20px; border-radius: 15px; box-shadow: 8px 8px 0px #d63638; background: #fff; max-width: 400px; text-align: center; margin: 20px auto;">
        <h3 style="color:#d63638; font-weight: 900; text-transform: uppercase; margin-top:0; font-size:24px;">🛑 Blokkolva!</h3>
        <p style="font-weight:bold; color: #1d2327;">Túl sok sikertelen próbálkozás. Pihenj 5 percet!</p>
    </div>

<?php elseif ($message === 'not_found'): ?>
    <div style="border: 4px solid #1d2327; padding: 15px; border-radius: 10px; box-shadow: 4px 4px 0px #d63638; background: #fff; max-width: 400px; margin: 0 auto 20px auto;">
        <p style="color:#d63638; font-weight:900; text-transform: uppercase; margin:0 0 5px 0; text-align:center;">Nincs ilyen e-mail cím!</p>
        <p style="text-align:center; font-weight:bold; color:#1d2327; margin:0; font-size:14px;">(Hátralévő próbálkozások: <?php echo esc_html($remaining_try); ?>)</p>
    </div>

<?php elseif ($message === 'error'): ?>
    <div style="border: 4px solid #1d2327; padding: 15px; border-radius: 10px; box-shadow: 4px 4px 0px #d63638; background: #fff; max-width: 400px; margin: 0 auto 20px auto;">
        <p style="color:#d63638; font-weight:900; margin:0; text-align:center;"><?php echo esc_html($error_message); ?></p>
    </div>

<?php elseif ($message === 'success_fake'): ?>
    <div style="border: 4px solid #1d2327; padding: 20px; border-radius: 15px; box-shadow: 8px 8px 0px #1d2327; background: #fff; max-width: 400px; text-align: center; margin: 20px auto;">
        <p style="color:#1d2327; font-weight:900; font-size: 18px; text-transform: uppercase;">A varázs-linket elküldtük!</p>
    </div>
<?php endif; ?>