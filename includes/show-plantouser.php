<?php
if ( ! class_exists( 'WooCommerce' ) ) {
    return; // یا خطا بده یا کاری نکن چون ووکامرس فعال نیست
}

// حالا می‌تونی WC() رو استفاده کنی
echo '<pre style="background:#eee; padding:10px;">';
print_r( WC()->cart->get_cart() );
echo '</pre>';
?>
