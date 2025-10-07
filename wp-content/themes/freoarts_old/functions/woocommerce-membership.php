<?php

/****************************************************
 *
 * WOOCOMMERCE - MEMBERSHIP
 *
 ****************************************************/

/**
 * Check if cart has Membership product
 *
 * @return bool
 */
function cart_has_membership_item()
{
    global $woocommerce;

    // If Membership product exists in cart, flag membership discount
    foreach ($woocommerce->cart->cart_contents as $item) {

        if ($item['data']->get_type() == 'membership') {

            return true;
        }
    }

    return false;
}

/**
 * Check if current user is a member or admin
 *
 * @return bool
 */
function user_is_member()
{

    $user = wp_get_current_user();

    $roles = array('member_individual', 'member_concession', 'administrator');

    foreach ($roles as $role) {

        if (in_array($role, $user->roles)) {

            return true;
        }
    }

    return false;
}

/**
 * Only allow one membership item to be added to the cart at one time
 *
 * @param $passed
 * @param $product_id
 * @param $quantity
 * @param string $variation_id
 * @param string $variations
 * @return bool
 */
function limit_cart_membership_items($passed, $product_id, $quantity, $variation_id = '', $variations = '')
{

    $_product = wc_get_product($product_id);

    if (cart_has_membership_item() && $_product->get_type() == 'membership') {

        $passed = false;
        wc_add_notice('Only one membership allowed per account', 'error');
    }

    return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'limit_cart_membership_items', 10, 5);

/**
 * If cart contains Membership product, or customer is a Member, apply discounts
 *
 * @param $price
 * @param $values
 * @param $wp_cart
 * @return mixed
 */
function freoarts_get_membership_price($price, $values, $wp_cart)
{
    global $woocommerce;

    $discount_percentage = get_field('membership_discount', 'options');

    $discount_rate = $discount_percentage / 100;

    $has_membership_role = user_is_member(); // Is user a Member?
    $has_membership_item = cart_has_membership_item(); // Does cart contain Membership product?

    $product = $values['data']; // Set product object

    // Do not apply discount to kids courses
    $is_kids_course = get_field('is_kids_course', $product->get_id());

    // If user is (or will be) a Member, apply discount
    if (
        ($has_membership_role || $has_membership_item)
        && $product->get_type() != 'membership'
        && !$is_kids_course
    ) {

        $discount_amount = $price * $discount_rate;
        $price -= $discount_amount;

        $total_discount = $discount_amount * $values['quantity'];
        $total_discount_tax = 0;

        if (wc_tax_enabled()) {
            $tax_rates          = WC_Tax::get_rates($product->get_tax_class());
            $taxes              = WC_Tax::calc_tax($discount_amount, $tax_rates, $woocommerce->cart->prices_include_tax);
            $total_discount_tax = WC_Tax::get_tax_total($taxes) * $values['quantity'];
            $total_discount     = $woocommerce->cart->prices_include_tax ? $total_discount - $total_discount_tax : $total_discount;
            $woocommerce->cart->discount_cart_tax += $total_discount_tax;
        }

        $woocommerce->cart->discount_cart += $total_discount;
    }

    return $price;
}
add_filter('woocommerce_get_discounted_price', 'freoarts_get_membership_price', 10, 3);


/**
 * Show discounted subtotal for each line item
 *
 * @param $values
 * @param $item
 * @return string
 */
function freoarts_cart_item_membership_subtotal($values, $item)
{
    // Show the discounted total in the line item's Subtotal
    return wc_price($item['line_total']);
}
// add_filter('woocommerce_cart_item_subtotal', 'freoarts_cart_item_membership_subtotal', 10, 2);


/**
 * Display the membership discount in cart totals
 *
 */
function freoarts_display_membership_discount_total()
{
    global $woocommerce;

    if (!empty($woocommerce->cart->discount_cart)) {

        echo '<tr class="order-total member-total">';
        echo '<th>Membership Discount</th>';
        echo '<td data-title="Membership Discount">' . wc_price($woocommerce->cart->discount_cart) . '</td>';
        echo '</tr>';
    }
}
add_action('woocommerce_cart_totals_before_order_total', 'freoarts_display_membership_discount_total', 10, 1);
add_action('woocommerce_review_order_before_order_total', 'freoarts_display_membership_discount_total', 10, 1);


/**
 * If cart has membership item, do not allow guest checkout, and force customer account creation
 *
 * @param $checkout
 */
function freoarts_disable_guest_checkout($checkout)
{
    global $woocommerce;

    $has_membership_item = cart_has_membership_item();

    if ($has_membership_item) {

        $woocommerce->checkout->must_create_account = true;
        $woocommerce->checkout->enable_guest_checkout = false;
    }
}
add_action('woocommerce_before_checkout_form', 'freoarts_disable_guest_checkout', 10, 1);
add_action('woocommerce_before_checkout_process', 'freoarts_disable_guest_checkout', 10, 1);

