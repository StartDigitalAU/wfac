<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="checkout-wrapper">

    <h2 class="faux-page-title">Checkout</h2>
    
    <?php
    wc_print_notices();

    do_action( 'woocommerce_before_checkout_form', $checkout );

    // If checkout registration is disabled and not logged in, the user cannot checkout
    if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
        echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
        return;
    }
    ?>

    <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

        <?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

            <div class="checkout-details">

                <?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>
                <?php do_action( 'woocommerce_checkout_billing' ); ?>
                <?php do_action( 'woocommerce_checkout_shipping' ); ?>
                <?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

            </div>

        <?php endif; ?>

        <div class="checkout-review">

            <h3 id="order_review_heading">Your Order</h3>
            <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
            <div id="order_review" class="woocommerce-checkout-review-order">
                <?php do_action( 'woocommerce_checkout_order_review' ); ?>
            </div>

            <?php if(!is_user_logged_in()) :
                $_cart = WC()->cart->get_cart();
                $cart_count = is_countable($_cart) ? intval(count($_cart)) : intval(1);
                $plural = $cart_count > 1 ? 'these courses' : 'this course';
                $price = WC()->cart->total;
                $membership_discount = floatval($GLOBALS['theme_options']['membership_discount']);
                $discount_rate = (100 - $membership_discount) / 100;
                $discount_price = number_format((float)$price * $discount_rate, 2,'.','');
                ?>
                <div class="member-discount-box">
                    <strong>Become a member</strong> to receive <?= $membership_discount ?>% off all courses.  Purchase <?= $plural ?> for only $<?= $discount_price ?>.
                </div>
            <?php endif; ?>

            <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

        </div>

        <div class="checkout-payment">

            <?php do_action( 'woocommerce_custom_checkout_payment' ); ?>

        </div>

    </form>

    <?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div>