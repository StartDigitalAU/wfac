<?php

/****************************************************
 *
 * WOOCOMMERCE - GENERAL
 *
 ****************************************************/

/**
 * Declare Woocommerce Support
 *
 */
function add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

add_action( 'after_setup_theme', 'add_woocommerce_support' );

/**
 * Rename the WooCommerce admin menu item
 *
 */
function rename_woocommerce_admin_menu_item() {

    global $menu;

    // Pinpoint menu item
    $woo = recursive_array_search('WooCommerce', $menu);

    // Validate
    if (!$woo) {

        return;
    }

    $menu[$woo][0] = 'Orders';
}
add_action('admin_menu', 'rename_woocommerce_admin_menu_item', 999);

/**
 * Remove the password restrictions
 *
 */
function remove_wc_password_meter() {

    wp_dequeue_script('wc-password-strength-meter');
}
add_action('wp_print_scripts', 'remove_wc_password_meter', 100);

/**
 * Disable the ability to delete Orders.
 * Deleting orders will orphan enrolments
 *
 */
/*
function restrict_post_deletion($post_ID) {

    $type = get_post_type($post_ID);

    if ($type == 'shop_order'){
        echo "You are not authorized to delete this page.";
        exit;
    }
}
add_action('wp_trash_post', 'restrict_post_deletion', 10, 1);
add_action('before_delete_post', 'restrict_post_deletion', 10, 1);
*/

function remove_row_actions($actions)
{

    if (get_post_type() === 'shop_order') {
        unset( $actions['trash'] );
    }
    return $actions;
}
add_filter( 'post_row_actions', 'remove_row_actions', 10, 1 );

/**
 * Update the WooCommerce actions and filters
 *
 */
