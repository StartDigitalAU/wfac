<?php

/****************************************************
 *
 * ENROLMENT WIZARD
 *
 ****************************************************/

include_once(TEMPLATEPATH . '/functions/library/FA_WC_Gateway_EWAY.class.php');

/**
 * Helpers
 *
 */

function show_ew_error($errors, $key) {

    if (isset($errors)) {

        if (ifne($errors, $key)) {

            echo '<span class="error">' . ifne($errors, $key) . '</span>';
        }
    }
}

/**
 * Add Enrolment Wizard to admin menu items too
 *
 */
function enrolment_wizard_add_admin_pages() {

    add_menu_page(
        'Enrolment Wizard',
        'Enrolment Wizard',
        'enrolment_wizard',
        'enrolment-wizard-main',
        'enrolment_wizard_admin_view',
        'dashicons-welcome-learn-more',
        250
    );

    add_submenu_page(
        'enrolment-wizard-main',
        'Existing Submissions',
        'Existing Submissions',
        'enrolment_wizard',
        'enrolment-wizard-list',
        'enrolment_wizard_admin_list'
    );
}
add_action('admin_menu', 'enrolment_wizard_add_admin_pages');

/**
 * Add admin styles and scripts
 *
 */
function enrolment_wizard_admin_enqueue_assets() {

    wp_enqueue_script('eway_api_js', 'https://api.ewaypayments.com/JSONP/v3/js');
}
add_action('admin_enqueue_scripts', 'enrolment_wizard_admin_enqueue_assets');

/**
 * Parse enrolment wizard step request
 *
 */
