<?php
if (!defined('ABSPATH')) exit;

/**
 * کلاس مدیریت ارسال پیامک با استفاده از API آی‌پنل
 */

class EasyGhest_SMS_Notifications {

    private $api_key;
    private $line_number;

    public function __construct() {
        $settings = get_option('easyghest_settings', []);
        $this->api_key = isset($settings['ippanel_api_key']) ? $settings['ippanel_api_key'] : '';
        $this->line_number = isset($settings['ippanel_line_number']) ? $settings['ippanel_line_number'] : '';
    }

    /**
     * ارسال پیامک از طریق آی‌پنل
     * @param string $to شماره موبایل دریافت کننده (با 98 یا 09 شروع شود)
     * @param string $message متن پیامک
     * @return bool|array true در صورت موفقیت یا آرایه خطا
     */
    public function send_sms($to, $message) {
        if (empty($this->api_key) || empty($this->line_number)) {
            return ['error' => 'API Key یا شماره خط پیامک تنظیم نشده است.'];
        }

        $url = "https://ippanel.com/api/select";

        $data = [
            'op' => 'send',
            'uname' => '',            // در آی‌پنل نام کاربری می‌گذاریم خالی (در این متد api_key استفاده می‌شود)
            'pass' => '',
            'from' => $this->line_number,
            'to' => $to,
            'msg' => $message,
            'isflash' => false,
            'udh' => false,
            'recId' => [],
            'status' => 0,
            'apiKey' => $this->api_key,
        ];

        $args = [
            'body' => json_encode($data),
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'timeout' => 15,
        ];

        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            return ['error' => $response->get_error_message()];
        }

        $body = wp_remote_retrieve_body($response);
        $result = json_decode($body, true);

        if (isset($result['status']) && $result['status'] == 1) {
            return true;
        }

        return ['error' => $result['message'] ?? 'ارسال پیامک ناموفق بود.'];
    }

    /**
     * ارسال پیامک سررسید اقساط به مشتری
     * @param string $phone_number شماره موبایل مشتری
     * @param array $installment_info اطلاعات قسط شامل مبلغ، سررسید و ... 
     */
    public function send_due_reminder($phone_number, $installment_info) {
        $message = sprintf(
            "مشتری گرامی، قسط مبلغ %s تومان شما تا تاریخ %s سررسید شده است. لطفا نسبت به پرداخت اقدام فرمایید. با تشکر.",
            number_format($installment_info['amount']),
            $installment_info['due_date']
        );

        return $this->send_sms($phone_number, $message);
    }

    /**
     * ارسال پیامک هشدار بدهی به مشتری
     * @param string $phone_number شماره موبایل مشتری
     * @param float $debt_amount مبلغ بدهی
     */
    public function send_debt_alert($phone_number, $debt_amount) {
        $message = sprintf(
            "مشتری عزیز، مبلغ %s تومان بدهی معوقه دارید. لطفا هر چه سریع‌تر نسبت به تسویه اقدام نمایید.",
            number_format($debt_amount)
        );

        return $this->send_sms($phone_number, $message);
    }
}
