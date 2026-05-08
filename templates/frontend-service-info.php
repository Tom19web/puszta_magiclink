<?php if (!defined('ABSPATH')) exit; ?>
<div class="pp-dashboard-container" style="max-width: 600px; margin: 20px auto 0; padding: 30px; border: 1px solid #e2e4e7; border-radius: 12px; background: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <h3 style="margin-top:0; margin-bottom:15px; color:#1d2327;">? Szolg&aacute;ltat&aacute;s oldali inform&aacute;ci&oacute;k</h3>

    <?php if (is_wp_error($xtream_info)): ?>
        <div style="background:#fff8e5; border-left:4px solid #f56e28; padding:12px 16px; border-radius:4px; margin-bottom:15px;">
            <p style="margin:0; color:#f56e28; font-weight:bold;">A szerveroldali adatok jelenleg nem &eacute;rhet?k el.</p>
            <p style="margin:4px 0 0; font-size:13px; color:#8c8f94;"><?php echo esc_html($xtream_info->get_error_message()); ?></p>
        </div>
    <?php else: ?>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div style="background:#f6f7f7; padding:12px; border-radius:6px;">
                <span style="font-size:12px; color:#8c8f94;">Regisztr&aacute;ci&oacute;</span>
                <br>
                <strong><?php echo esc_html($xtream_info['created_at'] ?: '—'); ?></strong>
            </div>
            <div style="background:#f6f7f7; padding:12px; border-radius:6px;">
                <span style="font-size:12px; color:#8c8f94;">Lej&aacute;rat</span>
                <br>
                <strong><?php echo esc_html($xtream_info['exp_date'] ?: '—'); ?></strong>
            </div>
            <div style="background:#f6f7f7; padding:12px; border-radius:6px;">
                <span style="font-size:12px; color:#8c8f94;">St&aacute;tusz</span>
                <br>
                <strong><?php echo esc_html($xtream_info['status']); ?></strong>
            </div>
            <div style="background:#f6f7f7; padding:12px; border-radius:6px;">
                <span style="font-size:12px; color:#8c8f94;">Pr&oacute;ba</span>
                <br>
                <strong><?php echo $xtream_info['is_trial'] ? 'Igen' : 'Nem'; ?></strong>
            </div>
            <div style="background:#f6f7f7; padding:12px; border-radius:6px;">
                <span style="font-size:12px; color:#8c8f94;">Akt&iacute;v kapcsolatok</span>
                <br>
                <strong><?php echo esc_html($xtream_info['active_cons']); ?></strong>
            </div>
            <div style="background:#f6f7f7; padding:12px; border-radius:6px;">
                <span style="font-size:12px; color:#8c8f94;">Max kapcsolatok</span>
                <br>
                <strong><?php echo esc_html($xtream_info['max_connections']); ?></strong>
            </div>
            <?php if (!empty($xtream_info['server_timezone'])): ?>
            <div style="background:#f6f7f7; padding:12px; border-radius:6px;">
                <span style="font-size:12px; color:#8c8f94;">Szerver id?z&oacute;na</span>
                <br>
                <strong><?php echo esc_html($xtream_info['server_timezone']); ?></strong>
            </div>
            <?php endif; ?>
            <?php if (!empty($xtream_info['server_time'])): ?>
            <div style="background:#f6f7f7; padding:12px; border-radius:6px;">
                <span style="font-size:12px; color:#8c8f94;">Szerver id?</span>
                <br>
                <strong><?php echo esc_html($xtream_info['server_time']); ?></strong>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($xtream_info['allowed_output_formats'])): ?>
        <div style="margin-top:12px; background:#f6f7f7; padding:12px; border-radius:6px;">
            <span style="font-size:12px; color:#8c8f94;">T&aacute;mogatott form&aacute;tumok</span>
            <br>
            <strong><?php echo esc_html(implode(', ', $xtream_info['allowed_output_formats'])); ?></strong>
        </div>
        <?php endif; ?>

        <?php if (!empty($xtream_info['message'])): ?>
        <div style="margin-top:12px; background:#fff8e5; padding:12px; border-radius:6px;">
            <span style="font-size:12px; color:#8c8f94;">Szerver &uuml;zenet</span>
            <br>
            <strong><?php echo esc_html($xtream_info['message']); ?></strong>
        </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