function enrolment_wizard_ajax_parse_step()
{

    global $wpdb,
           $submission,
           $errors,
           $membership;

    $data = array();
    $errors = array();

    $step = filter_input(INPUT_POST, 'step', FILTER_VALIDATE_INT);

    if (empty($step)) {

        echo '';
        wp_die();
    }

    if(!empty($submission['order_id'])) {

        include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-7.php');

    }

    /**
     * Step 1
     * Selecting a customer/user, or creating a new customer/user
     */
    if ($step === 1) {

        $is_customer_new = filter_input(INPUT_POST, 'is_customer_new', FILTER_VALIDATE_BOOLEAN);

        // Has a new customer been selected?
        if ($is_customer_new) {

            $data = array(
                'customer_email' => filter_input(INPUT_POST, 'customer_email', FILTER_SANITIZE_STRING)
            );

            if (empty($data['customer_email'])) {
                $errors['customer_email'] = 'Please provide the customer\'s email address.';
            }
            if (email_exists($data['customer_email'])) {
                $errors['customer_email'] = 'A customer with this email address already exists.';
            }

            $password = filter_input(INPUT_POST, 'customer_password', FILTER_SANITIZE_STRING);
            if (empty($password)) {
                $errors['customer_password'] = 'Please provide a password.';
            }

            if (!empty($errors)) {
                $errors['is_customer_new'] = 'Please provide valid details for the new customer.';
            }

            // If no issues with the customer data submitted, create a new WP User
            if (empty($errors)) {

                $user_id = wp_create_user(sanitize_user($data['customer_email'], true), $password, $data['customer_email']);

                $data['user_id'] = $user_id;
            }
        }
        // Has an existing customer been selected?
        else {

            $existing_customer_email = filter_input(INPUT_POST, 'existing_customer_email', FILTER_SANITIZE_STRING);

            $user_id = email_exists($existing_customer_email);
            if (!$user_id) {
                $errors['existing_customer_email'] = 'Customer not found.';
            }

            // Retrieve the customer's data
            $user_info = get_userdata($user_id);

            $data = array(
                'user_id' => $user_id,
                'customer_first_name' => $user_info->first_name,
                'customer_last_name' => $user_info->last_name,
                'customer_email' => $user_info->user_email,
                'customer_phone_number' => get_user_meta($user_id, 'billing_phone', true),
                'customer_billing_address' => get_user_meta($user_id, 'billing_address_1', true),
                'customer_billing_suburb' => get_user_meta($user_id, 'billing_city', true),
                'customer_billing_state' => get_user_meta($user_id, 'billing_state', true),
                'customer_billing_postcode' => get_user_meta($user_id, 'billing_postcode', true),
                'special_requirements' => get_user_meta($user_id, 'special_requirements', true),
                'emergency_contact_name' => get_user_meta($user_id, 'emergency_name', true),
                'emergency_contact_relationship' => get_user_meta($user_id, 'emergency_relationship', true),
                'emergency_contact_phone' => get_user_meta($user_id, 'emergency_phone', true)
            );
        }

        if (empty($errors)) {
            $data['current_step'] = 2;
        }

        // Save the customer data to the Submission
        $submission = enrolment_wizard_save_submission($submission['id'], $data);

        if (!empty($errors)) {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-1.php');
        }
        else {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-2.php');
        }
    }
    /**
     * Step 2
     * Populating the customer details
     */
    else if ($step === 2) {

        $data = array(
            'customer_first_name' => filter_input(INPUT_POST, 'customer_first_name', FILTER_SANITIZE_STRING),
            'customer_last_name' => filter_input(INPUT_POST, 'customer_last_name', FILTER_SANITIZE_STRING),
            'customer_phone_number' => filter_input(INPUT_POST, 'customer_phone_number', FILTER_SANITIZE_STRING),
            'customer_billing_address' => filter_input(INPUT_POST, 'customer_billing_address', FILTER_SANITIZE_STRING),
            'customer_billing_suburb' => filter_input(INPUT_POST, 'customer_billing_suburb', FILTER_SANITIZE_STRING),
            'customer_billing_state' => filter_input(INPUT_POST, 'customer_billing_state', FILTER_SANITIZE_STRING),
            'customer_billing_postcode' => filter_input(INPUT_POST, 'customer_billing_postcode', FILTER_SANITIZE_STRING),
            'special_requirements' => filter_input(INPUT_POST, 'special_requirements', FILTER_SANITIZE_STRING),
            'emergency_contact_name' => filter_input(INPUT_POST, 'emergency_contact_name', FILTER_SANITIZE_STRING),
            'emergency_contact_relationship' => filter_input(INPUT_POST, 'emergency_contact_relationship', FILTER_SANITIZE_STRING),
            'emergency_contact_phone' => filter_input(INPUT_POST, 'emergency_contact_phone', FILTER_SANITIZE_STRING)
        );

        if (empty($data['customer_first_name'])) {
            $errors['customer_first_name'] = 'Please provide the customer\'s first name.';
        }
        if (empty($data['customer_last_name'])) {
            $errors['customer_last_name'] = 'Please provide the customer\'s last name.';
        }
        if (empty($data['customer_phone_number'])) {
            $errors['customer_phone_number'] = 'Please provide the customer\'s phone number.';
        }
        if (empty($data['customer_billing_address'])) {
            $errors['customer_billing_address'] = 'Please provide the customer\'s billing address.';
        }
        if (empty($data['customer_billing_suburb'])) {
            $errors['customer_billing_suburb'] = 'Please provide the customer\'s billing suburb.';
        }
        if (empty($data['customer_billing_state'])) {
            $errors['customer_billing_state'] = 'Please provide the customer\'s billing state.';
        }
        if (empty($data['customer_billing_postcode'])) {
            $errors['customer_billing_postcode'] = 'Please provide the customer\'s billing postcode.';
        }

        if (empty($errors)) {
            $data['current_step'] = 3;
        }

        $submission = enrolment_wizard_save_submission($submission['id'], $data);

        if (!empty($errors)) {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-2.php');
        }
        else {

            $user_id = $submission['user_id'];

            // Due to stupidity of wp_insert_user & wp_update_user (issues with email and user_login) easier to update one by one :(
            update_user_meta($user_id, 'first_name', $data['customer_first_name']);
            update_user_meta($user_id, 'last_name', $data['customer_last_name']);
            update_user_meta($user_id, 'billing_phone', $data['customer_phone_number']);
            update_user_meta($user_id, 'billing_address_1', $data['customer_billing_address']);
            update_user_meta($user_id, 'billing_city', $data['customer_billing_suburb']);
            update_user_meta($user_id, 'billing_state', $data['customer_billing_state']);
            update_user_meta($user_id, 'billing_postcode', $data['customer_billing_postcode']);
            update_user_meta($user_id, 'special_requirements', $data['special_requirements']);
            update_user_meta($user_id, 'emergency_name', $data['emergency_contact_name']);
            update_user_meta($user_id, 'emergency_relationship', $data['emergency_contact_relationship']);
            update_user_meta($user_id, 'emergency_phone', $data['emergency_contact_phone']);

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-3.php');
        }
    }
    /**
     * Step 3
     * Does the order require membership?
     */
    else if ($step === 3) {

        $membership_product_id = filter_input(INPUT_POST, 'membership_product_id', FILTER_VALIDATE_INT);

        if (!empty($membership_product_id)) {

            $data = array(
                'membership_product_id' => $membership_product_id
            );
        }

        if (empty($errors)) {
            $data['current_step'] = 4;
        }

        $submission = enrolment_wizard_save_submission($submission['id'], $data);

        if (!empty($errors)) {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-3.php');
        }
        else {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-4.php');
        }
    }
    /**
     * Step 4
     * Choose courses
     */
    else if ($step === 4) {

        $submission_id = enrolment_wizard_get_submission_id();

        $courses = array();

        if (isset($_POST['course']) && !empty($_POST['course'])) {

            $courses = $_POST['course'];

            // TODO: Split the following out for readability, tidy up later

            // Delete any courses
            foreach ($courses as $key => $course) {

                if (isset($course['delete']) && !empty($course['delete'])) {

                    $wpdb->delete(
                        'wp_enrolment_wizard_enrolments',
                        array(
                            'id' => $course['id']
                        )
                    );

                    unset($courses[$key]);
                }
            }

            // Validate submitted courses
            foreach ($courses as $key => $course) {

                if (empty($course['course_id']) || empty($course['name'])) {

                    $errors['courses'] = 'Please ensure all courses are valid.';
                }
            }

            // Validate course quantities
            $course_quantities = array();
            foreach ($courses as $key => $course) {

                if (isset($course_quantities[$course['course_id']])) {

                    $course_quantities[$course['course_id']] += 1;
                }
                else {

                    $course_quantities[$course['course_id']] = 1;
                }
            }
            foreach ($course_quantities as $course_id => $quantity) {

                $product = wc_get_product($course_id);

                $stock_quantity = $product->get_stock_quantity();

                if ($stock_quantity < $quantity) {

                    $errors['courses'] = 'Not enough positions available for ' . $product->get_title() . ' (only ' . $stock_quantity . ' spots available). ';
                }
            }
        }

        // Courses selected, and no errors, save!
        if (!empty($courses) && empty($error)) {

            // Save courses
            foreach ($courses as $key => $course) {

                $data = array(
                    'submission_id' => $submission_id,
                    'course_id' => $course['course_id'],
                    'course_name' => $course['name']
                );

                // If existing, update record
                if (isset($course['id'])) {

                    $wpdb->update(
                        'wp_enrolment_wizard_enrolments',
                        $data,
                        array(
                            'id' => $course['id']
                        )
                    );
                }
                // If new, create record
                else {

                    $wpdb->insert(
                        'wp_enrolment_wizard_enrolments',
                        $data
                    );
                }
            }
        }

        if (empty($errors)) {
            $data['current_step'] = 5;
        }

        $submission = enrolment_wizard_get_submission_data($submission_id);

        if (!empty($errors)) {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-4.php');
        }
        else {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-5.php');
        }
    }
    /**
     * Step 5
     */
    else if ($step === 5) {

        $submission_id = enrolment_wizard_get_submission_id();

        if (isset($_POST['enrolments']) && !empty($_POST['enrolments'])) {
            foreach ($_POST['enrolments'] as $key => $enrolment) {

                $data = $enrolment;
                $result = enrolment_wizard_validate_enrolee_data($data);

                if (is_array($result)) {

                    $errors[$key] = $result;
                }

                $wpdb->update(
                    'wp_enrolment_wizard_enrolments',
                    $data,
                    array(
                        'id' => $key
                    )
                );
            }
        }

        if (empty($errors)) {
            $data = array(
                'current_step' => 6
            );
            $submission = enrolment_wizard_save_submission($submission['id'], $data);
        }

        $submission = enrolment_wizard_get_submission_data($submission_id);

        if (!empty($errors)) {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-5.php');
        }
        else {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-6.php');
        }
    }
    /**
     * Step 6
     */
    else if ($step === 6) {

        $submission_id = enrolment_wizard_get_submission_id();

        $is_cc_payment = filter_input(INPUT_POST, 'is_cc_payment', FILTER_VALIDATE_BOOLEAN);
        $has_read_terms_conditions = filter_input(INPUT_POST, 'has_read_terms_conditions', FILTER_VALIDATE_BOOLEAN);

        if ($is_cc_payment) {

            $data = array(
                'is_cc_payment' => true,
                'has_read_terms_conditions' => $has_read_terms_conditions
            );
        }
        else {

            if (empty(ifne($_POST, 'other_payment_methods'))) {
                $errors['other_payment_methods'] = 'Please select a payment method.';
            }

            $data = array(
                'is_cc_payment' => false,
                'other_payment_methods' => implode(', ', $_POST['other_payment_methods']),
                'payment_notes' => filter_input(INPUT_POST, 'payment_notes', FILTER_SANITIZE_STRING),
                'has_read_terms_conditions' => $has_read_terms_conditions
            );
        }

        if (empty($data['has_read_terms_conditions'])) {
            $errors['has_read_terms_conditions'] = 'Please accept the terms and conditions.';
        }

        if (empty($errors)) {
            $data['current_step'] = 7;
        }

        $submission = enrolment_wizard_save_submission($submission['id'], $data);

        if (!empty($errors)) {
            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-6.php');
        }
        else {
            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-7.php');
        }
    }
    /**
     * Step 7
     */
    else if ($step === 7) {

        $submission_id = enrolment_wizard_get_submission_id();

        $submission = enrolment_wizard_get_submission_data($submission_id);

        if (!empty($errors)) {

            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-7.php');
        }
        else {

            if (!ifne($submission, 'is_cc_payment')) {
                include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-8-other.php');
            }
        }

//        if(!empty($submission['order_id'])) {
//
//            include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-7.php');
//
//        }

    }
    /**
     * Step 8
     */
//    else if ($step === 8) {
//
//        $submission_id = enrolment_wizard_get_submission_id();
//
//        $submission = enrolment_wizard_get_submission_data($submission_id);
//    }

    wp_die();
}
add_action('wp_ajax_nopriv_enrolment-wizard-parse-step', 'enrolment_wizard_ajax_parse_step');
add_action('wp_ajax_enrolment-wizard-parse-step', 'enrolment_wizard_ajax_parse_step');

