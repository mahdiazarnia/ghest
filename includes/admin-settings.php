<?php
if (!defined('ABSPATH')) exit;

/**
 * مدیریت تنظیمات افزونه: تنظیمات پیامک، اقساط و...
 */

// ثبت تنظیمات در وردپرس
function easyghest_register_settings() {
    register_setting('easyghest_settings_group', 'easyghest_sms_api_key');
    register_setting('easyghest_settings_group', 'easyghest_sms_api_secret');
    register_setting('easyghest_settings_group', 'easyghest_installment_options');
}

add_action('admin_init', 'easyghest_register_settings');

// بازگردانی تنظیمات پیامک از دیتابیس
function easyghest_get_sms_settings() {
    return [
        'api_key' => get_option('easyghest_sms_api_key', ''),
        'api_secret' => get_option('easyghest_sms_api_secret', ''),
    ];
}

// بازگردانی تنظیمات اقساط با مقادیر پیش‌فرض
function easyghest_get_installment_options() {
    $defaults = [
        'enabled' => 1,
        'max_installments' => 6,
        'interest_rate' => 0, // درصد سود (مثلاً 0 یعنی بدون سود)
    ];
    return wp_parse_args(get_option('easyghest_installment_options', []), $defaults);
}

// ذخیره تنظیمات اقساط
function easyghest_save_installment_options($data) {
    if (is_array($data)) {
        update_option('easyghest_installment_options', $data);
    }
}
?>
