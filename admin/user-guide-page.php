<?php
if (!defined('ABSPATH')) exit;

function easyghest_user_guide_page() {
    ?>
    <div class="wrap">
        <h1><?php _e('راهنمای استفاده از پلاگین EasyGhest', 'easyghest'); ?></h1>
        <p><?php _e('این پلاگین امکان پرداخت اقساطی برای سفارش‌های ووکامرس را فراهم می‌کند.', 'easyghest'); ?></p>

        <h2><?php _e('نصب و راه‌اندازی', 'easyghest'); ?></h2>
        <ol>
            <li><?php _e('از منوی تنظیمات پلاگین، اطلاعات حساب پیامک (IPPANEL) را وارد کنید.', 'easyghest'); ?></li>
            <li><?php _e('پلن‌های پرداخت اقساطی مورد نظر خود را تعریف کنید.', 'easyghest'); ?></li>
            <li><?php _e('رده‌بندی کاربران را بر اساس مبلغ خرید تنظیم نمایید.', 'easyghest'); ?></li>
            <li><?php _e('تنظیمات پرداخت جزئی را بررسی و فعال کنید.', 'easyghest'); ?></li>
        </ol>

        <h2><?php _e('استفاده از شورت‌کدها', 'easyghest'); ?></h2>
        <p><?php _e('برای نمایش پلن‌های اقساط در صفحات مورد نظر از شورت‌کد زیر استفاده کنید:', 'easyghest'); ?></p>
        <pre>[easyghest_installment_plans]</pre>

        <h2><?php _e('تماس با پشتیبانی', 'easyghest'); ?></h2>
        <p><?php _e('در صورت نیاز به راهنمایی بیشتر با تیم پشتیبانی تماس بگیرید.', 'easyghest'); ?></p>
    </div>
    <?php
}