function enrolment_wizard_validate_enrolee_data($data)
{

    $errors = array();

    if (empty($data['first_name'])) {
        $errors['first_name'] = 'Please provide the enrolee\'s first name.';
    }
    if (empty($data['last_name'])) {
        $errors['last_name'] = 'Please provide the enrolee\'s last name.';
    }
    if (isset($data['age'])) {
        if (empty($data['age'])) {
            $errors['age'] = 'Please provide the enrolee\'s age.';
        }
    }
    if (empty($data['phone'])) {
        $errors['phone'] = 'Please provide the enrolee\'s phone.';
    }
    if (empty($data['email'])) {
        $errors['email'] = 'Please provide the enrolee\'s email.';
    }
    if (empty($data['special_requirements'])) {
        $errors['special_requirements'] = 'Please provide the enrolee\'s special requirements.';
    }
    if (empty($data['emergency_contact_name'])) {
        $errors['emergency_contact_name'] = 'Please provide the enrolee\'s emergency contact name.';
    }
    if (empty($data['emergency_contact_relationship'])) {
        $errors['emergency_contact_relationship'] = 'Please provide the enrolee\'s emergency contact relationship.';
    }
    if (empty($data['emergency_contact_phone'])) {
        $errors['emergency_contact_phone'] = 'Please provide the enrolee\'s emergency contact phone.';
    }

    if (empty($errors)) {

        return true;
    }

    return $errors;
}

