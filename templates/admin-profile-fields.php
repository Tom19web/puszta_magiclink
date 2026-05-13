<?php if (!defined('ABSPATH')) exit; ?>
<h3>PusztaPlay Rendszer Adatok</h3>
<p>Ahol az isteni hatalmad manifesztálódik a profiljukon.</p>
<table class="form-table">
    <tr>
        <th><label for="pp_client_id">Ügyfélkód (Xtream username)</label></th>
        <td>
            <input type="text" name="pp_client_id" id="pp_client_id" value="<?php echo esc_attr($client_id); ?>" class="regular-text" />
            <p class="description">A rabszolga azonosítója. Egyben az Xtream Codes felhasználónév.</p>
        </td>
    </tr>
    <tr>
        <th><label for="pp_xtream_pass">Xtream jelszó</label></th>
        <td>
            <input type="password" name="pp_xtream_pass" id="pp_xtream_pass"
                value=""
                placeholder="<?php echo $has_xtream_pass ? '•••••• (hagyd üresen hogy ne változzon)' : 'Add meg az Xtream jelszót'; ?>"
                class="regular-text"
                autocomplete="new-password" />
            <p class="description">
                <?php if ($has_xtream_pass) : ?>
                    <strong style="color:#2ea44f">✓ Jelszó mentve</strong>
                    — hagyd üresen ha nem akarod módosítani.
                <?php else : ?>
                    A lejátszó plugin ezzel lépteti be a felhasználót.
                <?php endif; ?>
            </p>
        </td>
    </tr>
    <tr>
        <th><label for="pp_subscription_package">Csomag</label></th>
        <td>
            <select name="pp_subscription_package" id="pp_subscription_package" class="regular-text">
                <option value="">Nincs aktív csomag</option>
                <?php foreach ($packages_array as $pkg) : ?>
                    <?php if (!empty($pkg)) : ?>
                        <option value="<?php echo esc_attr($pkg); ?>" <?php selected($selected_package, $pkg); ?>>
                            <?php echo esc_html($pkg); ?>
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th><label for="pp_subscription_end">Prémium tagság lejárata</label></th>
        <td>
            <input type="date" name="pp_subscription_end" id="pp_subscription_end" value="<?php echo esc_attr($date_value); ?>" class="regular-text" />
            <p class="description">Amikor az illúzió szertefoszlik.</p>
        </td>
    </tr>
    <tr>
        <th><label for="pp_phone">Telefonszám</label></th>
        <td>
            <input type="tel" name="pp_phone" id="pp_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" />
            <p class="description">A halandó elérhetősége, ha a galamb posta nem működik.</p>
        </td>
    </tr>
    <?php if ($api_key_exists) : ?>
    <tr>
        <th><label for="pp_api_key_revoke">API kulcs visszavonása</label></th>
        <td>
            <label>
                <input type="checkbox" name="pp_api_key_revoke" id="pp_api_key_revoke" value="1" <?php checked($api_key_revoked, true); ?> />
                <?php if ($api_key_revoked) : ?>
                    <strong style="color:#b71c1c">VISSZAVONVA</strong> — a TV app nem tud csatlakozni amíg vissza nem vonod.
                <?php else : ?>
                    Visszavonás — a TV app többé nem tudja használni ezt a kulcsot.
                <?php endif; ?>
            </label>
            <p class="description">Új kulcs generálásához pipáld ki, mentsd el, majd vedd ki a pipát és mentsd újra.</p>
        </td>
    </tr>
    <?php endif; ?>
</table>
