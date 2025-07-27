<?php
if (!defined('ABSPATH')) exit;

// بارگذاری فایل گزارشات مالی
require_once EASYGHEST_PLUGIN_DIR . 'includes/financial-reports.php';

function easyghest_financial_reports_menu() {
    add_submenu_page(
        'easyghest-main-menu',          // اسلاگ منوی اصلی (فرض می‌کنیم منوی اصلی به این اسلاگ هست)
        __('گزارشات مالی', 'easyghest'), // عنوان صفحه
        __('گزارشات مالی', 'easyghest'), // عنوان منو
        'manage_options',               // سطح دسترسی
        'easyghest-financial-reports', // اسلاگ صفحه
        'easyghest_financial_reports_page' // تابع رندر صفحه
    );
}
add_action('admin_menu', 'easyghest_financial_reports_menu');