function enrolment_wizard_remove_old_submissions()
{

    global $wpdb;

    $sql = "DELETE e, s

    FROM wp_enrolment_wizard_enrolments AS e

    INNER JOIN wp_enrolment_wizard_submissions AS s
    ON e.submission_id = s.id

    WHERE s.created_at < '" . date('Y-m-d H:i:s', strtotime('-2 days')) . "'
    AND s.status IS NULL
    ";

    $submissions = $wpdb->query($sql);
}

/**
 * Create an order based on the submitted data
 *
 * @param $submission
 * @return WC_Order|WP_Error
 */
function enrolment_wizard_create_order($submission)
{
    global $wpdb;

    $apply_membership_discount = false;

    $args = array(
        'customer_id' => ifne($submission, 'user_id')
    );

    // If order exists, update it rather than create it
    if (ifne($submission, 'order_id')) {

        $args['order_id'] = ifne($submission, 'order_id');
    }

    // Create the order
    $order = wc_create_order($args);

    // Remove any existing products in case the order existed and to avoid duplicates
    $items = $order->get_items();

    foreach ($items as $key => $product) {

        wc_delete_order_item($key);
    }

    // If purchasing membership?
    if (ifne($submission, 'membership_product_id')) {

        $product = wc_get_product(ifne($submission, 'membership_product_id'));

        $apply_membership_discount = true;

        $order->add_product($product, 1);
    }

    if (ifne($submission, 'user_id')) {

        if (enrolment_wizard_is_customer_member(ifne($submission, 'user_id'))) {

            $apply_membership_discount = true;
        }
    }

    // Are purchasing courses?
    $sql = "SELECT * FROM wp_enrolment_wizard_enrolments WHERE submission_id = " . ifne($submission, 'id');
    $enrolments = $wpdb->get_results($sql, ARRAY_A);

    foreach ($enrolments as $enrolment) {

        $product = wc_get_product(ifne($enrolment, 'course_id'));

        $price = $product->get_price();

        // Is this a kids course?
        $is_kids_course = get_field('is_kids_course', $product->get_id());

        // Apply the membership discount if applicable
        if ($apply_membership_discount && !$is_kids_course) {

            $discount_percentage = get_field('membership_discount', 'options');
            $discount_rate = $discount_percentage / 100;

            $discount_amount = $price * $discount_rate;
            $price -= $discount_amount;
        }

        $price_ex_tax = wc_get_price_excluding_tax($product, [
            'qty' => 1,
            'price' => $price,
        ]);
        $tax = ($price * 1) - $price_ex_tax;

        $order->add_product($product, 1, array(
            'totals' => array(
                'subtotal' => $price_ex_tax,
                'total' => $price_ex_tax,
                'subtotal_tax' => $tax,
                'tax' => $tax
            )
        ));
    }

    // Setup the address details
    $address = array(
        'first_name' => ifne($submission, 'customer_first_name'),
        'last_name'  => ifne($submission, 'customer_last_name'),
        'company'    => '',
        'email'      => ifne($submission, 'customer_email'),
        'phone'      => ifne($submission, 'customer_phone_number'),
        'address_1'  => ifne($submission, 'customer_billing_address'),
        'address_2'  => '',
        'city'       => ifne($submission, 'customer_billing_suburb'),
        'state'      => ifne($submission, 'customer_billing_state'),
        'postcode'   => ifne($submission, 'customer_billing_postcode'),
        'country'    => 'AU'
    );

    $order->set_address($address, 'billing');
    $order->set_address($address, 'shipping');

    $total = $order->calculate_totals(true);

    return $order;
}

