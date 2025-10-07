<?php
/**
 * Checkout login form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.8.0
 */

defined( 'ABSPATH' ) || exit;

if ( is_user_logged_in() || 'no' === get_option( 'woocommerce_enable_checkout_login_reminder' ) ) {
    return;
}

?>
<div class="checkout-login">

    <h3>Do you have an existing account? <a href="#" onclick="loginToggle()">Click Here</a></h3>
    <div id="toggleForm" class="checkout-login-form" style="display: none">
    <?php
        woocommerce_login_form(
            array(
                'redirect' => wc_get_page_permalink( 'checkout' ),
                'hidden'   => false
            )
        );
    ?>
    </div>

    <?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>
        <?php if ( $checkout->enable_guest_checkout ) : ?>

			<div class="create-accont">
				<h3>Not an account holder?</h3>
	            
	            <p class="form-row form-row-wide create-account">
	                <input class="input-checkbox" id="createaccount" <?php checked( ( true === $checkout->get_value( 'createaccount' ) || ( true === apply_filters( 'woocommerce_create_account_default_checked', false ) ) ), true) ?> type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><span><?php _e( 'Create account on checkout', 'woocommerce' ); ?></span></label>
	            </p>
			</div>

        <?php endif; ?>
    <?php endif; ?>

</div>
