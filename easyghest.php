<?php
/*
Plugin Name: EasyGhest - پرداخت اقساطی ووکامرس
Plugin URI:  https://yourwebsite.com/
Description: افزودن امکان پرداخت اقساطی به ووکامرس همراه با مدیریت پلن‌ها، پیامک سررسید، رده‌بندی کاربران و گزارشات مالی
Version:     1.0.0
Author:      Your Name
Author URI:  https://yourwebsite.com/
Text Domain: easyghest
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit;
}

// تعریف مسیرها
define('EASYGHEST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('EASYGHEST_PLUGIN_URL', plugin_dir_url(__FILE__));

// بارگذاری فایل‌های admin برای صفحات منو
require_once EASYGHEST_PLUGIN_DIR . 'admin/settings-page.php';
require_once EASYGHEST_PLUGIN_DIR . 'admin/installment-plans-page.php';
require_once EASYGHEST_PLUGIN_DIR . 'admin/partial-payment-settings.php';
require_once EASYGHEST_PLUGIN_DIR . 'admin/user-ranking-page.php';
require_once EASYGHEST_PLUGIN_DIR . 'admin/user-guide-page.php';

// بارگذاری فایل‌های اصلی پلاگین
require_once EASYGHEST_PLUGIN_DIR . 'includes/admin-settings.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/payment-plans.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/sms-notifications.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/user-ranking.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/custom-functions.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/taxes.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/partial-payment.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/financial-reports.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/security.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/transactions.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/product-installment.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/class-easyghest-cart-handler.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/order-meta.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/order-handler.php';
require_once EASYGHEST_PLUGIN_DIR . 'includes/show-plantouser.php';

// افزودن منوهای پیشخوان مدیریت
function easyghest_admin_menu() {
    add_menu_page(
        __('EasyGhest', 'easyghest'),
        __('EasyGhest', 'easyghest'),
        'manage_options',
        'easyghest-main-menu',
        'easyghest_settings_page',
        'dashicons-money-alt',
        56
    );

    add_submenu_page(
        'easyghest-main-menu',
        __('تنظیمات', 'easyghest'),
        __('تنظیمات', 'easyghest'),
        'manage_options',
        'easyghest-settings',
        'easyghest_settings_page'
    );

    add_submenu_page(
        'easyghest-main-menu',
        __('مدیریت پلن‌های اقساطی', 'easyghest'),
        __('پلن‌های اقساط', 'easyghest'),
        'manage_options',
        'easyghest-installment-plans',
        'easyghest_installment_plans_page'
    );

    add_submenu_page(
        'easyghest-main-menu',
        __('رده‌بندی کاربران', 'easyghest'),
        __('رده‌بندی کاربران', 'easyghest'),
        'manage_options',
        'easyghest-user-ranking',
        'easyghest_user_ranking_page'
    );

    add_submenu_page(
        'easyghest-main-menu',
        __('تنظیمات پرداخت جزئی', 'easyghest'),
        __('پرداخت جزئی', 'easyghest'),
        'manage_options',
        'easyghest-partial-payment-settings',
        'easyghest_partial_payment_settings_page'
    );

    add_submenu_page(
        'easyghest-main-menu',
        __('گزارشات مالی', 'easyghest'),
        __('گزارشات مالی', 'easyghest'),
        'manage_options',
        'easyghest-financial-reports',
        'easyghest_financial_reports_page'
    );

    add_submenu_page(
        'easyghest-main-menu',
        __('راهنما', 'easyghest'),
        __('راهنما', 'easyghest'),
        'manage_options',
        'easyghest-user-guide',
        'easyghest_user_guide_page'
    );
}
add_action('admin_menu', 'easyghest_admin_menu');

// شورت‌کد نمایش پلن‌های اقساطی
add_shortcode('easyghest_installment_plans', 'easyghest_shortcode_installment_plans');
function easyghest_shortcode_installment_plans() {
    ob_start();
    include EASYGHEST_PLUGIN_DIR . 'templates/installment-plans.php';
    return ob_get_clean();
}

// شورت‌کد نمایش خلاصه اقساط در داشبورد کاربر
add_shortcode('easyghest_payment_summary', 'easyghest_shortcode_payment_summary');
function easyghest_shortcode_payment_summary() {
    ob_start();
    include EASYGHEST_PLUGIN_DIR . 'templates/payment-summary.php';
    return ob_get_clean();
}

// بارگذاری زبان‌ها
function easyghest_load_textdomain() {
    load_plugin_textdomain('easyghest', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'easyghest_load_textdomain');

// ساخت جداول در زمان فعال‌سازی افزونه
register_activation_hook(__FILE__, 'easyghest_create_tables');
function easyghest_create_tables() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . 'easyghest_transactions';

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        order_id BIGINT(20) UNSIGNED NOT NULL,
        amount DECIMAL(12,2) NOT NULL,
        paid_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
        due_date DATE NOT NULL,
        status VARCHAR(20) NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// سایر اکشن‌ها و فیلترهای دلخواه می‌توانند اینجا اضافه شوند.
