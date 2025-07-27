<?php
if (!defined('ABSPATH')) {
    exit; // جلوگیری از دسترسی مستقیم
}

/**
 * نمایش اطلاعات اقساطی در متا آیتم‌های سفارش ووکامرس
 */
function easyghest_show_installment_order_meta($item_id, $item, $order) {
    // گرفتن مقدار متای خرید اقساطی
    $installment = $item->get_meta(__('خرید اقساطی', 'easyghest'));

    if ($installment && $installment === __('بله', 'easyghest')) {
        echo '<p style="color:green; font-weight:bold;">' . __('خرید اقساطی فعال', 'easyghest') . '</p>';

        $plan_name = $item->get_meta(__('نام پلن اقساطی', 'easyghest'));
        $duration = $item->get_meta(__('مدت (ماه)', 'easyghest'));
        $interest_rate = $item->get_meta(__('نرخ بهره (%)', 'easyghest'));

        if ($plan_name || $duration || $interest_rate) {
            echo '<ul style="margin-left:20px;">';
            if ($plan_name) {
                echo '<li>' . sprintf(__('پلن: %s', 'easyghest'), esc_html($plan_name)) . '</li>';
            }
            if ($duration) {
                echo '<li>' . sprintf(__('مدت زمان: %d ماه', 'easyghest'), intval($duration)) . '</li>';
            }
            if ($interest_rate) {
                echo '<li>' . sprintf(__('نرخ بهره: %s%%', 'easyghest'), esc_html($interest_rate)) . '</li>';
            }
            echo '</ul>';
        }
    }
}
add_action('woocommerce_order_item_meta_end', 'easyghest_show_installment_order_meta', 10, 3);
