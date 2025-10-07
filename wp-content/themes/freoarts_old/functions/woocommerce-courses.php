<?php

/****************************************************
 *
 * WOOCOMMERCE - COURSES
 *
 ****************************************************/


$EnrollmentsFactory = null;

/**
 * Create admin enrolment table
 *
 */
function freoarts_create_enrolment_table()
{

    global $EnrollmentsFactory;

    $EnrollmentsFactory = new EnrolmentsFactory();
    $EnrollmentsFactory->init();
}
add_action('init', 'freoarts_create_enrolment_table', 1);

/**
 * Load the Enrolment admin page assets
 *
 */
function freoarts_enrolment_admin_assets()
{
    wp_enqueue_style('humaan_list_table', get_template_directory_uri() . '/functions/library/css/humaan-list-table.css');
}
add_action('admin_init', 'freoarts_enrolment_admin_assets', 1);

/*
function freoarts_post_type_course_enrolment_list()
{

    global $EnrollmentsFactory;

    $EnrollmentsFactory->createListTable();
    echo $EnrollmentsFactory->renderList();
}
add_action('edit_form_advanced', 'freoarts_post_type_course_enrolment_list', 1);
*/

/**
 * Load custom course page instead of standard WooCommerce product page
 *
 * @param $original_template
 * @return string
 */
function is_course_page($original_template)
{

    global $post;

    if (!empty($post) && !is_search() && $post->post_type == 'product') {

        return get_template_directory() . '/single-course.php';
    } else {

        return $original_template;
    }
}
add_filter('template_include', 'is_course_page');

/**
 * If cart has membership item, do not allow guest checkout
 *
 * @param $checkout
 */