/**
 * Add to membership role if successful checkout with membership product
 *
 * @param $result
 * @param $order_id
 * @return mixed
 */
function freoarts_checkout_update_member($order_id)
{
    global $wpdb;

    $order = wc_get_order($order_id);
    $order_items = $order->get_items();

    $user_id = $order->get_user_id();

    foreach ($order_items as $order_item) {

        $_product = wc_get_product($order_item['product_id']);

        if ($_product->get_type() == 'membership' && !empty($user_id)) {

            $membership_type = get_field('membership_type', $_product->get_id());

            $user = new WP_User($user_id);

            if (!empty($membership_type) && !in_array('administrator', $user->roles)) {

                if ($order->get_status() == 'completed') {

                    $user->set_role($membership_type);

                    $old_expiry = get_user_meta($user->ID, 'expiry', true);

                    // If membership is old or doesn't exist, add a year from now
                    if (empty($old_expiry) || strtotime($old_expiry) < time()) {
                        $new_expiry = date('Y-m-d H:i:s', strtotime('+365 days'));
                    }
                    // If membership is still active, add a year to existing period end date
                    else {
                        $new_expiry = date('Y-m-d H:i:s', strtotime($old_expiry . ' +365 days'));
                    }

                    // Update user membership expiry
                    update_user_meta($user->ID, 'expiry', $new_expiry);

                    // Preset the renewal reminder to false
                    update_user_meta($user->ID, 'renewal_reminder', false);

                    $order->add_order_note('Updated membership expiry to ' . $new_expiry, 0, true);
                }
            }
        }
    }
}
add_action('woocommerce_pre_payment_complete', 'freoarts_checkout_update_member');

/**
 * Run cron actions
 *
 */

if (!wp_next_scheduled('freoarts_check_membership')) {
    wp_schedule_event(time(), 'hourly', 'freoarts_check_membership');
}

// wp_clear_scheduled_hook('freoarts_check_membership');

if (!wp_next_scheduled('freoarts_check_course_reminder')) {
    wp_schedule_event(time(), 'hourly', 'freoarts_check_course_reminder');
}

// Check expired memberships daily
if (!wp_next_scheduled('freoarts_expire_members')) {
    wp_schedule_event(time(), 'daily', 'freoarts_expire_members');
}

// wp_clear_scheduled_hook('freoarts_check_course_reminder');

/**
 * Get users and send mail if required
 *
 */
function freoarts_check_and_update_membership()
{

    $args = array(
        'role__in' => array(
            'member_individual',
            'member_concession'
        ),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'expiry',
                'compare' => '<=',
                'value' => date('Y-m-d h:i:s', strtotime('+3 weeks')),
                'type' => 'date'
            ),
            array(
                'key' => 'expiry',
                'compare' => '>',
                'value' => '2017-03-30 00:00:00', // We don't want to send to members prior to the website launch date
                'type' => 'date'
            ),
            array(
                'relation' => 'OR',
                array(
                    'key' => 'renewal_reminder',
                    'value' => false,
                    'type' => 'BOOLEAN'
                ),
                array(
                    'key' => 'renewal_reminder',
                    'compare' => 'NOT EXISTS'
                ),
                array(
                    'key' => 'renewal_reminder',
                    'value' => '0'
                )
            )
        )
    );

    $users = get_users($args);

    if (count($users)) {
        foreach ($users as $user) {

            $expiry_date = get_user_meta($user->ID, 'expiry', true);
            if (empty($expiry_date)) {
                $expiry_date = date('Y-m-d H:i:s');
                update_user_meta($user->ID, 'expiry', $expiry_date);
            }
            $expiry_date = date('F j, Y', strtotime($expiry_date));

            $from_email = get_field('email_address', 'option');
            $phone_number = get_field('phone_number', 'option');

            ob_start();
            include(TEMPLATEPATH . '/parts/email/membership-renewal.php');
            $html_email = ob_get_contents();
            ob_end_clean();

            $subject = 'Fremantle Arts Centre Membership Renewal';
            $from_name = 'Fremantle Arts Centre';
            $to = $user->user_email;
            $email = new HumaanEmail();
            $email->sendEmail($html_email, $subject, $from_name, $from_email, $to);

            update_user_meta($user->ID, 'renewal_reminder', true);
        }
    }
}
add_action('freoarts_check_membership', 'freoarts_check_and_update_membership');


/**
 * Get users and send mail if required
 *
 */
function freoarts_check_and_expire_membership()
{

    $args = array(
        'role__in' => array(
            'member_individual',
            'member_concession'
        ),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'expiry',
                'compare' => '<',
                'value' => date('Y-m-d h:i:s'),
                'type' => 'date'
            )
        )
    );

    $users = get_users($args);

    if (count($users)) {
        foreach ($users as $user) {

            $u = new WP_User($user->ID);

            $u->add_role('subscriber');

            $u->remove_role('member_individual');
            $u->remove_role('member_concession');
        }
    }
}
add_action('freoarts_expire_members', 'freoarts_check_and_expire_membership');

