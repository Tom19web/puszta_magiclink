<?php if (!defined('ABSPATH')) exit; ?>
<div class="pp-dashboard-container" style="max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #e2e4e7; border-radius: 12px; background: #ffffff; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <h2 style="margin-top:0; color:#1d2327;">Üdvözlünk, <?php echo esc_html($current_user->display_name); ?>!</h2>
    <p style="color:#50575e; margin-bottom:20px;">Itt kezelheted a fiókod adatait és az előfizetésedet.</p>
    <hr style="border:0; border-top:1px solid #f0f0f1; margin:20px 0;">
    
    <h3 style="margin-bottom:15px; color:#1d2327;">Saját adataid:</h3>
    <p style="margin:0 0 10px 0; font-size:16px;"><strong>E-mail címed:</strong> <?php echo esc_html($current_user->user_email); ?></p>
    
    <p style="margin:0 0 5px 0; font-size:16px;"><strong>Ügyfélkódod:</strong> <span style="font-family:monospace; background:#f0f0f1; padding:3px 8px; border-radius:4px; font-weight:bold; color:#d63638;"><?php echo esc_html($client_id); ?></span></p>
    <p style="margin:0 0 20px 0; font-size:13px; color:#8c8f94;"><i>Banki átutalás esetén ezt a kódot írd a közlemény rovatba!</i></p>
    
    <div style="background-color: #f6f7f7; padding: 20px; border-left: 4px solid #2271b1; border-radius: 4px;">
        <h3 style="margin-top:0; margin-bottom:15px; color:#1d2327;">Előfizetésed állapota:</h3>
        <p style="margin:0 0 10px 0; font-size:16px;"><strong>Csomag:</strong> <span style="background:#2271b1; color:#fff; padding:3px 8px; border-radius:4px; font-size:14px;"><?php echo esc_html($sub_package); ?></span></p>
        
        <p style="margin:0 0 10px 0; font-size:16px;"><strong>Státusz:</strong> 
            <?php if ($sub_status === 'aktív'): ?>
                <span style="color:#108548; font-weight:bold;">Aktív</span>
            <?php elseif ($sub_status === 'lejárt'): ?>
                <span style="color:#d63638; font-weight:bold;">Lejárt</span>
            <?php else: ?>
                <span style="color:#f56e28; font-weight:bold;">Inaktív</span>
            <?php endif; ?>
        </p>
        
        <p style="margin:0; font-size:16px;"><strong>Lejárat ideje:</strong> <?php echo esc_html($sub_end_date); ?></p>
    </div>
</div>