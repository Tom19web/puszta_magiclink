<?php if (!defined('ABSPATH')) exit; ?>
<div class="pp-dashboard-container pp-profile-manager" style="max-width: 600px; margin: 20px auto 0; padding: 30px; border: 1px solid #e2e4e7; border-radius: 12px; background: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <h3 style="margin-top:0; margin-bottom:15px; color:#1d2327;">? Profilok kezel&eacute;se</h3>
    <p style="color:#50575e; margin-bottom:20px; font-size:13px;">Itt t&ouml;r&ouml;lheted a profiljaidat &eacute;s a hozz&aacute;juk tartoz&oacute; kedvenceket.</p>

    <input type="hidden" id="pp_profile_nonce" value="<?php echo esc_attr($nonce); ?>">
    <input type="hidden" id="pp_ajax_url" value="<?php echo esc_url($ajax_url); ?>">

    <?php if (empty($profiles)): ?>
        <div style="background:#f6f7f7; padding:20px; border-radius:6px; text-align:center;">
            <p style="margin:0; color:#8c8f94;">M&eacute;g nincsenek profilok l&eacute;trehozva.</p>
        </div>
    <?php else: ?>
        <div id="pp-profile-list">
            <?php foreach ($profiles as $index => $profile): 
                $name     = esc_html($profile['name'] ?? 'Profil');
                $color    = esc_attr($profile['color'] ?? '#ffcc00');
                $avatar   = esc_html($profile['avatar'] ?? '?');
                $pid      = esc_attr($profile['id'] ?? '');
                $favCount = isset($profile['favorites']) && is_array($profile['favorites']) ? count($profile['favorites']) : 0;
                $wlCount  = isset($profile['watch_later']) && is_array($profile['watch_later']) ? count($profile['watch_later']) : 0;
            ?>
            <div class="pp-profile-card" data-profile-id="<?php echo $pid; ?>" style="display:flex; align-items:center; gap:15px; background:#f6f7f7; padding:15px; border-radius:8px; margin-bottom:12px;">
                <div style="width:48px; height:48px; border-radius:50%; background:<?php echo $color; ?>; display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0;">
                    <?php echo $avatar; ?>
                </div>
                <div style="flex:1; min-width:0;">
                    <strong style="font-size:16px; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo $name; ?></strong>
                    <span style="font-size:12px; color:#8c8f94;">
                        ? <span class="pp-fav-count"><?php echo (int) $favCount; ?></span> kedvenc &nbsp;|&nbsp;
                        ? <span class="pp-wl-count"><?php echo (int) $wlCount; ?></span> megn&eacute;zend?
                    </span>
                </div>
                <div style="display:flex; flex-direction:column; gap:6px; flex-shrink:0;">
                    <button class="pp-btn pp-btn-clear-favs" data-action="clear_favorites" data-pid="<?php echo $pid; ?>" style="cursor:pointer; background:#f0f0f1; border:1px solid #c3c4c7; border-radius:4px; padding:4px 10px; font-size:12px; white-space:nowrap;">Kedvencek t&ouml;rl&eacute;se</button>
                    <button class="pp-btn pp-btn-clear-wl" data-action="clear_watch_later" data-pid="<?php echo $pid; ?>" style="cursor:pointer; background:#f0f0f1; border:1px solid #c3c4c7; border-radius:4px; padding:4px 10px; font-size:12px; white-space:nowrap;">Megn&eacute;zend?k t&ouml;rl&eacute;se</button>
                    <button class="pp-btn pp-btn-delete" data-action="delete_profile" data-pid="<?php echo $pid; ?>" style="cursor:pointer; background:#d63638; color:#fff; border:none; border-radius:4px; padding:4px 10px; font-size:12px; white-space:nowrap; font-weight:bold;">Profil t&ouml;rl&eacute;se</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div id="pp-profile-message" style="display:none; margin-top:12px; padding:10px 14px; border-radius:4px;"></div>
    <?php endif; ?>
</div>
