<?php
if (!defined('ABSPATH')) exit;

/**
 * پشتیبانی از مالیات ووکامرس برای اقساط
 */

// افزودن مالیات به مبلغ هر قسط
function easyghest_add_tax_to_installment($installment_amount, $order_id) {
    $tax_rates = WC_Tax::get_rates('');
    $taxes = WC_Tax::calc_tax($installment_amount, $tax_rates, true);
    $total_tax = array_sum($taxes);
    return round($installment_amount + $total_tax, 2);
}

// محاسبه مالیات روی کل مبلغ اقساط
function easyghest_calculate_tax_for_installments($total_amount) {
    $tax_rates = WC_Tax::get_rates('');
    $taxes = WC_Tax::calc_tax($total_amount, $tax_rates, true);
    return array_sum($taxes);
}
?>