function enrolment_wizard_ajax_successful_cc_purchase()
{
    // commented out early - eway doesnt give errors to prevent this now.
    echo json_encode(array(
        'status' => 'OK'
    ));
    die();

    error_log('enrolment_wizard_ajax_successful_cc_purchase');
    $submission_id = filter_input(INPUT_POST, 'submission_id', FILTER_SANITIZE_STRING);

    error_log(var_export($submission_id, true));

    $submission = enrolment_wizard_get_submission_data($submission_id);

    error_log(var_export($submission, true));

    enrolment_wizard_create_enrolments($submission);

    $submission = enrolment_wizard_save_submission(
        $submission['id'],
        array(
            'status' => 'complete'
        )
    );

    echo json_encode(array(
        'status' => 'OK'
    ));
    wp_die();
}
add_action('wp_ajax_nopriv_enrolment-wizard-successful-cc-purchase', 'enrolment_wizard_ajax_successful_cc_purchase');
add_action('wp_ajax_enrolment-wizard-successful-cc-purchase', 'enrolment_wizard_ajax_successful_cc_purchase');

/**
 * Create enrolments based on the courses purchased
 *
 * @param $submission
 * @return mixed
 */
function enrolment_wizard_create_enrolments($submission)
{
    global $wpdb;

    $successful_enrolments = array();

    $sql = "SELECT * FROM wp_enrolment_wizard_enrolments WHERE submission_id = " . ifne($submission, 'id');
    $enrolments = $wpdb->get_results($sql, ARRAY_A);

    foreach ($enrolments as $enrolment) {

        $child = ifne($enrolment, 'age') ? true: false;
        $method_of_payment = ifne($submission, 'is_cc_payment') ? 'Online': 'Offline';

        $data = array(
            'course_id'                 => ifne($enrolment, 'course_id'),
            'user_id'                   => ifne($submission, 'user_id'),
            'order_id'                  => ifne($submission, 'order_id'),
            'title'                     => ifne($enrolment, 'title'),
            'preferred_pronoun'         => ifne($enrolment, 'preferred_pronoun'),
            'first_name'                => ifne($enrolment, 'first_name'),
            'last_name'                 => ifne($enrolment, 'last_name'),
            'phone'                     => ifne($enrolment, 'phone'),
            'email'                     => ifne($enrolment, 'email'),
            'special_requirements'      => ifne($enrolment, 'special_requirements'),
            'emergency_name'            => ifne($enrolment, 'emergency_contact_name'),
            'emergency_relationship'    => ifne($enrolment, 'emergency_contact_relationship'),
            'emergency_phone'           => ifne($enrolment, 'emergency_contact_phone'),
            'child'                     => $child,
            'age'                       => ifne($enrolment, 'age'),
            'method_of_payment'         => $method_of_payment,
            'notes'                     => '',
            'created_at'                => date('Y-m-d H:i:s'),
            'updated_at'                => date('Y-m-d H:i:s')
        );

        $wpdb->insert(
            'wp_enrolments',
            $data
        );

        freoarts_send_enrolment_confirmation_email($data);

        $data['id'] = $wpdb->insert_id;

        $successful_enrolments[] = $data;
    }

    return $successful_enrolments;
}

/**
 * Update order to pay via Other payment method
 *
 * @param $submission
 * @param $order
 */
function enrolment_wizard_process_other_payment($submission, $order)
{

    update_post_meta($order->get_id(), '_payment_method', 'other_payment_method');
    update_post_meta($order->get_id(), '_payment_method_title', ifne($submission, 'other_payment_methods'));
    update_post_meta($order->get_id(), '_via_admin', true);

    $notes = "Payment Method Notes:\n" . ifne($submission, 'payment_notes');
    $order->add_order_note(nl2br($notes));

    $total = $order->calculate_totals(true);

    // $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    $gateways = WC()->payment_gateways->payment_gateways();

    $result = $gateways['other_payment_method']->process_payment($order->get_id());

    if ($result['result'] == 'success') {
        return $result['redirect'];
    }
    else {
        return false;
    }
}

