<?php if (!defined('ABSPATH')) exit; ?>
<h3>PusztaPlay Rendszer Adatok</h3>
<p>Ahol az isteni hatalmad manifesztálódik a profiljukon.</p>
<table class="form-table">
    <tr>
        <th><label for="pp_client_id">Ügyfélkód</label></th>
        <td>
            <input type="text" name="pp_client_id" id="pp_client_id" value="<?php echo esc_attr($client_id); ?>" class="regular-text" />
            <p class="description">A rabszolga azonosítója.</p>
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
</table>