function freoarts_check_and_update_course_reminder()
{
    global $wpdb;

    // Check if notifications paused
    $paused = get_field('pause_notifications', 'options');
    if ($paused) return false;

    // Don't send old reminders
    $sql = "SELECT
        e.*,
        pm1.meta_value AS start_date

    FROM wp_enrolments AS e

    LEFT JOIN wp_posts AS p
    ON e.course_id = p.ID

    LEFT JOIN wp_postmeta AS pm1
    ON p.ID = pm1.post_id
    AND pm1.meta_key = 'start_date'

    WHERE e.trashed != 1
    AND e.reminder_sent != 1
    AND e.archived_course_id IS NULL
    AND pm1.meta_value <= " . date('Ymd', strtotime('+2 days', time())) . "
    AND pm1.meta_value >= " . date('Ymd', time()) . "
    ";

    $results = $wpdb->get_results($sql, ARRAY_A);

    if (count($results)) {
        foreach ($results as $enrolment) {

            $enrolment['course_title']  = get_the_title($enrolment['course_id']);
            $enrolment['course_url']    = get_the_permalink($enrolment['course_id']);
            $enrolment['start_date']    = get_field('start_date', $enrolment['course_id']);
            $enrolment['end_date']      = get_field('end_date', $enrolment['course_id']);
            $enrolment['duration']      = get_field('duration', $enrolment['course_id']);

            $from_email = get_field('email_address', 'option');

            ob_start();
            include(TEMPLATEPATH . '/parts/email/enrolment-reminder.php');
            $html_email = ob_get_contents();
            ob_end_clean();

            $to_email = $enrolment['email'];
            $subject = 'Fremantle Arts Centre Course Reminder';
            $from_name = 'Fremantle Arts Centre';

            $email = new HumaanEmail();
            $email->sendEmail($html_email, $subject, $from_name, $from_email, $to_email);

            $sms_text = 'Fremantle Arts Centre - Reminder. Your course ' . $enrolment['course_title'] . ' is due to start ' . date('D d M Y', strtotime($enrolment['start_date']));

            send_SMS($enrolment['phone'], $sms_text);

            $wpdb->update(
                'wp_enrolments',
                array(
                    'reminder_sent' => 1,
                    'reminder_sent_at' => date('Y-m-d H:i:s')
                ),
                array(
                    'ID' => $enrolment['id']
                )
            );
        }
    }

    return $results;
}
add_action('freoarts_check_course_reminder', 'freoarts_check_and_update_course_reminder');

/**
 * Get course dates in a consistent format
 * This handles both ACF date formats and ensures reliable date retrieval
 *
 * @param int $course_id The course post ID
 * @return array Array with start_date and end_date
 */
function freoarts_get_course_dates($course_id)
{
    $start_date = get_field('start_date', $course_id);
    $end_date = get_field('end_date', $course_id);
    
    // Handle different ACF date formats
    if ($start_date && strlen($start_date) == 8) {
        // Format: YYYYMMDD
        $start_date = DateTime::createFromFormat('Ymd', $start_date);
        $start_date = $start_date ? $start_date->format('Y-m-d') : null;
    }
    
    if ($end_date && strlen($end_date) == 8) {
        // Format: YYYYMMDD  
        $end_date = DateTime::createFromFormat('Ymd', $end_date);
        $end_date = $end_date ? $end_date->format('Y-m-d') : null;
    }
    
    return array(
        'start_date' => $start_date,
        'end_date' => $end_date
    );
}

/**
 * Format course date for display
 *
 * @param string $date_string Date in various formats
 * @return string Formatted date
 */
function freoarts_format_course_date($date_string)
{
    if (empty($date_string)) {
        return 'TBD';
    }
    
    // Handle YYYYMMDD format
    if (strlen($date_string) == 8 && is_numeric($date_string)) {
        $formatted = DateTime::createFromFormat('Ymd', $date_string);
    } else {
        $formatted = new DateTime($date_string);
    }
    
    if ($formatted) {
        return $formatted->format('l, F j, Y');
    }
    
    return $date_string;
}

/**
 * TODO:
 * Check if the saved post is a product
 * Check if the product_type is course
 * Check if the start_date or end_date has changed
 * Trigger email
 * 
 */

// add_action('save_post', 'freoarts_handle_course_date_change', 10, 3);

// Hook into both start_date and end_date ACF fields
// add_filter('acf/update_value/name=start_date', 'freoarts_handle_course_date_change', 10, 3);
// add_filter('acf/update_value/name=end_date', 'freoarts_handle_course_date_change', 10, 3);