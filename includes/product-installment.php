<?php
if (!defined('ABSPATH')) exit;

/**
 * بررسی فعال بودن خرید اقساطی برای یک محصول
 * @param int $product_id
 * @return bool
 */
function easyghest_is_installment_enabled_for_product($product_id) {
    $global_enabled = get_option('easyghest_installment_enabled', 'yes');
    if ($global_enabled !== 'yes') {
        return false;
    }
    $disabled = get_post_meta($product_id, '_easyghest_disable_installment', true);
    if ($disabled === 'yes') {
        return false;
    }
    return true;
}

/**
 * افزودن متاباکس تنظیمات خرید اقساطی به صفحه ویرایش محصول
 */
function easyghest_add_installment_meta_box() {
    add_meta_box(
        'easyghest_installment_meta_box',
        __('تنظیمات خرید اقساطی', 'easyghest'),
        'easyghest_render_installment_meta_box',
        ['product'],
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'easyghest_add_installment_meta_box');

/**
 * رندر متاباکس در صفحه ویرایش محصول
 */
function easyghest_render_installment_meta_box($post) {
    wp_nonce_field('easyghest_save_installment_meta', 'easyghest_installment_meta_nonce');
    $disabled = get_post_meta($post->ID, '_easyghest_disable_installment', true);
    ?>
    <p>
        <label>
            <input type="checkbox" name="easyghest_disable_installment" value="yes" <?php checked($disabled, 'yes'); ?> />
            <?php _e('غیرفعال کردن امکان خرید اقساطی برای این محصول', 'easyghest'); ?>
        </label>
    </p>
    <?php
}

/**
 * ذخیره مقدار متاباکس هنگام ذخیره محصول
 */
function easyghest_save_installment_meta($post_id) {
    if (!isset($_POST['easyghest_installment_meta_nonce']) || !wp_verify_nonce($_POST['easyghest_installment_meta_nonce'], 'easyghest_save_installment_meta')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['easyghest_disable_installment']) && $_POST['easyghest_disable_installment'] === 'yes') {
        update_post_meta($post_id, '_easyghest_disable_installment', 'yes');
    } else {
        delete_post_meta($post_id, '_easyghest_disable_installment');
    }
}
add_action('save_post_product', 'easyghest_save_installment_meta');

/**
 * نمایش گزینه خرید اقساطی در صفحه تک محصول (قبل از دکمه افزودن به سبد)
 */
function easyghest_display_installment_option_on_product() {
    global $product;
    if (!easyghest_is_installment_enabled_for_product($product->get_id())) {
        return;
    }
    ?>
    <div class="easyghest-installment-option" style="margin: 15px 0;">
        <label>
            <input type="checkbox" name="easyghest_installment" value="yes" id="easyghest_installment_checkbox" />
            <?php _e('خرید اقساطی', 'easyghest'); ?>
        </label>
    </div>
    <?php
}
add_action('woocommerce_before_add_to_cart_button', 'easyghest_display_installment_option_on_product', 10);

/**
 * نمایش انتخاب پلن‌های اقساطی وقتی خرید اقساطی فعال شده است
 */
function easyghest_display_installment_plan_options() {
    global $product;
    if (!easyghest_is_installment_enabled_for_product($product->get_id())) {
        return;
    }

    $plans = get_option('easyghest_installment_plans', []);
    if (empty($plans)) {
        return;
    }

    $price = floatval($product->get_price());

    echo '<div class="easyghest-installment-plans" style="margin: 15px 0; display:none;" id="easyghest_installment_plans_wrapper">';
    echo '<p>' . __('انتخاب پلن اقساطی:', 'easyghest') . '</p>';

    foreach ($plans as $id => $plan) {
        if ($price < floatval($plan['min_amount'])) {
            continue;
        }
        ?>
        <label>
            <input type="radio" name="easyghest_installment_plan" value="<?php echo esc_attr($id); ?>" />
            <?php echo esc_html($plan['name']) . ' - ' . esc_html($plan['duration']) . ' ' . __('ماه', 'easyghest'); ?>
        </label><br/>
        <?php
    }

    echo '</div>';
}
add_action('woocommerce_before_add_to_cart_button', 'easyghest_display_installment_plan_options', 20);

