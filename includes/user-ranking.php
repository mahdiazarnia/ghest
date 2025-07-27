<?php
if (!defined('ABSPATH')) exit;

/**
 * مدیریت رده‌بندی کاربران برای اقساط (طلایی، نقره‌ای، برنزی و ...)
 */

// تعریف رده‌ها و شرایط آنها
function easyghest_get_user_ranks() {
    // نمونه ساده: هر رده با حداقل مبلغ خرید یا تعداد سفارش مشخص می‌شود
    return [
        'bronze' => [
            'name' => 'برنزی',
            'min_orders' => 1,
            'discount_percent' => 0,
        ],
        'silver' => [
            'name' => 'نقره‌ای',
            'min_orders' => 5,
            'discount_percent' => 5,
        ],
        'gold' => [
            'name' => 'طلایی',
            'min_orders' => 10,
            'discount_percent' => 10,
        ],
    ];
}

// گرفتن رده کاربر براساس تعداد سفارشات موفق
function easyghest_get_user_rank($user_id) {
    if (!$user_id) return null;

    $ranks = easyghest_get_user_ranks();

    $args = [
        'customer_id' => $user_id,
        'status' => 'completed',
        'return' => 'ids',
    ];

    $orders = wc_get_orders($args);
    $order_count = count($orders);

    $user_rank = null;
    foreach ($ranks as $key => $rank) {
        if ($order_count >= $rank['min_orders']) {
            $user_rank = $key;
        }
    }
    return $user_rank;
}

// گرفتن اطلاعات کامل رده کاربر (نام و درصد تخفیف)
function easyghest_get_user_rank_info($user_id) {
    $rank_key = easyghest_get_user_rank($user_id);
    $ranks = easyghest_get_user_ranks();
    return isset($ranks[$rank_key]) ? $ranks[$rank_key] : null;
}
?>
