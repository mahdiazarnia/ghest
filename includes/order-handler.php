<?php
if (!defined('ABSPATH')) exit;

/**
 * افزودن اطلاعات اقساط به آیتم‌های سفارش ووکامرس
 */

add_action('woocommerce_checkout_create_order_line_item', 'easyghest_add_installment_meta_to_order_items', 10, 4);

function easyghest_add_installment_meta_to_order_items($item, $cart_item_key, $values, $order) {
    if (!empty($values['easyghest_installment']) && !empty($values['easyghest_installment_plan'])) {
        $plan = $values['easyghest_installment_plan'];

        $item->add_meta_data(__('خرید اقساطی', 'easyghest'), __('بله', 'easyghest'), true);
        $item->add_meta_data(__('نام پلن اقساطی', 'easyghest'), sanitize_text_field($plan['name']), true);
        $item->add_meta_data(__('مدت (ماه)', 'easyghest'), intval($plan['duration']), true);
        $item->add_meta_data(__('نرخ بهره (%)', 'easyghest'), floatval($plan['interest_rate']), true);
    }
}
