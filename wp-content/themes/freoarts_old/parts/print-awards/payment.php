<?php

// Has an order already been created for this submission?
if (empty($submission->order_id)) {

    // Build the order address
    $address = array(
        'first_name' => $submission->artist_first_name,
        'last_name'  => $submission->artist_surname,
        'company'    => $submission->artist_agent,
        'email'      => $submission->artist_email,
        'phone'      => $submission->mobile,
        'address_1'  => $submission->address,
        'address_2'  => '',
        'city'       => $submission->suburb,
        'state'      => $submission->state,
        'postcode'   => $submission->postcode,
        'country'    => 'AU'
    );

    // Create the order
    $order = wc_create_order(array(
        'customer_id' => $submission->user_id
    ));

    // Get print award product
    $print_award_id = get_field('print_award_product', 'options');
    $product = wc_get_product($print_award_id);
    $order->add_product($product, 1);

    // Save the address details to the order
    $order->set_address($address, 'billing');
    $order->set_address($address, 'shipping');

    $order->calculate_totals();
    $order->get_tax_totals();

    // Update the order's payment meta data
    update_post_meta($order->get_id(), '_payment_method', 'eway');
    update_post_meta($order->get_id(), '_payment_method_title', 'Credit Card');
    update_post_meta($order->get_id(), '_via_admin', false);

    $submission->update(array(
        'order_id' => $order->get_id()
    ));
}
else {

    $order = wc_get_order($submission->order_id);
}

// Get eWAY access token
$FA_WC_Gateway_EWAY->public_set_token_customer_id($order, 'new');
$result = $FA_WC_Gateway_EWAY->public_request_access_code($order);

$eway_access_code = esc_attr($result->AccessCode);
?>

<h2>Payment</h2>

<?php if (!$fields_valid){ ?>
<p class="error">There was an error with your submission please review and try again</p>
<?php } ?>

<?php
    $gateway_url = 'https://secure.ewaypayments.com/Process';
    if ($FA_WC_Gateway_EWAY->testmode == 'yes') {
        $gateway_url = 'https://secure-au.sandbox.ewaypayments.com/Process';
    }
?>
<p class="description">Please note that this form will redirect you to the eWAY payment completion page.</p>

<p>Total cost of submission is <strong>$<?php echo number_format($order->get_total(), 2, '.', ' '); ?></strong> (cost includes $<?php echo number_format($order->get_total_tax(), 2, '.', ' '); ?> GST).</p>

<form method="post" class="inside acf-fields -left print-award-cc-form" action="<?php echo $gateway_url; ?>" id="eway_credit_card_form">

    <input type="hidden" name="EWAY_ACCESSCODE" value="<?php echo $eway_access_code; ?>">

    <div class="acf-field field acf-field-text">
        <div class="acf-label">
            <label for="EWAY_CARDNAME">Card Name</label>
        </div>
        <div class="acf-input">
            <div class="acf-input-wrap">
                <input type="text" name="EWAY_CARDNAME" value="" autocomplete="off" class="required">
            </div>
        </div>
    </div>

    <div class="acf-field field acf-field-text">
        <div class="acf-label">
            <label for="EWAY_CARDNUMBER">Card Number</label>
        </div>
        <div class="acf-input">
            <div class="acf-input-wrap">
                <input type="text" name="EWAY_CARDNUMBER" value="" autocomplete="off" class="required">
            </div>
        </div>
    </div>

    <div class="field-wrapper">
    
	    <div class="acf-field field half-field acf-field-text">
	        <div class="acf-label">
	            <label for="EWAY_CARDEXPIRYMONTH">Expiry Month</label>
	        </div>
	        <div class="acf-input">
	            <div class="acf-input-wrap">
	                <div class="styled">
		                <select name="EWAY_CARDEXPIRYMONTH" class="required">
		                    <?php for ($j = 1; $j <= 12; $j++) { ?>
		                        <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
		                    <?php } ?>
		                </select>
		                <span class="arrow"></span>
	                </div>
	            </div>
	        </div>
	    </div>
	
	    <div class="acf-field field half-field acf-field-text">
	        <div class="acf-label">
	            <label for="EWAY_CARDEXPIRYYEAR">Expiry Year</label>
	        </div>
	        <div class="acf-input">
	            <div class="acf-input-wrap">
	                <div class="styled">
		                <select name="EWAY_CARDEXPIRYYEAR" class="required">
		                    <?php for ($j = date('Y'); $j <= (date('Y') + 10); $j++) { ?>
		                        <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
		                    <?php } ?>
		                </select>
		                <span class="arrow"></span>
	                </div>
	            </div>
	        </div>
	    </div>
    
    </div>

    <div class="acf-field field acf-field-text">
        <div class="acf-label">
            <label for="EWAY_CARDCVN">Security Code (CVN)</label>
        </div>
        <div class="acf-input">
            <div class="acf-input-wrap">
                <input type="text" name="EWAY_CARDCVN" value="" autocomplete="off" class="required">
            </div>
        </div>
    </div>

    <div class="acf-field field acf-field-text">
        <div class="acf-label">
        </div>
        <div class="acf-input">
            <div class="acf-input-wrap">
                <input class="btn btn-black is-pay" type="submit" name="pay_with_eway" value="Pay">
            </div>
        </div>
    </div>

</form>

<nav class="form-nav">

	<a href="<?php the_permalink() ?>?stage=artwork" class="prev">Artwork</a>

</nav>



