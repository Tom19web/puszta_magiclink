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
                    <input type="password" name="pp_smtp_settings[pass]" value="" placeholder="<?php echo !empty($options['pass']) ? '•••••• (írd át ha módosítani akarod)' : 'Add meg az SMTP jelszót'; ?>" class="regular-text">
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

        <hr style="margin:20px 0;">

        <h2>⏰ Előfizetés Lejárati Emlékeztető</h2>
        <p class="description">Automatikus e-mail küldés a felhasználóknak, mielőtt lejár az előfizetésük.</p>

        <table class="form-table">
            <tr>
                <th scope="row"><label for="reminder_enabled">Emlékeztető engedélyezése</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="pp_smtp_settings[reminder_enabled]" value="1" <?php checked(isset($options['reminder_enabled']) ? $options['reminder_enabled'] : 0, 1); ?>>
                        Bekapcsolás (alapértelmezetten KI van kapcsolva)
                    </label>
                    <p class="description">Ha bekapcsolod, naponta egyszer ellenőrizzük a lejáró előfizetéseket.</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="reminder_days_before">Hány nappal előtte</label></th>
                <td>
                    <input type="number" name="pp_smtp_settings[reminder_days_before]" min="1" max="30" value="<?php echo esc_attr(isset($options['reminder_days_before']) ? $options['reminder_days_before'] : '7'); ?>" class="small-text">
                    <p class="description">Ennyi nappal a lejárat előtt küldjük ki az emlékeztetőt (1–30).</p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="reminder_subject">E-mail tárgya</label></th>
                <td>
                    <input type="text" name="pp_smtp_settings[reminder_subject]" value="<?php echo esc_attr(isset($options['reminder_subject']) ? $options['reminder_subject'] : 'A PusztaPlay előfizetésed hamarosan lejár'); ?>" class="regular-text" style="width:100%;max-width:500px;">
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="reminder_body">E-mail szövege (HTML)</label></th>
                <td>
                    <textarea name="pp_smtp_settings[reminder_body]" rows="12" class="large-text" style="width:100%;max-width:600px;font-family:monospace;"><?php
                        $default_body = '<p>Kedves {name}!</p>' . "\n\n"
                            . '<p>Az előfizetésed (<strong>{package}</strong>) <strong>{days_left} nap múlva</strong>, {expiry_date} napon lejár.</p>' . "\n\n"
                            . '<p>A zavartalan szolgáltatás érdekében kérjük, hosszabbítsd meg az előfizetésedet a <a href="{dashboard_url}">Vezérlőpulton</a>.</p>' . "\n\n"
                            . '<p>Üdvözlettel,<br>A PusztaPlay csapata</p>';
                        echo esc_textarea(isset($options['reminder_body']) && !empty($options['reminder_body']) ? $options['reminder_body'] : $default_body);
                    ?></textarea>
                    <p class="description">
                        Használható helykitöltők:
                        <code>{name}</code> — név &nbsp;|&nbsp;
                        <code>{email}</code> — e-mail &nbsp;|&nbsp;
                        <code>{package}</code> — csomag &nbsp;|&nbsp;
                        <code>{expiry_date}</code> — lejárat dátuma &nbsp;|&nbsp;
                        <code>{days_left}</code> — hátralévő napok száma &nbsp;|&nbsp;
                        <code>{dashboard_url}</code> — vezérlőpult link
                    </p>
                </td>
            </tr>
        </table>
        <?php submit_button('Diktátum Mentése'); ?>
    </form>
</div>