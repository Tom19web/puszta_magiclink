<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1>Új PusztaPlay Előfizető Hozzáadása</h1>
    <?php echo $message; ?>
    
    <form method="post" style="background:#fff; padding:20px; border:1px solid #ccd0d4; box-shadow: 0 1px 3px rgba(0,0,0,0.04); max-width:500px; margin-top:20px; border-left:4px solid #2271b1;">
        
        <?php wp_nonce_field('pp_create_member_action', 'pp_new_member_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th><label>E-mail cím</label></th>
                <td><input type="email" name="email" required class="regular-text"></td>
            </tr>
            <tr>
                <th><label>Becenév</label></th>
                <td><input type="text" name="nickname" class="regular-text" placeholder="pl. PusztaPusztító"></td>
            </tr>
            <tr>
                <th><label>Ügyfélkód</label></th>
                <td><input type="text" name="client_id" class="regular-text" placeholder="pl. PP-1024"></td>
            </tr>
            <tr>
                <th><label>Csomag</label></th>
                <td>
                    <select name="package">
                        <option value="Nincs">Nincs aktív csomag</option>
                        <?php foreach ($packages_array as $pkg): ?>
                            <?php if (!empty($pkg)): ?>
                                <option value="<?php echo esc_attr($pkg); ?>"><?php echo esc_html($pkg); ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label>Lejárat dátuma</label></th>
                <td><input type="date" name="expiry" class="regular-text"></td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="pp_new_member_submit" class="button button-primary" value="Halandó Beiktatása">
        </p>
    </form>
</div>