<?php
if (!defined('ABSPATH')) exit;

/**
 * امنیت پلاگین - پشتیبانی از 2FA (احراز هویت دو مرحله‌ای)
 */

// افزودن فیلد 2FA به صفحه پروفایل کاربر (نمونه ساده)
function easyghest_add_2fa_field_to_profile($user) {
    ?>
    <h3><?php _e('احراز هویت دو مرحله‌ای', 'easyghest'); ?></h3>
    <table class="form-table">
        <tr>
            <th><label for="easyghest_2fa_enabled"><?php _e('فعال‌سازی 2FA', 'easyghest'); ?></label></th>
            <td>
                <input type="checkbox" name="easyghest_2fa_enabled" id="easyghest_2fa_enabled" value="1" <?php checked(get_user_meta($user->ID, 'easyghest_2fa_enabled', true), 1); ?> />
                <span class="description"><?php _e('فعال کردن احراز هویت دو مرحله‌ای برای این حساب کاربری', 'easyghest'); ?></span>
            </td>
        </tr>
    </table>
    <?php
}
add_action('show_user_profile', 'easyghest_add_2fa_field_to_profile');
add_action('edit_user_profile', 'easyghest_add_2fa_field_to_profile');

// ذخیره تنظیمات 2FA برای کاربر
function easyghest_save_2fa_field($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;

    $enabled = isset($_POST['easyghest_2fa_enabled']) ? 1 : 0;
    update_user_meta($user_id, 'easyghest_2fa_enabled', $enabled);
}
add_action('personal_options_update', 'easyghest_save_2fa_field');
add_action('edit_user_profile_update', 'easyghest_save_2fa_field');

// بررسی فعال بودن 2FA برای کاربر
function easyghest_is_2fa_enabled($user_id) {
    return (bool) get_user_meta($user_id, 'easyghest_2fa_enabled', true);
}

// برای تکمیل می‌توان در اینجا بررسی 2FA هنگام ورود اضافه کرد
?>