function enrolment_wizard_process_cc_payment($submission, $order)
{
    $total = $order->calculate_totals(true);

    $FA_WC_Gateway_EWAY = new FA_WC_Gateway_EWAY();

    $FA_WC_Gateway_EWAY->public_set_token_customer_id($order, 'new');

    $result = $FA_WC_Gateway_EWAY->public_request_access_code($order);

    update_post_meta($order->get_id(), '_payment_method', 'eway');
    update_post_meta($order->get_id(), '_payment_method_title', 'Credit Card');
    update_post_meta($order->get_id(), '_via_admin', true);

    $access_code = esc_attr($result->AccessCode);

    if ($access_code) {
        return $access_code;
    }
    else {
        return false;
    }
}

/**
 * Save enrolment wizard submission to the database
 *
 * @param $data
 * @return array|null|object|void
 */
function enrolment_wizard_save_submission($id, $data = [])
{

    global $wpdb;

    $submission_id = $id ? $id : enrolment_wizard_get_submission_id();

    $data['updated_at'] = date('Y-m-d H:i:s');

    $wpdb->update(
        'wp_enrolment_wizard_submissions',
        $data,
        array(
            'id' => $submission_id
        )
    );

    $submission = enrolment_wizard_get_submission_data($submission_id);

    return $submission;
}

/**
 * Get submission data
 *
 */
function enrolment_wizard_get_submission_data($submission_id)
{

    global $wpdb;

    $submission = $wpdb->get_row("SELECT * FROM wp_enrolment_wizard_submissions WHERE id = " . $submission_id, ARRAY_A);

    return $submission;
}

/**
 * Get submission ID if exists, or create new submission and get it's new ID
 *
 */
function enrolment_wizard_get_submission_id()
{

    global $wpdb;

    $submission_id = filter_input(INPUT_POST, 'submission_id', FILTER_VALIDATE_INT);

    if (empty($submission_id)) {

        $wpdb->insert(
            'wp_enrolment_wizard_submissions',
            array(
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            )
        );

        $submission_id = $wpdb->insert_id;
    }

    return $submission_id;
}

/**
 * Build Enrolment Wizard admin page
 *
 */
function enrolment_wizard_admin_view()
{
    global $submission;
    ?>

    <style>

        span.error {
            display: block;
            color: red;
        }

        .postbox-container {
            opacity: 0.3;
        }

        .postbox-container.active {
            opacity: 1;
        }

        .postbox-container .mask {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
        }

        .postbox-container.active .mask {
            display: none;
        }

        .enrolment-forms {
        }

        th.a-left,
        td.a-left {
            text-align: left;
        }

        th.a-right,
        td.a-right {
            text-align: right;
        }

    </style>

    <div class="wrap page-enrolment-wizard">
        <h1>Enrolment Wizard</h1>
        <div id="poststuff">
            <div id="enrolment-wizard">
                <?php

                if (isset($_GET['submission_id']) && !empty($_GET['submission_id'])) {

                    $submission = enrolment_wizard_get_submission_data($_GET['submission_id']);
                }

                if (ifne($submission, 'status') == 'complete') {

                    echo '<p>This order has already been processed. Please create a new enrolment wizard submission.</p>';
                }
                else {

                    $init_step = 1;

                    if (isset($submission) && !empty($submission['current_step'])) {

                        $init_step = $submission['current_step'];
                    }

                    if (isset($_GET['step']) && !empty($_GET['step'])) {

                        $init_step = $_GET['step'];
                    }

                    include_once(TEMPLATEPATH . '/functions/enrolment-wizard/step-' . $init_step . '.php');
                }
                ?>
            </div>
        </div>
    </div> <!-- .wrap -->

    <?php
}

/**
 * Build Enrolment Wizard admin list page
 *
 */
