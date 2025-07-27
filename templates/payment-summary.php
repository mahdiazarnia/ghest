<?php
if (!defined('ABSPATH')) exit;

function easyghest_payment_summary($atts) {
    if (!is_user_logged_in()) {
        return '<p>' . __('برای مشاهده وضعیت اقساط باید وارد سایت شوید.', 'easyghest') . '</p>';
    }

    $user_id = get_current_user_id();

    // فرضاً داده‌های اقساط از جدول یا متاهای کاربر خوانده می‌شود
    $installments = get_user_meta($user_id, '_easyghest_installments', true);

    if (empty($installments)) {
        return '<p>' . __('هیچ اقساطی برای شما ثبت نشده است.', 'easyghest') . '</p>';
    }

    ob_start();
    ?>
    <div class="easyghest-payment-summary">
        <h3><?php _e('خلاصه وضعیت اقساط', 'easyghest'); ?></h3>
        <table class="widefat fixed" cellspacing="0">
            <thead>
                <tr>
                    <th><?php _e('نام سفارش', 'easyghest'); ?></th>
                    <th><?php _e('مبلغ کل', 'easyghest'); ?></th>
                    <th><?php _e('مبلغ پرداخت‌شده', 'easyghest'); ?></th>
                    <th><?php _e('مانده پرداخت', 'easyghest'); ?></th>
                    <th><?php _e('وضعیت', 'easyghest'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($installments as $installment): ?>
                    <tr>
                        <td><?php echo esc_html($installment['order_name'] ?? ''); ?></td>
                        <td><?php echo number_format(floatval($installment['total_amount'] ?? 0)); ?> <?php _e('ریال', 'easyghest'); ?></td>
                        <td><?php echo number_format(floatval($installment['paid_amount'] ?? 0)); ?> <?php _e('ریال', 'easyghest'); ?></td>
                        <td><?php echo number_format(floatval($installment['remaining_amount'] ?? 0)); ?> <?php _e('ریال', 'easyghest'); ?></td>
                        <td>
                            <?php
                            $status = $installment['status'] ?? 'نامشخص';
                            if ($status === 'paid') {
                                echo '<span style="color:green;">' . __('پرداخت شده', 'easyghest') . '</span>';
                            } elseif ($status === 'pending') {
                                echo '<span style="color:orange;">' . __('در انتظار پرداخت', 'easyghest') . '</span>';
                            } else {
                                echo '<span style="color:red;">' . __('نامشخص', 'easyghest') . '</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('easyghest_payment_summary', 'easyghest_payment_summary');
