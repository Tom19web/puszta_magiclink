<?php if (!defined('ABSPATH')) exit; ?>
<div class="wrap">
    <h1>PusztaPlay Magic Login - Rendszer Beállítások</h1>
    <p>Ne babrálj itt semmivel, ha nem tudod, mit csinálsz, te kis kókler!</p>
    
    <form method="post" action="options.php">
        <?php settings_fields('pp_magic_login_group'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="packages">Előfizetési Csomagok</label></th>
                <td>
                    <input type="text" name="pp_smtp_settings[packages]" value="<?php echo esc_attr(isset($options['packages']) ? $options['packages'] : 'Alap Csomag, Prémium Csomag, VIP Csomag'); ?>" class="regular-text" style="width: 100%; max-width: 500px;">
                    <p class="description">Vesszővel elválasztva, ahogy az arisztokrácia szereti.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="logo_url">Levél Logó URL</label></th>
                <td><input type="url" name="pp_smtp_settings[logo_url]" value="<?php echo esc_attr(isset($options['logo_url']) ? $options['logo_url'] : ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label>SMTP Szerver (Host)</label></th>
                <td><input type="text" name="pp_smtp_settings[host]" value="<?php echo esc_attr(isset($options['host']) ? $options['host'] : ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label>SMTP Port</label></th>
                <td><input type="number" name="pp_smtp_settings[port]" value="<?php echo esc_attr(isset($options['port']) ? $options['port'] : '465'); ?>" class="small-text"></td>
            </tr>
            <tr>
                <th scope="row"><label>Felhasználónév (SMTP)</label></th>
                <td><input type="email" name="pp_smtp_settings[user]" value="<?php echo esc_attr(isset($options['user']) ? $options['user'] : ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label>Jelszó (SMTP)</label></th>
                <td>
                    <input type="password" name="pp_smtp_settings[pass]" value="<?php echo esc_attr(isset($options['pass']) ? $options['pass'] : ''); ?>" class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row"><label>Feladó E-mail</label></th>
                <td><input type="email" name="pp_smtp_settings[from_email]" value="<?php echo esc_attr(isset($options['from_email']) ? $options['from_email'] : ''); ?>" class="regular-text"></td>
            </tr>
            <tr>
                <th scope="row"><label>Feladó Neve</label></th>
                <td><input type="text" name="pp_smtp_settings[from_name]" value="<?php echo esc_attr(isset($options['from_name']) ? $options['from_name'] : 'PusztaPlay'); ?>" class="regular-text"></td>
            </tr>
        </table>
        <?php submit_button('Diktátum Mentése'); ?>
    </form>
</div>