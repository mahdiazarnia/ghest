<?php
if (!defined('ABSPATH')) exit;

/**
 * مدیریت پلن‌های پرداخت اقساطی
 */

class EasyGhest_Payment_Plans {

    // بارگذاری همه پلن‌ها
    public static function get_all_plans() {
        $plans = get_option('easyghest_payment_plans', []);
        return is_array($plans) ? $plans : [];
    }

    // ذخیره‌سازی کل پلن‌ها
    public static function save_all_plans($plans) {
        update_option('easyghest_payment_plans', $plans);
    }

    // اضافه کردن پلن جدید
    public static function add_plan($plan_data) {
        $plans = self::get_all_plans();

        // ساخت ID یکتا
        $plan_data['id'] = uniqid('plan_');

        $plans[] = $plan_data;

        self::save_all_plans($plans);
    }

    // ویرایش پلن بر اساس ID
    public static function update_plan($plan_id, $plan_data) {
        $plans = self::get_all_plans();

        foreach ($plans as &$plan) {
            if ($plan['id'] === $plan_id) {
                $plan = array_merge($plan, $plan_data);
                break;
            }
        }
        self::save_all_plans($plans);
    }

    // حذف پلن
    public static function delete_plan($plan_id) {
        $plans = self::get_all_plans();

        $plans = array_filter($plans, function($plan) use ($plan_id) {
            return $plan['id'] !== $plan_id;
        });

        self::save_all_plans(array_values($plans));
    }

    // محاسبه اقساط برای یک مبلغ خاص بر اساس یک پلن
    public static function calculate_installments($total_amount, $plan_id) {
        $plans = self::get_all_plans();

        foreach ($plans as $plan) {
            if ($plan['id'] === $plan_id) {
                $installments_count = intval($plan['installments_count']);
                $interest_percent = floatval($plan['interest_percent']);

                // محاسبه مبلغ اقساط با احتساب سود
                $total_with_interest = $total_amount + ($total_amount * $interest_percent / 100);
                $installment_amount = round($total_with_interest / $installments_count);

                return [
                    'total_with_interest' => $total_with_interest,
                    'installment_amount' => $installment_amount,
                    'installments_count' => $installments_count,
                    'interest_percent' => $interest_percent,
                ];
            }
        }

        return false; // اگر پلن پیدا نشد
    }
}
?>