function freoarts_checkout_course_enrolment()
{
    global $woocommerce;

    $cart_contents = $woocommerce->cart->cart_contents;

    $enrolment_increment_id = 0;

    if (!empty($cart_contents)) :
        $output = '<div class="enrolments"><h3>Enrolment Details</h3>';
        $output .= '<p>Please enter the details of the student completing the course. For Kids’ Courses please enter your child’s name.</p>';
        $output .= '<p><a class="blue use-user-details" href="#" title="Use my details">Click here to use my account details for all enrolments</a></p>
    
                        ';

        foreach ($cart_contents as $item) {

            if ($item['data']->get_type() == 'course') {
                for ($i = 0; $i < $item['quantity']; $i++) {

                    $course = $item['data'];

                    ob_start();
?>

                    <div class="enrolment">

                        <p><strong><?php echo $course->get_title(); ?></strong></p>

                        <input type="hidden" name="enrolments[<?php echo $enrolment_increment_id; ?>][course_id]" value="<?php echo $course->get_id(); ?>" />

                        <div class="input-wrap-outer clearfix">
                            <div class="input-wrap clearfix">
                                <label for="first_name_<?php echo $enrolment_increment_id; ?>">First Name <span class="required">*</span></label>
                                <input class="enrolment-first_name" type="text" id="first_name_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][first_name]" value="" />
                            </div>

                            <div class="input-wrap clearfix">
                                <label for="last_name">Last Name <span class="required">*</span></label>
                                <input class="enrolment-last_name" type="text" id="last_name" name="enrolments[<?php echo $enrolment_increment_id; ?>][last_name]" value="" />
                            </div>

                        </div>
                        <div class="input-wrap-outer clearfix">

                            <div class="input-wrap clearfix">
                                <label for="phone_<?php echo $enrolment_increment_id; ?>">Phone <span class="required">*</span></label>
                                <input class="enrolment-phone" type="text" id="phone_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][phone]" value="" />
                            </div>

                            <div class="input-wrap clearfix">
                                <label for="email_<?php echo $enrolment_increment_id; ?>">Email <span class="required">*</span></label>
                                <input class="enrolment-email" type="text" id="email_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][email]" value="" />
                            </div>

                        </div>
                        <div class="input-wrap-outer clearfix one-three">
                            <div class="input-wrap clearfix">
                                <label for="preferred_pronoun_<?php echo $enrolment_increment_id; ?>">Preferred Pronoun</label>
                                <input class="enrolment-preferred-pronoun" type="text" id="preferred_pronoun_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][preferred_pronoun]" value="" />
                            </div>
                            <div class="input-wrap clearfix">
                                <?php $kids_course = get_field('is_kids_course', $course->get_id()); ?>
                                <?php if ($kids_course) { ?>
                                    <label for="age_<?php echo $enrolment_increment_id; ?>">Age <span class="required">*</span></label>
                                    <div class="styled">
                                        <select class="enrolment-age" id="age_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][age]">
                                            <?php for ($j = 18; $j >= 5; $j--) { ?>
                                                <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
                                            <?php } ?>
                                        </select>
                                        <span class="arrow"></span>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="input-wrap clearfix is-special">
                            <label for="special_requirements_<?php echo $enrolment_increment_id; ?>">Do you have any medical conditions/ allergies/access needs that we need to be aware of? <span class="required">*</span></label>
                            <input class="enrolment-special_requirements" type="text" id="special_requirements_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][special_requirements]" value="" />
                        </div>

                        <div class="input-wrap-outer clearfix one-three">
                            <div class="input-wrap clearfix">
                                <label for="emergency_name_<?php echo $enrolment_increment_id; ?>">Emergency Contact Name <span class="required">*</span></label>
                                <input class="enrolment-emergency_name" type="text" id="emergency_name_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][emergency_name]" value="" />
                            </div>
                            <div class="input-wrap clearfix">
                                <label for="emergency_relationship_<?php echo $enrolment_increment_id; ?>">Emergency Contact Relationship <span class="required">*</span></label>
                                <input class="enrolment-emergency_relationship" type="text" id="emergency_relationship_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][emergency_relationship]" value="" />
                            </div>
                            <div class="input-wrap clearfix">
                                <label for="emergency_phone_<?php echo $enrolment_increment_id; ?>">Emergency Contact Phone <span class="required">*</span></label>
                                <input class="enrolment-emergency_phone" type="text" id="emergency_phone_<?php echo $enrolment_increment_id; ?>" name="enrolments[<?php echo $enrolment_increment_id; ?>][emergency_phone]" value="" />
                            </div>

                        </div>

                    </div>
    <?php
                    $output .= ob_get_contents();
                    ob_end_clean();

                    $enrolment_increment_id++;
                }
            }
        }

        $output .= '</div>';

        echo $output;

    endif;
}
add_action('woocommerce_checkout_after_customer_details', 'freoarts_checkout_course_enrolment', 10, 1);

/**
 * Save enrolments to session
 *
 * @param $enrolments
 * @param $order_id
 * @return array
 */
function freoarts_save_enrolments_to_session($trigger, $enrolments, $order_id)
{

    // WC()->session->__unset('enrolments');

    $user_id = get_current_user_id() ? get_current_user_id() : null;

    $stored_enrolments = array();

    theme_log(
        'checkout',
        'Trigger: ' . $trigger,
        'Function: freoarts_save_enrolments_to_session',
        'User: ' . $user_id,
        'Order: ' . $order_id,
        'Enrolments:',
        $enrolments
    );

    foreach ($enrolments as $enrolment) {

        // Check if enrollee is a child?
        $child = false;
        $age = null;
        if (isset($enrolment['age'])) {
            $child = true;
            $age = $enrolment['age'];
        }

        $enrolee = array(
            'course_id'                 => $enrolment['course_id'],
            'user_id'                   => $user_id,
            'order_id'                  => $order_id,
            'title'                     => ifne($enrolment, 'title'),
            'preferred_pronoun'         => ifne($enrolment, 'preferred_pronoun'),
            'first_name'                => $enrolment['first_name'],
            'last_name'                 => $enrolment['last_name'],
            'phone'                     => $enrolment['phone'],
            'email'                     => $enrolment['email'],
            'special_requirements'      => $enrolment['special_requirements'],
            'emergency_name'            => $enrolment['emergency_name'],
            'emergency_relationship'    => $enrolment['emergency_relationship'],
            'emergency_phone'           => $enrolment['emergency_phone'],
            'child'                     => $child,
            'age'                       => $age,
            'method_of_payment'         => 'Online',
            'notes'                     => '',
            'created_at'                => date('Y-m-d H:i:s'),
            'updated_at'                => date('Y-m-d H:i:s')
        );

        $stored_enrolments[] = $enrolee;

        if (!empty($user_id)) {

            update_user_meta($user_id, 'special_requirements', $enrolment['special_requirements']);
            update_user_meta($user_id, 'emergency_name', $enrolment['emergency_name']);
            update_user_meta($user_id, 'emergency_relationship', $enrolment['emergency_relationship']);
            update_user_meta($user_id, 'emergency_phone', $enrolment['emergency_phone']);
        }
    }

    update_post_meta($order_id, 'stored_enrolments', $stored_enrolments);
    // WC()->session->set('enrolments', $stored_enrolments);

    return $stored_enrolments;
}

function freoarts_checkout_enrol_users_payment($result, $order_id)
{

    global $wpdb;

    $enrolments = '';
    if (isset($_POST['enrolments']) && !empty($_POST['enrolments'])) {
        $enrolments = $_POST['enrolments'];
    }

    $order = wc_get_order($order_id);

    theme_log(
        'checkout',
        'Trigger: Hook - woocommerce_payment_successful_result',
        'Function: freoarts_checkout_enrol_users_payment',
        'Result:',
        $result,
        'Order Status: ' . $order->get_status(),
        'Enrolments:',
        $enrolments ? $enrolments : NULL
    );

    if (!empty($enrolments)) {
        $stored_enrolments = freoarts_save_enrolments_to_session('freoarts_checkout_enrol_users_payment', $enrolments, $order_id);
    }

    return $result;
}
add_filter('woocommerce_payment_successful_result', 'freoarts_checkout_enrol_users_payment', 10, 3);


function freoarts_checkout_enrol_users_no_payment_required($url, $order)
{

    global $wpdb;

    $enrolments = '';
    if (isset($_POST['enrolments']) && !empty($_POST['enrolments'])) {
        $enrolments = $_POST['enrolments'];
    }

    theme_log(
        'checkout',
        'Trigger: Hook - woocommerce_checkout_no_payment_needed_redirect',
        'Function: freoarts_checkout_enrol_users_no_payment_required',
        'Order: ' . $order->get_id(),
        $order,
        'Order Status: ' . $order->get_status(),
        'Enrolments:',
        $enrolments ? $enrolments : NULL
    );

    if (!empty($enrolments)) {
        $stored_enrolments = freoarts_save_enrolments_to_session('freoarts_checkout_enrol_users_no_payment_required', $enrolments, $order->get_id());
    }

    freoarts_checkout_process_enrolments($order->get_id());

    return $url;
}
add_filter('woocommerce_checkout_no_payment_needed_redirect', 'freoarts_checkout_enrol_users_no_payment_required', 10, 3);

/**
 * Save enrolments to the database
 *
 * @param $result
 * @param $order_id
 * @return mixed
 */
function freoarts_checkout_process_enrolments($order_id)
{

    global $wpdb;

    $table_name = $wpdb->prefix . 'enrolments';

    $enrolments = get_post_meta($order_id, 'stored_enrolments', true);
    // $enrolments = WC()->session->get('enrolments', null);

    $order = wc_get_order($order_id);

    theme_log(
        'checkout',
        'Trigger: Hook - woocommerce_payment_complete',
        'Function: freoarts_checkout_process_enrolments',
        'Order: ' . $order_id,
        'Order Status: ' . $order->get_status(),
        $enrolments
    );

    if (!empty($enrolments)) {

        foreach ($enrolments as $enrolment) {

            $wpdb->insert(
                $table_name,
                $enrolment
            );

            freoarts_send_enrolment_confirmation_email($enrolment);
        }
    }

    // WC()->session->__unset('enrolments');
}
add_filter('woocommerce_payment_complete', 'freoarts_checkout_process_enrolments', 10, 1);

/**
 * Send transactional email via SendGrid
 *
 */
function freoarts_send_enrolment_confirmation_email($enrolment)
{

    $enrolment['course_title']  = get_the_title($enrolment['course_id']);
    $enrolment['course_url']    = get_the_permalink($enrolment['course_id']);
    $enrolment['start_date']    = get_field('start_date', $enrolment['course_id']);
    $enrolment['end_date']      = get_field('end_date', $enrolment['course_id']);
    $enrolment['duration']      = get_field('duration', $enrolment['course_id']);
    $enrolment['tc_url']        = get_bloginfo('url') . '/terms/';

    ob_start();
    include(TEMPLATEPATH . '/parts/email/enrolment-confirmation.php');
    $html_email = ob_get_contents();
    ob_end_clean();

    $from_email = get_field('email_address', 'option');

    // TODO: Update the from address

    $subject = 'Fremantle Arts Centre Enrolment Confirmation';
    $from_name = 'Fremantle Arts Centre';
    $to = $enrolment['email'];
    $emailClient = new HumaanEmail();
    $emailClient->sendEmail($html_email, $subject, $from_name, $from_email, $to);

    theme_log(
        'checkout',
        'Trigger: freoarts_checkout_process_enrolments',
        'Function: freoarts_send_enrolment_confirmation_email',
        $enrolment
    );
}

/**
 * Send transactional email via SendGrid
 *
 */
function freoarts_send_enrolment_cancellation_email($enrolment, $refund = 'full', $refund_amount = 0)
{

    $enrolment['course_title']  = get_the_title($enrolment['course_id']);
    $enrolment['course_url']    = get_the_permalink($enrolment['course_id']);

    ob_start();
    include(TEMPLATEPATH . '/parts/email/enrolment-cancellation.php');
    $html_email = ob_get_contents();
    ob_end_clean();


    $from_email = get_field('email_address', 'option');

    $subject = 'Fremantle Arts Centre Enrolment Cancellation';
    $from_name = 'Fremantle Arts Centre';
    $to = $enrolment['email'];
    $emailClient = new HumaanEmail();
    $emailClient->sendEmail($html_email, $subject, $from_name, $from_email, $to);

    /**
     * Send cancellation SMS
     *
     */
    if ($refund == 'full') {

        $sms_text = "Your course " . $enrolment['course_title'] . " has been cancelled by Fremantle Arts Centre and a full refund will be given.\n\nWhy has my course been cancelled?\nCourses are cancelled due to low enrolment numbers, tutor illness and/or at the discretion of Fremantle Arts Centre.\n\nWhat do I need to do to get my refund?\nIf you paid by credit card we will refund you automatically and may take up to 7 days.\nIf you paid by any other method we will contact you to arrange a refund.";

        send_SMS($enrolment['phone'], $sms_text);
    }
}

/**
 * Add meta box for course enrolee list on the Course product admin page
 *
 */
function freoarts_add_meta_box_course()
{

    add_meta_box(
        'enrolments_meta',
        'Enrolments',
        'freoarts_meta_box_course_enrolments',
        'product',
        'normal'
    );
}
add_action('add_meta_boxes', 'freoarts_add_meta_box_course');

/**
 * The course enrolee list content for the meta box on the Course product admin page
 *
 */
function freoarts_meta_box_course_enrolments()
{
    global $post;
    global $EnrollmentsFactory;

    $results = $EnrollmentsFactory->fetchEntries(
        array(
            'course_id = ' . $post->ID,
            'is_archived = 0',
            'trashed != 1'
        )
    );

    ob_start();
    ?>
    <div class="acf-field acf-field-repeater">
        <div class="acf-label">
            <label>Enrolments</label>
            <p class="description">
                <?php if (count($results)) { ?>
                    <?php $url = admin_url('admin.php?page=course-management-course-enrolments&course_id=' . $post->ID); ?>
                    The following are all the enrolments for this course. For further enrolee information <a href="<?php echo $url; ?>" title="Course enrolees">click here</a>.
                <?php } else { ?>
                    There are currently no enrolments for this course.
                <?php } ?>
            </p>

            <?php if (count($results)) { ?>
                <div style="margin: 15px 0; padding: 10px; background: #f9f9f9; border-left: 4px solid #0073aa;">
                    <button type="button" id="send-manual-reminder" class="button button-primary" style="margin-right: 10px;">
                        Send Reminder Emails Now
                    </button>
                    <span id="reminder-status" style="font-weight: bold;"></span>
                    <p style="margin: 8px 0 0 0; font-size: 13px; color: #666;">
                        This will send reminder emails and SMS to all <?php echo count($results); ?> enrolled student(s) for this course.
                    </p>
                </div>
            <?php } ?>
        </div>

        <?php if (count($results)) { ?>
            <div class="acf-input">
                <table class="acf-table">
                    <thead>
                        <tr>
                            <th class="acf-row-handle"></th>
                            <th class="acf-th acf-th-text">Enrollee</th>
                            <th class="acf-th acf-th-text">Email</th>
                            <th class="acf-th acf-th-text">Phone</th>
                            <th class="acf-th acf-th-text">Order</th>
                            <th class="acf-th acf-th-text">Reminder Sent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($results as $enrolment) { ?>
                            <tr class="acf-row">
                                <td class="acf-row-handle order">
                                    <span><?php echo $i; ?></span>
                                </td>
                                <td class="acf-field acf-field-text">
                                    <a href="?page=enrolments_view&amp;entry=<?php echo $enrolment->id; ?>" title="View enrolment">
                                        <?php echo $enrolment->last_name . ', ' . $enrolment->first_name . ' (' . $enrolment->title . ')'; ?>
                                    </a>
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php echo $enrolment->email; ?>
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php echo $enrolment->phone; ?>
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php if (!empty($enrolment->order_id)) { ?>
                                        <a href="<?php echo get_edit_post_link($enrolment->order_id); ?>">
                                            #<?php echo $enrolment->order_id; ?>
                                        </a>
                                    <?php } else { ?>
                                        No Associated Order
                                    <?php } ?>
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php if (!empty($enrolment->reminder_sent) && $enrolment->reminder_sent == 1) { ?>
                                        <span style="color: green;">✓ Sent</span>
                                        <?php if (!empty($enrolment->reminder_sent_at)) { ?>
                                            <br><small><?php echo date('M j, Y g:i A', strtotime($enrolment->reminder_sent_at)); ?></small>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <span style="color: #999;">Not sent</span>
                                    <?php } ?>
                                </td>
                            </tr>
                            <?php $i++; ?>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#send-manual-reminder').on('click', function(e) {
                e.preventDefault();

                var button = $(this);
                var status = $('#reminder-status');

                // Confirm action
                if (!confirm('Are you sure you want to send reminder emails to all enrolled students? This will also mark their reminders as sent.')) {
                    return;
                }

                // Disable button and show loading
                button.prop('disabled', true).text('Sending...');
                status.text('Processing...').css('color', '#0073aa');

                // Send AJAX request
                $.post(ajaxurl, {
                    action: 'manual_course_reminder',
                    course_id: <?php echo $post->ID; ?>,
                    nonce: '<?php echo wp_create_nonce('manual_course_reminder'); ?>'
                }, function(response) {
                    if (response.success) {
                        status.text(response.data.message).css('color', 'green');
                        button.text('✓ Emails Sent');

                        // Reload page after 2 seconds to show updated reminder status
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        status.text('Error: ' + response.data).css('color', 'red');
                        button.prop('disabled', false).text('Send Reminder Emails Now');
                    }
                }).fail(function() {
                    status.text('Request failed. Please try again.').css('color', 'red');
                    button.prop('disabled', false).text('Send Reminder Emails Now');
                });
            });
        });
    </script>

<?php
    $output = ob_get_contents();
    ob_clean();

    echo $output;
}

