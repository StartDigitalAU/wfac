<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 * @global WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="woocommerce-billing-fields">
	<?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>

		<h3><?php _e( 'Billing &amp; Shipping', 'woocommerce' ); ?></h3>

	<?php else : ?>

		<h3><?php _e( 'Account & billing details', 'woocommerce' ); ?></h3>

	<?php endif; ?>
	
	<p class="note">
		Please note all account holders must be of 18 years of age<br>
		<span class="required">*</span> indicates a required field
	</p>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>

	<?php foreach ( $checkout->checkout_fields['billing'] as $key => $field ) : ?>

		<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

	<?php endforeach; ?>
	
	<?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>

	<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

	<?php if ( ! empty( $checkout->checkout_fields['account'] ) ) : ?>

		<div class="create-account">

			<p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce' ); ?></p>

			<?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>

				<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

			<?php endforeach; ?>
			
			<p class="account-note">
				The password should be at least seven characters long.<br>
				To make it stronger, use upper and lower case letters, numbers and symbols like ! " ? $ % ^ & ).
			</p>

			<div class="clear"></div>

		</div>

	<?php endif; ?>

    <?php
    $hear_about_us_options = get_field('hear_about_us_options', 'option');
    $value = $checkout->get_value('billing_hear_about_us');
    ?>
    <div class="input billing-select">
        <label for="billing_hear_about_us" class="">How did you hear about us?</label>
        <div class="styled">
            <select name="billing_hear_about_us" id="billing_hear_about_us" class="select " data-allow_clear="true" data-placeholder="Please select">
                <option value="Not selected">Please select</option>
                <?php
                foreach ($hear_about_us_options as $option) {

                    $selected = ($value == $option['option']) ? ' selected="selected"': '';
                    echo '<option value="' . $option['option'] . '"' . $selected . '>' . $option['option'] . '</option>';
                }
                ?>
            </select>
            <span class="arrow"></span>
        </div>
    </div>

    <div class="input billing-checkboxes">
        <span class="label">I'd like to receive news about</span>
        <div class="inner">
	        <?php
	        $subscription_options = get_field('subscription_options', 'option');
	        $i = 1;
	        ?>
	        <?php foreach ($subscription_options as $option) { ?>
	            <div class="input-wrap">
	                <input type="checkbox" id="subscription_options-<?php echo $i; ?>" name="subscription_options[]" value="<?php echo $option['list_id']; ?>">
	                <label for="subscription_options-<?php echo $i; ?>"><span><?php echo $option['label']; ?></span></label>
	            </div>
	            <?php $i++; ?>
	        <?php } ?>
        </div>
        
        <p class="billing-note">Your information is always kept confidential and you can unsubscribe at any time.</p>
    </div>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>

	<?php endif; ?>
</div>