function enrolment_wizard_admin_list()
{
    global $wpdb;
    ?>

    <div class='wrap'>

        <h1 class="wp-heading-inline">Existing Submissions</h1>

        <hr class="wp-header-end">

        <?php
        $sql = "SELECT * FROM wp_enrolment_wizard_submissions WHERE status IS NULL ORDER BY created_at DESC";

        $submissions = $wpdb->get_results($sql, ARRAY_A);
        ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Created At</th>
                    <th>Submission ID</th>
                    <th>Status</th>
                    <th>Order ID</th>
                    <th>Email Address</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td>
                        <?php echo ifne($submission, 'created_at'); ?>
                    </td>
                    <td>
                        <a href="<?php echo get_admin_url(null, 'admin.php?page=enrolment-wizard-main&submission_id=' . ifne($submission, 'id')); ?>">
                            #<?php echo ifne($submission, 'id'); ?>
                        </a>
                    </td>
                    <td>
                        <?php echo empty(ifne($submission, 'status')) ? 'Incomplete': ifne($submission, 'status'); ?>
                    </td>
                    <td>
                        <?php if (!empty(ifne($submission, 'order_id'))): ?>
                        <a href="<?php echo get_admin_url(null, 'post.php?post=' . ifne($submission, 'order_id') . '&action=edit'); ?>">
                            #<?php echo ifne($submission, 'order_id'); ?>
                        </a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php echo ifne($submission, 'customer_email'); ?>
                    </td>
                    <td>
                        <a class="button" href="<?php echo get_admin_url(null, 'admin.php?page=enrolment-wizard-main&submission_id=' . ifne($submission, 'id')); ?>" title="Resume">Resume</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>

    <?php
}

/**
 * Step links
 *
 */
function enrolment_wizard_get_step_links($step, $submission_id)
{

    $step_label = array(
        1 => 'Customer',
        2 => 'Customer Details',
        3 => 'Membership',
        4 => 'Courses',
        5 => 'Enrolees',
        6 => 'Payment Type',
        7 => 'Review'
    );

    $steps = 7;

    echo '<p>';

    for ($i = 1; $i <= $steps; $i++) {

        if ($i < $step || $step == 'payment') {

            echo '<a href="' . get_admin_url(null, 'admin.php?page=enrolment-wizard-main&submission_id=' . $submission_id . '&step=' . $i . '') . '" title="">Step ' . $i . '</a>';
        }
        elseif ($i == $step) {

            echo '<span><strong style="color: #f1f1f1; background: #888; padding: 3px 5px; border-radius: 3px;">Step&nbsp;' . $i . '</strong></span>';
        }
        else {

            echo '<span>Step ' . $i . '</span>';
        }

        echo ' | ';
    }

//    if ($step == 'payment') {
//
//        echo '<span><strong style="color: #f1f1f1; background: #888; padding: 3px 5px; border-radius: 3px;">Process Payment</strong></span>';
//    }
//    else {
//
//        echo '<span>Process Payment</span>';
//    }

    echo '</p>';
}

/**
 * AJAX method to retrieve Course form HTML
 *
 */
function enrolment_wizard_ajax_get_course_form()
{

    global $woocommerce;
    global $wpdb;

    $response = [
        'message' => ''
    ];

    $increment = filter_input(INPUT_POST, 'increment', FILTER_VALIDATE_INT);

    ob_start();

    ?>
    <div class="acf-field acf-field-select" data-increment="<?php echo $increment; ?>">
        <div class="acf-label">
            <label>Course #<span class="course-increment"><?php echo $increment + 1; ?></span> Enrolment</label>
            <p class="description">Start typing course name locate existing course.</p>
        </div>
        <div class="acf-input">
            <div class="acf-input-wrap">
                <input type="text" class="course-name" data-increment="<?php echo $increment; ?>" name="course[<?php echo $increment; ?>][name]" value="">
                <input type="hidden" class="course-id" data-increment="<?php echo $increment; ?>" name="course[<?php echo $increment; ?>][course_id]" value="">
                <br><a class="remove-course" href="#" title="Remove course" data-increment="<?php echo $increment; ?>">Remove Course</a>
            </div>
        </div>
    </div>

    <?php

    $html = ob_get_clean();

    $response['status'] = 'OK';
    $response['increment'] = $increment;
    $response['html'] = $html;

    echo json_encode($response);

    wp_die();
}
add_action('wp_ajax_nopriv_enrolment-wizard-get-course-form', 'enrolment_wizard_ajax_get_course_form');
add_action('wp_ajax_enrolment-wizard-get-course-form', 'enrolment_wizard_ajax_get_course_form');

/**
 * Display customer details
 *
 */
function enrolment_wizard_get_customer_details($submission)
{

    ?>
    <p>
        <?php echo ifne($submission, 'customer_first_name'); ?> <?php echo ifne($submission, 'customer_last_name'); ?><br />
        <?php echo ifne($submission, 'customer_email'); ?><br />
        <?php echo ifne($submission, 'customer_phone_number'); ?>
    </p>
    <p>
        <?php echo ifne($submission, 'customer_billing_address'); ?><br />
        <?php echo ifne($submission, 'customer_billing_suburb'); ?><br />
        <?php echo ifne($submission, 'customer_billing_state'); ?><br />
        <?php echo ifne($submission, 'customer_billing_postcode'); ?>
    </p>
    <p>
        Special Requirements:<br />
        <?php echo ifne($submission, 'special_requirements'); ?><br />
        Emergency Contact Name:<br />
        <?php echo ifne($submission, 'emergency_contact_name'); ?><br />
        Emergency Contact Relationship:<br />
        <?php echo ifne($submission, 'emergency_contact_relationship'); ?><br />
        Emergency Contact Phone:<br />
        <?php echo ifne($submission, 'emergency_contact_phone'); ?>
    </p>
    <?php
}