/**
 * Bug fix for ACF
 *
 * To reproduce:
 * A custom field group that contains a set of post_taxonomy checkbox selector fields.
 * The custom field group has a show/hide filter based on a post_taxonomy.
 *
 * Bug:
 * When clicking on a checkbox it posts args that contain 'post_taxonomy'.
 * This arg overrides the 'post_taxonomy' show/hide filter check and always returns false.
 * As such, the custom field group disappears once the AJAX post call is complete.
 *
 * Fix:
 * Using the filter hook, we add our own custom group filter validation.
 * If validated for this post type, we then override the 'post_taxonomy' arg (since it has already been saved
 * by this point) and set it to null.
 * This will then proceed with the default ACF group validation as normal.
 *
 * @param $args
 * @param $field_group
 * @return mixed
 */
function freoarts_acf_rule_match_post_taxonomy($args, $field_group)
{

    $terms = array();

    if (
        isset($args['action']) && $args['action'] == 'acf/post/get_field_groups' &&
        isset($args['ajax']) && $args['ajax'] == true &&
        isset($args['post_id']) && !empty($args['post_id'])
    ) {

        $terms = wp_get_post_terms($args['post_id'], 'product_type');

        foreach ($terms as $term) {

            if ($term->slug == 'course' || $term->slug == 'membership') {

                $args['post_taxonomy'] = null;

                return $args;
            }
        }
    }

    return $args;
}
add_filter('acf/location/screen', 'freoarts_acf_rule_match_post_taxonomy', 10, 3);