function update_woocommerce_actions()
{

    // Move the order review section within the checkout page
    remove_action( 'woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20 );
    add_action( 'woocommerce_custom_checkout_payment', 'woocommerce_checkout_payment', 20 );
}
add_action('init', 'update_woocommerce_actions');

/**
 * Add custom checkout fields
 * (This method has been deprecated in favour of hard-coded field in form-billing.php)
 *
 * @param $fields
 * @return mixed
 */
function custom_checkout_fields($fields)
{

    $fields['billing']['billing_hear_about_us'] = array(
        'type' => 'text'
    );

    return $fields;
}
// add_filter('woocommerce_checkout_fields' , 'custom_checkout_fields');

/**
 * Display custom order fields in admin
 *
 * @param $order
 */
function custom_checkout_field_display_admin_order_meta($order)
{
    echo '<div class="address">';

    $value = get_post_meta($order->get_id(), '_billing_hear_about_us', true);
    echo '<p><strong>'.__('How did you hear about us?').':</strong> ' . $value . '</p>';

    echo '</div>';
}
add_action('woocommerce_admin_order_data_after_shipping_address', 'custom_checkout_field_display_admin_order_meta', 10, 1);

/**
 * Add custom fields to email
 *
 * @param $fields
 * @param $sent_to_admin
 * @param $order
 * @return mixed
 */
function custom_checkout_field_display_email_order_meta($fields, $sent_to_admin, $order)
{
    $value = get_post_meta($order->get_id(), '_billing_hear_about_us', true);

    $fields['billing_hear_about_us'] = array(
        'label' => __('How did you hear about us?'),
        'value' => $value,
    );

    return $fields;
}
add_filter('woocommerce_email_order_meta_fields', 'custom_checkout_field_display_email_order_meta', 10, 3);

/**
 * Manually add the custom checkout fields, which allows for better control with HTML
 *
 * @param $result
 * @param $order_id
 * @return mixed
 */
function custom_checkout_field_add_after_payment($result, $order_id)
{
    global $wpdb;

    if (isset($_REQUEST['billing_hear_about_us']) && !empty($_REQUEST['billing_hear_about_us'])) {

        add_post_meta($order_id, '_billing_hear_about_us', $_REQUEST['billing_hear_about_us']);
    }

    return $result;
}
add_filter('woocommerce_payment_successful_result', 'custom_checkout_field_add_after_payment', 10, 3);

/**
 * Subscribe the user to the selected list IDs
 *
 * @param $result
 * @param $order_id
 * @return mixed
 */
function add_subscriber_to_lists($result, $order_id)
{
    global $wpdb;

    if (isset($_REQUEST['subscription_options']) && !empty($_REQUEST['subscription_options'])) {

        $order = new WC_Order($order_id);
        $email = $order->get_billing_email();

        $cm_api_key = get_field('campaign_monitor_api_key', 'option');
        $cm_client_id = get_field('campaign_monitor_client_id', 'option');

        require_once get_template_directory() . '/library/vendor/campaignmonitor/createsend-php/csrest_subscribers.php';

        $auth = array(
            'api_key' => $cm_api_key
        );

        foreach ($_REQUEST['subscription_options'] as $list_id) {

            $wrap = new CS_REST_Subscribers($list_id, $auth);

            $response = $wrap->add([
                'EmailAddress' => $email,
                'Resubscribe'  => true
            ]);
        }
    }

    return $result;
}
add_filter('woocommerce_payment_successful_result', 'add_subscriber_to_lists', 10, 3);

/**
 * Add title to the eWAY credit card form
 *
 */
function add_title_to_payment_page_order_details() {

    echo '<h3 class="payment-order-details">Order Details</h3>';
}
add_action('before_woocommerce_pay', 'add_title_to_payment_page_order_details');

function add_title_to_payment_page_credit_card_form() {

    echo '<h3 class="payment-credit-card-form">Credit Card Details</h3>';
}
add_action('woocommerce_receipt_eway', 'add_title_to_payment_page_credit_card_form');

/**
 * Once a product has been added to the cart via the query string, redirect back to the cart to clear the query string
 *
 */
function add_to_cart_query_string_redirect()
{

    if (!empty($_GET['add-to-cart'])) {

        wp_redirect(home_url('/cart/'));
        exit();
    }
}
add_action('template_redirect', 'add_to_cart_query_string_redirect');

function woocommerce_product_columns($columns)
{

    unset($columns['product_cat']);
    unset($columns['product_tag']);
    unset($columns['product_type']);
    unset($columns['wpseo-score']);
    unset($columns['wpseo-score-readability']);
    unset($columns['wpseo-title']);
    unset($columns['wpseo-metadesc']);
    unset($columns['wpseo-focuskw']);

    $columns['start_date'] = __('Start Date');

    return $columns;
}
add_filter('manage_edit-product_columns', 'woocommerce_product_columns', 15);

function woocommerce_product_column_start_date($column, $post_id)
{

    if ($column == 'start_date') {

        $start_date = get_field('start_date', $post_id);

        if (!empty($start_date)) {

            echo date('Y/m/d', strtotime($start_date));
        }
        else {

            echo '';
        }
    }
}
add_action('manage_product_posts_custom_column', 'woocommerce_product_column_start_date', 10, 2);


function woocommerce_product_sortable_custom_columns($columns)
{

    $columns['start_date'] = 'start_date';

    return $columns;
}
add_filter('manage_edit-product_sortable_columns', 'woocommerce_product_sortable_custom_columns');

/**
 * Pre fetch posts filter for adding a sortable column for Woocommerce products
 *
 * @param $query
 */
function woocommerce_pre_get_posts_start_date($query)
{

    // only in admin
    if (!is_admin() || !$query->is_main_query()) {

        return;
    }

    if (isset($query->query_vars['post_type']) && $query->query_vars['post_type'] == 'product' && ($orderby = $query->get('orderby'))) {

        if ($orderby == 'start_date') {

            $query->set('meta_key', 'start_date');
        }
    }
}
add_action('pre_get_posts', 'woocommerce_pre_get_posts_start_date');

/**
 * Add eway invoice description to the online order transactions
 *
 */
function woocommerce_add_eway_invoice_description($string, $order)
{

    if (!empty($string)) {
        $string .= ' ';
    }
    $string = '(Online Payment)';

    return $string;
}
add_filter('woocommerce_eway_description', 'woocommerce_add_eway_invoice_description', 10, 3);

/**
 * Register the product status Archive
 *
 */
function woocommerce_archive_post_status()
{

    register_post_status('archive', array(
        'label'                     => _x('Archive', 'product'),
        'public'                    => false,
        'internal'                  => false,
        'private'                   => false,
        'exclude_from_search'       => true,
        'show_in_admin_all_list'    => false,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'Archive <span class="count">(%s)</span>', 'Archive <span class="count">(%s)</span>' ),
    ));
}
add_action('init', 'woocommerce_archive_post_status');

/**
 * Add Archive status to the product status select box
 *
 */
