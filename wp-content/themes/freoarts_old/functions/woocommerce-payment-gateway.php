<?php

/****************************************************
 *
 * WOOCOMMERCE - PAYMENT GATEWAY
 *
 ****************************************************/

function freoarts_always_complete_order($order_id)
{

    global $wpdb;
    $order = wc_get_order($order_id);

    $order->update_status('completed', 'Payment completed.');
    $order->payment_complete();

    // Check for wp_enrolment_wizard_submissions and mark as complete
    $wp_enrolment_wizard_submissions = $wpdb->get_row("SELECT * FROM wp_enrolment_wizard_submissions WHERE order_id = " . $order_id);

    if($wp_enrolment_wizard_submissions) {
        $submission_id = $wp_enrolment_wizard_submissions->id;
        if(isset($submission_id) && !empty($submission_id)) {
            $submission = enrolment_wizard_get_submission_data($submission_id);
        
            enrolment_wizard_create_enrolments($submission);

            $wpdb->update(
                'wp_enrolment_wizard_submissions',
                array(
                    'status' => 'complete'
                ),
                array(
                    'id' => $submission_id
                )
            );
        }

    }
}
add_action('woocommerce_payment_complete', 'freoarts_always_complete_order');

/**
 * Add the custom gateway to the Woocommerce array
 *
 * @param $gateways
 * @return array
 */
function freoarts_custom_add_to_gateways($gateways)
{

    $gateways[] = 'WC_Custom_Gateway';

    return $gateways;
}
add_filter('woocommerce_payment_gateways', 'freoarts_custom_add_to_gateways');

/**
 * Initiate the custom class for Other Payment Methods
 *
 * TODO: Move this to it's own file
 */
function freoarts_custom_payment_gateway()
{
    if (!class_exists('WC_Custom_Gateway')) {

        class WC_Custom_Gateway extends WC_Payment_Gateway
        {

            public function __construct()
            {

                $this->id = 'other_payment_method';
                $this->icon = apply_filters('woocommerce_offline_icon', '');
                $this->has_fields = false;
                $this->method_title = 'Other Payment Method';
                $this->method_description = 'Used for Other Payment Methods';

                // Load the settings.
                $this->init_form_fields();
                $this->init_settings();

                // Define user set variables
                $this->title = $this->get_payment_method_title();
                $this->description = 'Used for Other Payment Methods';
                $this->instructions = $this->get_option('instructions', $this->description);

                // Actions
                add_action('woocommerce_update_options_payment_gateways_' . $this->id,
                    array($this, 'process_admin_options'));
                add_action('woocommerce_thankyou_' . $this->id, array($this, 'thankyou_page'));

                // Customer Emails
                add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);
            }

            public function get_payment_method_title()
            {

                global $post;

                if (get_post_type($post) == 'shop_order') {

                    $title = get_metadata('post', $post->ID, '_payment_method_title', true);

                    if ($title) {
                        return $title;
                    }
                }

                return 'Other Payment Method';
            }

            public function init_form_fields()
            {

                //
            }

            public function thankyou_page()
            {

                //
            }

            public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

                //
            }

            public function process_payment($order_id)
            {

                $order = wc_get_order($order_id);

                // Mark as on-hold (we're awaiting the payment)
                $order->update_status('completed', 'Other payment method completed.');
                // Reduce stock levels
                $order->reduce_order_stock();

                // Payment has been successful
                $order->add_order_note('Payment completed via other payment method.');

                // Mark order as Paid
                $order->payment_complete();

                // Send invoice
                $email = new WC_Email_Customer_Invoice();
                $email->trigger($order_id);

                // Remove cart
                WC()->cart->empty_cart();

                // Return thankyou redirect
                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url($order)
                );
            }
        }
    }
}
add_action('after_setup_theme', 'freoarts_custom_payment_gateway', 99);