/**
 * Override Facebook image
 *
 * @param $url
 * @return mixed
 */
function opengraph_image_course_filter($url)
{

    if (is_singular('product')) {

        if (!empty(ifne($GLOBALS['page_fields'], 'info_image'))) {

            $url = get_resized_image(ifne($GLOBALS['page_fields'], 'info_image'), 'course');

            list($width, $height, $type, $attr) = @getimagesize($url);

            if ($width >= 200 && $height >= 200) {

                return $url;
            }
        }

        return $url;
    }

    return $url;
}
add_filter('wpseo_opengraph_image', 'opengraph_image_course_filter');

function freoarts_manual_course_reminder_ajax()
{
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['nonce'], 'manual_course_reminder')) {
        wp_die('Security check failed');
    }

    // Check user permissions
    if (!current_user_can('edit_posts')) {
        wp_die('Insufficient permissions');
    }

    $course_id = intval($_POST['course_id']);

    if (!$course_id) {
        wp_send_json_error('Invalid course ID');
    }

    // Run the reminder function for this specific course
    $result = freoarts_send_manual_course_reminder($course_id);

    if ($result['success']) {
        wp_send_json_success($result);
    } else {
        wp_send_json_error($result['message']);
    }
}
add_action('wp_ajax_manual_course_reminder', 'freoarts_manual_course_reminder_ajax');

