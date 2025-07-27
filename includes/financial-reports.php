<?php
if (!defined('ABSPATH')) exit;

function easyghest_get_financial_report($start_date = null, $end_date = null) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'easyghest_transactions';

    $query = "SELECT 
                COUNT(id) as total_transactions,
                SUM(amount) as total_amount,
                SUM(paid_amount) as total_paid,
                SUM(amount - paid_amount) as total_due
              FROM $table_name
              WHERE 1=1 ";

    $params = [];

    if ($start_date) {
        $query .= " AND transaction_date >= %s ";
        $params[] = $start_date;
    }
    if ($end_date) {
        $query .= " AND transaction_date <= %s ";
        $params[] = $end_date;
    }

    $prepared_query = $wpdb->prepare($query, $params);

    $result = $wpdb->get_row($prepared_query);

    return $result;
}

// تابع نمایش گزارشات در پنل مدیریت
function easyghest_financial_reports_page() {
    // دریافت تاریخ‌ها از فرم
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : null;
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : null;

    $report = easyghest_get_financial_report($start_date, $end_date);
    ?>
    <div class="wrap">
        <h1><?php _e('گزارشات مالی اقساط', 'easyghest'); ?></h1>

        <form method="get" class="financial-report-filter">
            <input type="hidden" name="page" value="easyghest-financial-reports" />
            <label>
                <?php _e('از تاریخ:', 'easyghest'); ?>
                <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>" />
            </label>
            &nbsp;&nbsp;
            <label>
                <?php _e('تا تاریخ:', 'easyghest'); ?>
                <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>" />
            </label>
            &nbsp;&nbsp;
            <input type="submit" class="button" value="<?php _e('فیلتر', 'easyghest'); ?>" />
        </form>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('تعداد تراکنش‌ها', 'easyghest'); ?></th>
                    <th><?php _e('مبلغ کل (تومان)', 'easyghest'); ?></th>
                    <th><?php _e('مبلغ پرداخت شده (تومان)', 'easyghest'); ?></th>
                    <th><?php _e('مبلغ باقی‌مانده (تومان)', 'easyghest'); ?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo intval($report->total_transactions); ?></td>
                    <td><?php echo number_format(floatval($report->total_amount)); ?></td>
                    <td><?php echo number_format(floatval($report->total_paid)); ?></td>
                    <td><?php echo number_format(floatval($report->total_due)); ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <?php
}
