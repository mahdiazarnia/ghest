<?php
if (!defined('ABSPATH')) exit;

function easyghest_display_installment_plans() {
    $plans = get_option('easyghest_payment_plans', []);

    if (empty($plans)) {
        return '<p>' . __('هیچ پلن اقساطی تعریف نشده است.', 'easyghest') . '</p>';
    }

    ob_start();
    ?>
    <div class="easyghest-installment-plans">
        <h3><?php _e('پلن‌های پرداخت اقساطی', 'easyghest'); ?></h3>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e('نام پلن', 'easyghest'); ?></th>
                    <th><?php _e('تعداد اقساط', 'easyghest'); ?></th>
                    <th><?php _e('درصد بهره', 'easyghest'); ?></th>
                    <th><?php _e('فاصله بین اقساط (روز)', 'easyghest'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($plans as $plan): ?>
                    <tr>
                        <td><?php echo esc_html($plan['name']); ?></td>
                        <td><?php echo intval($plan['installments']); ?></td>
                        <td><?php echo floatval($plan['interest']); ?>%</td>
                        <td><?php echo intval($plan['interval_days']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('easyghest_installment_plans', 'easyghest_display_installment_plans');