/**
 * Send manual course reminder for specific course
 * Based on your existing freoarts_check_and_update_course_reminder function
 */
function freoarts_send_manual_course_reminder($course_id)
{
    global $wpdb;

    // Safety checks
    if (!$course_id || !get_post($course_id)) {
        return array('success' => false, 'message' => 'Course not found');
    }

    // Use SAME query as display (most important fix)
    $sql = $wpdb->prepare("
        SELECT e.*
        FROM {$wpdb->prefix}enrolments AS e
        WHERE e.course_id = %d
        AND e.trashed != 1
        AND e.is_archived = 0
        AND e.archived_course_id IS NULL
    ", $course_id);

    $results = $wpdb->get_results($sql, ARRAY_A);

    if (empty($results)) {
        return array('success' => false, 'message' => 'No enrollments found');
    }

    error_log("Manual reminder: Course $course_id, sending to " . count($results) . " students");

    $sent_count = 0;
    $dates = freoarts_get_course_dates($course_id);

    foreach ($results as $enrolment) {
        $enrolment['course_title'] = get_the_title($enrolment['course_id']);
        $enrolment['course_url'] = get_the_permalink($enrolment['course_id']);

        // Use the reliable date functions
        $enrolment['start_date'] = $dates['start_date'];
        $enrolment['end_date'] = $dates['end_date'];
        $enrolment['duration'] = get_field('duration', $enrolment['course_id']);

        $from_email = get_field('email_address', 'option');

        // Generate email content
        ob_start();
        include(TEMPLATEPATH . '/parts/email/enrolment-reminder.php');
        $html_email = ob_get_contents();
        ob_end_clean();

        $to_email = $enrolment['email'];
        $subject = 'Fremantle Arts Centre Course Reminder';
        $from_name = 'Fremantle Arts Centre';

        // Send email
        $email = new HumaanEmail();
        $email->sendEmail($html_email, $subject, $from_name, $from_email, $to_email);

        // Send SMS with proper date formatting
        if (!empty($dates['start_date'])) {
            $formatted_start_date = freoarts_format_course_date($dates['start_date']);
            $sms_text = 'Fremantle Arts Centre - Reminder. Your course ' . $enrolment['course_title'] . ' is due to start ' . $formatted_start_date;
        } else {
            $sms_text = 'Fremantle Arts Centre - Reminder. Your course ' . $enrolment['course_title'] . ' - please check course details for start date.';
        }

        if (!empty($enrolment['phone'])) {
            send_SMS($enrolment['phone'], $sms_text);
        }

        $current_datetime = freoarts_get_local_datetime();

        $wpdb->update(
            $wpdb->prefix . 'enrolments',
            array(
                'reminder_sent' => 1,
                'reminder_sent_at' => $current_datetime
            ),
            array(
                'id' => $enrolment['id']
            )
        );

        $sent_count++;
    }

    return array(
        'success' => true,
        'message' => "Reminder emails sent to {$sent_count} student(s)",
        'count' => $sent_count
    );
}

function freoarts_get_local_datetime()
{
    $timezone = wp_timezone();
    $datetime = new DateTime('now', $timezone);
    return $datetime->format('Y-m-d H:i:s');
}
