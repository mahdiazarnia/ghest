<?php
if (!defined('ABSPATH')) exit;

function easyghest_partial_payment_settings_page() {
    if (isset($_POST['easyghest_partial_payment_nonce']) && wp_verify_nonce($_POST['easyghest_partial_payment_nonce'], 'easyghest_save_partial_payment')) {
        if (current_user_can('manage_options')) {
            $enabled = isset($_POST['partial_payment_enabled']) ? 1 : 0;
            $min_amount = floatval($_POST['partial_payment_min_amount'] ?? 0);
            update_option('easyghest_partial_payment_enabled', $enabled);
            update_option('easyghest_partial_payment_min_amount', $min_amount);
            echo '<div class="updated"><p>' . __('تنظیمات پرداخت جزئی ذخیره شد.', 'easyghest') . '</p></div>';
        }
    }

    $enabled = get_option('easyghest_partial_payment_enabled', 0);
    $min_amount = get_option('easyghest_partial_payment_min_amount', 0);
    ?>

    <div class="wrap">
        <h1><?php _e('تنظیمات پرداخت جزئی', 'easyghest'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('easyghest_save_partial_payment', 'easyghest_partial_payment_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><?php _e('فعال‌سازی پرداخت جزئی', 'easyghest'); ?></th>
                    <td><input type="checkbox" name="partial_payment_enabled" value="1" <?php checked($enabled, 1); ?>></td>
                </tr>
                <tr>
                    <th><label for="partial_payment_min_amount"><?php _e('حداقل مبلغ پرداخت جزئی (ریال)', 'easyghest'); ?></label></th>
                    <td><input type="number" id="partial_payment_min_amount" name="partial_payment_min_amount" value="<?php echo esc_attr($min_amount); ?>" min="0" step="0.01"></td>
                </tr>
            </table>
            <?php submit_button(__('ذخیره تنظیمات', 'easyghest')); ?>
        </form>
    </div>

    <?php
}