/**
 * Display basic HTML of cart and totals
 *
 */
function enrolment_wizard_get_cart_review($submission)
{
    global $wpdb;

    ob_start();
?>
<table>
    <thead>
    <tr>
        <th class="a-left" style="padding: 0 10px 10px 0;">Item</th>
        <th class="a-right">Cost</th>
    </tr>
    </thead>
    <tbody>
    <?php

    $apply_membership_discount = false;
    $total = 0;

    // If purchasing membership?
    if (ifne($submission, 'membership_product_id')) {

        $product = wc_get_product(ifne($submission, 'membership_product_id'));

        echo '<tr>';
        echo '<td style="padding: 0 10px 10px 0;">' . $product->get_name() . '</td>';
        echo '<td class="a-right">$' . $product->get_price() . '</td>';
        echo '</tr>';

        $apply_membership_discount = true;

        $total += $product->get_price();
    }

    if (ifne($submission, 'user_id')) {

        if (enrolment_wizard_is_customer_member(ifne($submission, 'user_id'))) {

            $apply_membership_discount = true;
        }
    }

    $sql = "SELECT * FROM wp_enrolment_wizard_enrolments WHERE submission_id = " . ifne($submission, 'id');
    $enrolments = $wpdb->get_results($sql, ARRAY_A);

    foreach ($enrolments as $enrolment) {

        $product = wc_get_product(ifne($enrolment, 'course_id'));

        $price = $product->get_price();

        // Is this a kids course?
        $is_kids_course = get_field('is_kids_course', $product->get_id());

        // Apply the membership discount if applicable
        if ($apply_membership_discount && !$is_kids_course) {

            $discount_percentage = get_field('membership_discount', 'options');
            $discount_rate = $discount_percentage / 100;

            $discount_amount = $price * $discount_rate;
            $price -= $discount_amount;
        }

        $price_ex_tax = wc_get_price_excluding_tax($product, [
            'qty' => 1,
            'price' => $price,
        ]);
        $tax = ($price * 1) - $price_ex_tax;

        echo '<tr>';
        echo '<td style="padding: 0 10px 10px 0;">';
        echo $product->get_name() . '<br>';
        echo '<em>' . ifne($enrolment, 'first_name') . ' ' . ifne($enrolment, 'last_name') . '</em>';
        echo '</td>';
        if ($price == $product->get_price()) {
            echo '<td class="a-right" style="vertical-align: top;">$' . number_format($price, 2, '.', ' ') . '</td>';
        }
        else {
            echo '<td class="a-right" style="vertical-align: top;"><del>$' . $product->get_price() . '</del> $' . number_format($price, 2, '.', ' ') . '</td>';
        }
        echo '</tr>';

        $total += $price;
    }
    ?>
    </tbody>
    <tfoot>
    <tr>
        <td><strong>TOTAL</strong></td>
        <td class="a-right"><strong>$<?php echo number_format($total, 2, '.', ' '); ?></strong></td>
    </tr>
    </tfoot>
</table>
<?php
    $output = ob_get_contents();
    ob_end_clean();

    return [
        'total' => $total * 100,
        'html' => $output,
    ];
}

/**
 * Check if user is a member or admin
 *
 * @return bool
 */
function enrolment_wizard_is_customer_member($user_id)
{

    $user = get_userdata($user_id);

    $roles = array('member_individual', 'member_concession', 'administrator');

    foreach ($roles as $role) {

        if (in_array($role, $user->roles)) {

            return true;
        }
    }

    return false;
}

function enrolment_wizard_ajax_get_users() {
    global $wpdb;

    $search_term = $_REQUEST['search_term'] ?? '';

    $sql = "SELECT
        u.ID,
        u.user_email
    FROM wp_users AS u
    WHERE u.user_email LIKE '%{$search_term}%'
    ORDER BY u.user_email ASC
    ";

    $users = $wpdb->get_results($sql, OBJECT);

    $source = array();
    foreach ($users as $user) {
        $source[] = [
          'label' => $user->user_email,
          'value' => $user->ID
        ];
    }

    echo json_encode($source);

    wp_die();
}
add_action('wp_ajax_nopriv_enrolment-wizard-get-users', 'enrolment_wizard_ajax_get_users');
add_action('wp_ajax_enrolment-wizard-get-users', 'enrolment_wizard_ajax_get_users');