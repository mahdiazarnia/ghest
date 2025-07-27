<?php
if (!defined('ABSPATH')) exit;

function easyghest_settings_page() {
    // پردازش ذخیره تنظیمات
    if (isset($_POST['easyghest_settings_nonce']) && wp_verify_nonce($_POST['easyghest_settings_nonce'], 'easyghest_save_settings')) {
        if (current_user_can('manage_options')) {
            // دریافت و ذخیره تنظیمات پیامک
            $sms_api_key = sanitize_text_field($_POST['sms_api_key'] ?? '');
            $sms_sender = sanitize_text_field($_POST['sms_sender'] ?? '');

            update_option('easyghest_sms_api_key', $sms_api_key);
            update_option('easyghest_sms_sender', $sms_sender);

            // تنظیمات اقساط
            $default_plan_duration = intval($_POST['default_plan_duration'] ?? 12);
            update_option('easyghest_default_plan_duration', $default_plan_duration);

            // فعال یا غیرفعال بودن فروش اقساطی
            $installment_enabled = ($_POST['easyghest_installment_enabled'] === 'no') ? 'no' : 'yes';
            update_option('easyghest_installment_enabled', $installment_enabled);

            echo '<div class="updated"><p>' . __('تنظیمات با موفقیت ذخیره شد.', 'easyghest') . '</p></div>';
        }
    }

    // بارگذاری تنظیمات ذخیره شده
    $sms_api_key = get_option('easyghest_sms_api_key', '');
    $sms_sender = get_option('easyghest_sms_sender', '');
    $default_plan_duration = get_option('easyghest_default_plan_duration', 12);
    $installment_enabled = get_option('easyghest_installment_enabled', 'yes');
    ?>

    <div class="wrap">
        <h1><?php _e('تنظیمات افزونه EasyGhest', 'easyghest'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('easyghest_save_settings', 'easyghest_settings_nonce'); ?>

            <h2><?php _e('تنظیمات پیامک (IPPANEL)', 'easyghest'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="sms_api_key"><?php _e('کلید API پیامک', 'easyghest'); ?></label></th>
                    <td><input type="text" id="sms_api_key" name="sms_api_key" value="<?php echo esc_attr($sms_api_key); ?>" class="regular-text" /></td>
                </tr>
                <tr>
                    <th><label for="sms_sender"><?php _e('شماره فرستنده', 'easyghest'); ?></label></th>
                    <td><input type="text" id="sms_sender" name="sms_sender" value="<?php echo esc_attr($sms_sender); ?>" class="regular-text" /></td>
                </tr>
            </table>

            <h2><?php _e('تنظیمات پلن‌های اقساط', 'easyghest'); ?></h2>
            <table class="form-table">
                <tr>
                    <th><label for="default_plan_duration"><?php _e('مدت پیش‌فرض پلن (ماه)', 'easyghest'); ?></label></th>
                    <td><input type="number" id="default_plan_duration" name="default_plan_duration" value="<?php echo esc_attr($default_plan_duration); ?>" min="1" max="60" /></td>
                </tr>
                <tr>
                    <th><?php _e('فروش اقساطی فعال باشد؟', 'easyghest'); ?></th>
                    <td>
                        <label>
                            <input type="radio" name="easyghest_installment_enabled" value="yes" <?php checked($installment_enabled, 'yes'); ?> />
                            <?php _e('بله', 'easyghest'); ?>
                        </label>
                        &nbsp;
                        <label>
                            <input type="radio" name="easyghest_installment_enabled" value="no" <?php checked($installment_enabled, 'no'); ?> />
                            <?php _e('خیر', 'easyghest'); ?>
                        </label>
                        <p class="description"><?php _e('اگر غیرفعال باشد، کاربران نمی‌توانند خرید اقساطی جدید انجام دهند اما اقساط قبلی همچنان فعال خواهند بود.', 'easyghest'); ?></p>
                    </td>
                </tr>
            </table>

            <?php submit_button(__('ذخیره تنظیمات', 'easyghest')); ?>
        </form>
    </div>

    <?php
}
