<?php
if (!defined('ABSPATH')) exit;

function easyghest_user_ranking_page() {
    if (isset($_POST['easyghest_user_ranking_nonce']) && wp_verify_nonce($_POST['easyghest_user_ranking_nonce'], 'easyghest_save_user_ranking')) {
        if (current_user_can('manage_options')) {
            $rankings = [
                'gold' => sanitize_text_field($_POST['gold'] ?? ''),
                'silver' => sanitize_text_field($_POST['silver'] ?? ''),
                'bronze' => sanitize_text_field($_POST['bronze'] ?? ''),
            ];
            update_option('easyghest_user_rankings', $rankings);
            echo '<div class="updated"><p>' . __('رده‌بندی کاربران با موفقیت ذخیره شد.', 'easyghest') . '</p></div>';
        }
    }

    $rankings = get_option('easyghest_user_rankings', [
        'gold' => '',
        'silver' => '',
        'bronze' => '',
    ]);
    ?>

    <div class="wrap">
        <h1><?php _e('مدیریت رده‌بندی کاربران', 'easyghest'); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('easyghest_save_user_ranking', 'easyghest_user_ranking_nonce'); ?>
            <table class="form-table">
                <tr>
                    <th><label for="gold"><?php _e('طلایی', 'easyghest'); ?></label></th>
                    <td><input type="text" id="gold" name="gold" value="<?php echo esc_attr($rankings['gold']); ?>" class="regular-text" placeholder="<?php _e('مثلا 10000000', 'easyghest'); ?>"></td>
                </tr>
                <tr>
                    <th><label for="silver"><?php _e('نقره‌ای', 'easyghest'); ?></label></th>
                    <td><input type="text" id="silver" name="silver" value="<?php echo esc_attr($rankings['silver']); ?>" class="regular-text" placeholder="<?php _e('مثلا 5000000', 'easyghest'); ?>"></td>
                </tr>
                <tr>
                    <th><label for="bronze"><?php _e('برنزی', 'easyghest'); ?></label></th>
                    <td><input type="text" id="bronze" name="bronze" value="<?php echo esc_attr($rankings['bronze']); ?>" class="regular-text" placeholder="<?php _e('مثلا 1000000', 'easyghest'); ?>"></td>
                </tr>
            </table>
            <?php submit_button(__('ذخیره رده‌بندی', 'easyghest')); ?>
        </form>
    </div>

    <?php
}
