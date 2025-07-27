<?php
if (!defined('ABSPATH')) exit;

/**
 * مدیریت پرداخت‌های جزئی (Partial Payments)
 */

// ثبت پرداخت جزئی برای سفارش
function easyghest_record_partial_payment($order_id, $amount, $payment_date = '') {
    $payments = get_post_meta($order_id, '_easyghest_partial_payments', true);
    if (!is_array($payments)) {
        $payments = [];
    }

    $payments[] = [
        'amount' => floatval($amount),
        'date' => $payment_date ? $payment_date : current_time('mysql'),
    ];

    update_post_meta($order_id, '_easyghest_partial_payments', $payments);
}

// گرفتن مجموع پرداخت‌های جزئی انجام شده
function easyghest_get_partial_payments_total($order_id) {
    $payments = get_post_meta($order_id, '_easyghest_partial_payments', true);
    if (!is_array($payments)) {
        return 0;
    }
    $total = 0;
    foreach ($payments as $pay) {
        $total += floatval($pay['amount']);
    }
    return round($total, 2);
}

// بررسی پرداخت کامل یا ناقص اقساط
function easyghest_is_installment_fully_paid($order_id, $total_amount) {
    $paid = easyghest_get_partial_payments_total($order_id);
    return $paid >= $total_amount;
}

?>
