<?php
if (!defined('ABSPATH')) exit;

function easyghest_advanced_reports() {
    if (!current_user_can('manage_options')) {
        return '<p>' . __('دسترسی ندارید.', 'easyghest') . '</p>';
    }

    // فرضا اطلاعات تراکنش‌ها از دیتابیس خوانده می‌شود
    $transactions = get_option('easyghest_transactions', []);

    ob_start();
    ?>
    <div class="easyghest-advanced-reports">
        <h2><?php _e('گزارشات پیشرفته اقساط', 'easyghest'); ?></h2>
        <?php if (empty($transactions)) : ?>
            <p><?php _e('هیچ تراکنشی ثبت نشده است.', 'easyghest'); ?></p>
        <?php else : ?>
            <table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th><?php _e('شناسه تراکنش', 'easyghest'); ?></th>
                        <th><?php _e('نام کاربر', 'easyghest'); ?></th>
                        <th><?php _e('مبلغ', 'easyghest'); ?></th>
                        <th><?php _e('تاریخ', 'easyghest'); ?></th>
                        <th><?php _e('وضعیت', 'easyghest'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td><?php echo esc_html($tx['id'] ?? ''); ?></td>
                            <td><?php echo esc_html($tx['user_name'] ?? ''); ?></td>
                            <td><?php echo number_format(floatval($tx['amount'] ?? 0)); ?> <?php _e('ریال', 'easyghest'); ?></td>
                            <td><?php echo esc_html($tx['date'] ?? ''); ?></td>
                            <td><?php echo esc_html($tx['status'] ?? ''); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('easyghest_advanced_reports', 'easyghest_advanced_reports');
