<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

wc_print_notices();

?>

<p class="cart-empty">
	<?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?>
</p>

<?php do_action( 'woocommerce_cart_is_empty' ); ?>

<p class="return-to-shop">
    <a href="<?php eu($GLOBALS['site_url'] . '/learn/'); ?>" class="button wc-backward">View Courses</a>
    <a href="<?php eu($GLOBALS['site_url'] . '/membership/'); ?>" class="button wc-backward">View Membership Options</a>
</p>