function woocommerce_append_post_status_list(){

    global $post;

    $complete = '';
    $label = '';

    if ($post->post_type == 'product') {

        if ($post->post_status == 'archive') {

            $complete = ' selected="selected"';
            $label = '<span id="post-status-display"> Archive</span>';
        }

        echo '<script>
                jQuery(document).ready(function($){
                    $("select#post_status").append(\'<option value="archive" '.$complete.'>Archive</option>\');
                    $(".misc-pub-section label").append(\'' . $label . '\');
                });
                </script>';
    }
}
add_action('admin_footer-post.php', 'woocommerce_append_post_status_list');

/**
 * Create an Archive state for posts (specifically used for Product post type)
 *
 * @param $states
 * @return array
 */
function woocommerce_display_archive_state($states)
{
    global $post;

    $arg = get_query_var('post_status');

    if ($arg != 'archive' && isset($post->post_status)) {

        if ($post->post_status == 'archive') {

            return array('Archive');
        }
    }

    return $states;
}
add_filter('display_post_states', 'woocommerce_display_archive_state');

/**
 * Add custom checkout page validation
 *
 */
function woocommerce_custom_checkout_validation()
{

    $enrolment_error = false;

    if (isset($_POST['enrolments']) && count($_POST['enrolments'])) {

        foreach ($_POST['enrolments'] as $enrolment) {

            foreach ($enrolment as $field => $value) {

                if (empty($value) && $field != "preferred_pronoun") {

                    $enrolment_error = true;
                }
            }
        }
    }

    if ($enrolment_error) {

        wc_add_notice('Please ensure all required fields are filled.', 'error');
    }
}
add_action('woocommerce_checkout_process', 'woocommerce_custom_checkout_validation');

/**
 * Add Subtotal header for line items in order view screen
 *
 * @param $order
 */
function view_order_show_line_item_subtotal_heading($order) {

    ?>
    <th class="item_cost sortable">SubTotal</th>
    <?php
}
add_action('woocommerce_admin_order_item_headers', 'view_order_show_line_item_subtotal_heading', 10, 1);

/**
 * Add Subtotal for line items in order view screen
 *
 * @param $order
 */
function view_order_show_line_item_subtotal($_product, $item, $item_id) {
    ?>
    <td class="item_cost" width="1%">
        <div class="view">
            <?php echo wc_price($item->get_total() + $item->get_total_tax()) ?>
        </div>
    </td>
    <?php
}
add_action('woocommerce_admin_order_item_values', 'view_order_show_line_item_subtotal', 10, 3);

/**
 * Update the shop_order post type arguments for registration
 *
 * @param $args
 * @return mixed
 */
function update_post_type_shop_order($args) {

    if (isset($args['labels']['singular_name'])) {

        $args['labels']['singular_name'] = _x('Tax Invoice Order', 'shop_order post type singular name', 'woocommerce');
    }

    return $args;
}
add_filter('woocommerce_register_post_type_shop_order', 'update_post_type_shop_order', 10, 1);

function wc_remove_menu_pages() {
    remove_menu_page('wc-admin&path=/marketing');
    remove_menu_page('wc-admin&path=/analytics/revenue');
}
add_action('admin_menu', 'wc_remove_menu_pages', 100);

function wc_disable_marketing_hub($marketing_pages) {
    return [];
}
add_filter('woocommerce_marketing_menu_items', 'wc_disable_marketing_hub');

function disable_features($features) {
    $marketing = array_search('marketing', $features);
    unset($features[$marketing]);
    return $features;
}
add_filter('woocommerce_admin_features', 'disable_features');

/**
 * Disable refunds for eWAY payment gateway
 *
 * @param $var
 * @param $feature
 * @param $instance
 *
 * @return false
 */
function filter_woocommerce_payment_gateway_supports($var, $feature, $instance) {
    if ($feature === 'refunds' && $instance instanceof WC_Gateway_EWAY) {
        return false;
    }
    return $var;
};
add_filter( 'woocommerce_payment_gateway_supports', 'filter_woocommerce_payment_gateway_supports', 10, 3 );

/**
 * Translate Coupon(s) to Dicount(s)
 */
if( !function_exists('filter_change_woocommerce_coupon_text')) {
    function filter_change_woocommerce_coupon_text( $translated ) {
        $text       = array(
            'Coupon(s)' => 'Discount(s)',
            'Coupon' => 'Discount',
            'coupon' => 'discount',
        );
        $translated = str_ireplace( array_keys( $text ), $text, $translated );
        return $translated;
    }
    add_filter( 'gettext', 'filter_change_woocommerce_coupon_text', 20 );
}