/**
 * اسکریپت ساده برای نمایش/مخفی کردن پلن‌ها وقتی چک‌باکس خرید اقساطی زده می‌شود
 */
function easyghest_enqueue_installment_scripts() {
    if (!is_product()) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var checkbox = document.getElementById('easyghest_installment_checkbox');
        var plansWrapper = document.getElementById('easyghest_installment_plans_wrapper');
        if(!checkbox || !plansWrapper) return;

        checkbox.addEventListener('change', function () {
            if (checkbox.checked) {
                plansWrapper.style.display = 'block';
            } else {
                plansWrapper.style.display = 'none';
                // پاک کردن انتخاب پلن اگر چک‌باکس خاموش شد
                var radios = plansWrapper.querySelectorAll('input[type=radio]');
                radios.forEach(function(radio) { radio.checked = false; });
            }
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'easyghest_enqueue_installment_scripts');

/**
 * افزودن اطلاعات خرید اقساطی و پلن به آیتم سبد خرید
 */
function easyghest_add_installment_option_to_cart_item($cart_item_data, $product_id, $variation_id) {
    if (!easyghest_is_installment_enabled_for_product($product_id)) {
        return $cart_item_data;
    }

    if (isset($_POST['easyghest_installment']) && $_POST['easyghest_installment'] === 'yes') {
        $cart_item_data['easyghest_installment'] = true;

        if (isset($_POST['easyghest_installment_plan'])) {
            $plan_id = sanitize_text_field($_POST['easyghest_installment_plan']);
            $plans = get_option('easyghest_installment_plans', []);
            if (isset($plans[$plan_id])) {
                $cart_item_data['easyghest_installment_plan'] = $plans[$plan_id];
                $cart_item_data['easyghest_installment_plan']['id'] = $plan_id;
            }
        }
    }

    return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'easyghest_add_installment_option_to_cart_item', 10, 3);

/**
 * نمایش اطلاع خرید اقساطی و پلن در صفحه سبد خرید
 */
function easyghest_display_installment_cart_item($item_name, $cart_item, $cart_item_key) {
    if (!empty($cart_item['easyghest_installment'])) {
        $item_name .= '<br/><small style="color:green;">' . __('خرید اقساطی فعال', 'easyghest') . '</small>';
        if (!empty($cart_item['easyghest_installment_plan'])) {
            $plan = $cart_item['easyghest_installment_plan'];
            $item_name .= '<br/><small style="color:blue;">' . sprintf(__('پلن: %s - مدت %d ماه - بهره %s%%', 'easyghest'), 
                esc_html($plan['name']),
                intval($plan['duration']),
                esc_html($plan['interest_rate'])
            ) . '</small>';
        }
    }
    return $item_name;
}
add_filter('woocommerce_cart_item_name', 'easyghest_display_installment_cart_item', 10, 3);

/**
 * ذخیره اطلاعات خرید اقساطی و پلن در آیتم‌های سفارش هنگام ساخت سفارش
 */
function easyghest_add_installment_data_to_order_items($item, $cart_item_key, $values, $order) {
    if (!empty($values['easyghest_installment'])) {
        $item->add_meta_data(__('خرید اقساطی', 'easyghest'), __('بله', 'easyghest'), true);
        if (!empty($values['easyghest_installment_plan'])) {
            $plan = $values['easyghest_installment_plan'];
            $item->add_meta_data(__('نام پلن اقساطی', 'easyghest'), $plan['name'], true);
            $item->add_meta_data(__('مدت (ماه)', 'easyghest'), $plan['duration'], true);
            $item->add_meta_data(__('نرخ بهره (%)', 'easyghest'), $plan['interest_rate'], true);
        }
    }
}
add_action('woocommerce_checkout_create_order_line_item', 'easyghest_add_installment_data_to_order_items', 10, 4);
