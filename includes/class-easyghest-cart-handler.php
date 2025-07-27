<?php
if (!defined('ABSPATH')) exit;

class Easyghest_Cart_Handler {

    public function __construct() {
        // اصلاح قیمت اقساطی در سبد خرید
        add_action('woocommerce_before_calculate_totals', [$this, 'adjust_cart_item_price'], 20);

        // نمایش اقساط در نام محصول داخل سبد خرید
        add_filter('woocommerce_cart_item_name', [$this, 'display_installment_info_in_cart'], 10, 3);
    }

    // تغییر قیمت محصول در سبد خرید به مبلغ قسط ماهانه
    public function adjust_cart_item_price($cart_object) {
        foreach ($cart_object->get_cart() as $cart_item_key => $cart_item) {
            if (!empty($cart_item['easyghest_installment']) && !empty($cart_item['easyghest_installment_plan'])) {
                $plan = $cart_item['easyghest_installment_plan'];
                $original_price = $cart_item['data']->get_price();

                $interest = ($original_price * floatval($plan['interest_rate'])) / 100;
                $total_with_interest = $original_price + $interest;
                $monthly_payment = $total_with_interest / intval($plan['duration']);

                $cart_item['data']->set_price($monthly_payment);
            }
        }
    }

    // نمایش اطلاعات اقساط در نام محصول داخل سبد خرید
    public function display_installment_info_in_cart($item_name, $cart_item, $cart_item_key) {
        if (!empty($cart_item['easyghest_installment'])) {
            $item_name .= '<br/><small style="color:green;">' . __('خرید اقساطی فعال', 'easyghest') . '</small>';

            if (!empty($cart_item['easyghest_installment_plan'])) {
                $plan = $cart_item['easyghest_installment_plan'];
                $item_name .= '<br/><small style="color:blue;">' . sprintf(
                    __('پلن: %s - مدت %d ماه - بهره %s%%', 'easyghest'),
                    esc_html($plan['name']),
                    intval($plan['duration']),
                    esc_html($plan['interest_rate'])
                ) . '</small>';
            }
        }
        return $item_name;
    }
}

new Easyghest_Cart_Handler();
