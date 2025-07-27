<?php
if (!defined('ABSPATH')) exit;

require_once EASYGHEST_PLUGIN_DIR . 'includes/payment-plans.php';

function easyghest_installment_plans_page() {
    // پردازش فرم افزودن/ویرایش پلن
    if (isset($_POST['easyghest_installment_plans_nonce']) && wp_verify_nonce($_POST['easyghest_installment_plans_nonce'], 'easyghest_save_installment_plan')) {
        if (current_user_can('manage_options')) {
            $plan_name = sanitize_text_field($_POST['plan_name'] ?? '');
            $duration = intval($_POST['duration'] ?? 0);
            $interest_rate = floatval($_POST['interest_rate'] ?? 0);
            $min_amount = floatval($_POST['min_amount'] ?? 0);

            if ($plan_name && $duration > 0) {
                $plans = get_option('easyghest_installment_plans', []);
                $plan_id = sanitize_text_field($_POST['plan_id'] ?? '');

                // افزودن یا ویرایش پلن
                if ($plan_id) {
                    $plans[$plan_id] = [
                        'name' => $plan_name,
                        'duration' => $duration,
                        'interest_rate' => $interest_rate,
                        'min_amount' => $min_amount,
                    ];
                } else {
                    $plans[] = [
                        'name' => $plan_name,
                        'duration' => $duration,
                        'interest_rate' => $interest_rate,
                        'min_amount' => $min_amount,
                    ];
                }
                update_option('easyghest_installment_plans', $plans);
                echo '<div class="updated"><p>' . __('پلن با موفقیت ذخیره شد.', 'easyghest') . '</p></div>';
            } else {
                echo '<div class="error"><p>' . __('لطفاً نام پلن و مدت زمان را وارد کنید.', 'easyghest') . '</p></div>';
            }
        }
    }

    // حذف پلن
    if (isset($_GET['delete_plan'])) {
        if (current_user_can('manage_options')) {
            $delete_id = intval($_GET['delete_plan']);
            $plans = get_option('easyghest_installment_plans', []);
            if (isset($plans[$delete_id])) {
                unset($plans[$delete_id]);
                update_option('easyghest_installment_plans', $plans);
                echo '<div class="updated"><p>' . __('پلن حذف شد.', 'easyghest') . '</p></div>';
            }
        }
    }

    $plans = get_option('easyghest_installment_plans', []);

    ?>
    <div class="wrap">
        <h1><?php _e('مدیریت پلن‌های اقساطی', 'easyghest'); ?></h1>

        <h2><?php _e('افزودن / ویرایش پلن', 'easyghest'); ?></h2>
        <form method="post" action="">
            <?php wp_nonce_field('easyghest_save_installment_plan', 'easyghest_installment_plans_nonce'); ?>
            <input type="hidden" name="plan_id" id="plan_id" value="" />
            <table class="form-table">
                <tr>
                    <th><label for="plan_name"><?php _e('نام پلن', 'easyghest'); ?></label></th>
                    <td><input type="text" id="plan_name" name="plan_name" class="regular-text" required></td>
                </tr>
                <tr>
                    <th><label for="duration"><?php _e('مدت زمان (ماه)', 'easyghest'); ?></label></th>
                    <td><input type="number" id="duration" name="duration" min="1" max="60" required></td>
                </tr>
                <tr>
                    <th><label for="interest_rate"><?php _e('نرخ بهره (%)', 'easyghest'); ?></label></th>
                    <td><input type="number" id="interest_rate" name="interest_rate" step="0.01" min="0" value="0"></td>
                </tr>
                <tr>
                    <th><label for="min_amount"><?php _e('حداقل مبلغ (ریال)', 'easyghest'); ?></label></th>
                    <td><input type="number" id="min_amount" name="min_amount" step="0.01" min="0" value="0"></td>
                </tr>
            </table>
            <?php submit_button(__('ذخیره پلن', 'easyghest')); ?>
        </form>

        <h2><?php _e('پلن‌های موجود', 'easyghest'); ?></h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e('نام پلن', 'easyghest'); ?></th>
                    <th><?php _e('مدت زمان (ماه)', 'easyghest'); ?></th>
                    <th><?php _e('نرخ بهره (%)', 'easyghest'); ?></th>
                    <th><?php _e('حداقل مبلغ (ریال)', 'easyghest'); ?></th>
                    <th><?php _e('عملیات', 'easyghest'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($plans)) {
                    foreach ($plans as $id => $plan) {
                        ?>
                        <tr>
                            <td><?php echo esc_html($plan['name']); ?></td>
                            <td><?php echo esc_html($plan['duration']); ?></td>
                            <td><?php echo esc_html($plan['interest_rate']); ?></td>
                            <td><?php echo esc_html(number_format($plan['min_amount'])); ?></td>
                            <td>
                                <a href="#" class="easyghest-edit-plan" data-id="<?php echo esc_attr($id); ?>"
                                   data-name="<?php echo esc_attr($plan['name']); ?>"
                                   data-duration="<?php echo esc_attr($plan['duration']); ?>"
                                   data-interest_rate="<?php echo esc_attr($plan['interest_rate']); ?>"
                                   data-min_amount="<?php echo esc_attr($plan['min_amount']); ?>">
                                   <?php _e('ویرایش', 'easyghest'); ?>
                                </a> |
                                <a href="<?php echo admin_url('admin.php?page=easyghest-installment-plans&delete_plan=' . esc_attr($id)); ?>"
                                   onclick="return confirm('<?php _e('آیا مطمئن هستید؟', 'easyghest'); ?>');">
                                   <?php _e('حذف', 'easyghest'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr><td colspan="5"><?php _e('هیچ پلنی تعریف نشده است.', 'easyghest'); ?></td></tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var editLinks = document.querySelectorAll('.easyghest-edit-plan');
            editLinks.forEach(function(link){
                link.addEventListener('click', function(e){
                    e.preventDefault();
                    document.getElementById('plan_id').value = this.getAttribute('data-id');
                    document.getElementById('plan_name').value = this.getAttribute('data-name');
                    document.getElementById('duration').value = this.getAttribute('data-duration');
                    document.getElementById('interest_rate').value = this.getAttribute('data-interest_rate');
                    document.getElementById('min_amount').value = this.getAttribute('data-min_amount');
                });
            });
        });
    </script>
    <?php
}