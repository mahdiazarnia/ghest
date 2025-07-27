<?php
if (!defined('ABSPATH')) exit;

/**
 * توابع کمکی و سفارشی افزونه EasyGhest
 */

// ثبت داده‌های پرداخت اقساطی در متا سفارش
function easyghest_save_installment_data($order_id, $installment_data) {
    if (!empty($installment_data) && is_array($installment_data)) {
        update_post_meta($order_id, '_easyghest_installment_data', $installment_data);
    }
}

// گرفتن داده‌های پرداخت اقساطی از متا سفارش
function easyghest_get_installment_data($order_id) {
    return get_post_meta($order_id, '_easyghest_installment_data', true);
}

// افزودن ستون وضعیت اقساط در لیست سفارشات ادمین ووکامرس
function easyghest_add_installment_column($columns) {
    $columns['easyghest_installment_status'] = __('وضعیت اقساط', 'easyghest');
    return $columns;
}
add_filter('manage_edit-shop_order_columns', 'easyghest_add_installment_column');

// نمایش وضعیت اقساط در ستون اضافه شده
function easyghest_render_installment_column($column, $post_id) {
    if ($column === 'easyghest_installment_status') {
        $data = easyghest_get_installment_data($post_id);
        if (!empty($data)) {
            echo esc_html($data['status'] ?? 'نامشخص');
        } else {
            echo __('ندارد', 'easyghest');
        }
    }
}
add_action('manage_shop_order_posts_custom_column', 'easyghest_render_installment_column', 10, 2);

// ثبت شورت‌کد نمایش فرم اقساط
function easyghest_installment_form_shortcode() {
    ob_start();
    wc_get_template('installment-plans.php', [], 'easyghest', plugin_dir_path(__FILE__) . '../templates/');
    return ob_get_clean();
}
add_shortcode('easyghest_installments', 'easyghest_installment_form_shortcode');

?>
