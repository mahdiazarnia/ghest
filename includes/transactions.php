<?php
if (!defined('ABSPATH')) exit;

/**
 * مدیریت تراکنش‌های اقساطی
 */

// ثبت تراکنش جدید
function easyghest_add_transaction($order_id, $amount, $type, $note = '') {
    $transactions = get_post_meta($order_id, '_easyghest_transactions', true);
    if (!is_array($transactions)) {
        $transactions = [];
    }

    $transactions[] = [
        'date' => current_time('mysql'),
        'amount' => floatval($amount),
        'type' => sanitize_text_field($type), // مثلا 'payment', 'refund'
        'note' => sanitize_text_field($note),
    ];

    update_post_meta($order_id, '_easyghest_transactions', $transactions);
}

// گرفتن تراکنش‌ها برای سفارش
function easyghest_get_transactions($order_id) {
    $transactions = get_post_meta($order_id, '_easyghest_transactions', true);
    if (!is_array($transactions)) {
        $transactions = [];
    }
    return $transactions;
}
?>
