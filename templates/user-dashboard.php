<?php
if (!defined('ABSPATH')) exit;

function easyghest_user_dashboard() {
    if (!is_user_logged_in()) {
        return '<p>' . __('لطفاً وارد شوید تا وضعیت اقساط شما نمایش داده شود.', 'easyghest') . '</p>';
    }

    $user_id = get_current_user_id();

    // فرض داده‌های اقساط کاربر
    $installments = get_user_meta($user_id, '_easyghest_installments', true);

    ob_start();
    ?>
    <div class="easyghest-user-dashboard">
        <h2><?php _e('وضعیت اقساط شما', 'easyghest'); ?></h2>
        <?php if (empty($installments)) : ?>
            <p><?php _e('هیچ اقساط فعالی ندارید.', 'easyghest'); ?></p>
        <?php else : ?>
            <ul>
                <?php foreach ($installments as $installment): ?>
                    <li>
                        <strong><?php echo esc_html($installment['order_name'] ?? ''); ?></strong> — 
                        <?php echo number_format(floatval($installment['remaining_amount'] ?? 0)); ?> <?php _e('ریال مانده', 'easyghest'); ?> — 
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
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('easyghest_user_dashboard', 'easyghest_user_dashboard');
