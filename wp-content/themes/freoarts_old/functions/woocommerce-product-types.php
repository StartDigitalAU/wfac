<?php

/****************************************************
 *
 * WOOCOMMERCE - PRODUCT MODIFICATIONS
 *
 ****************************************************/

/**
 * Replace default product image
 *
 * @param $image_url
 * @return mixed
 */
function woocommerce_update_placeholder_image($image_url)
{

    $image_url = get_bloginfo('template_url') . '/img/ui/placeholder-50x50.png';

    return $image_url;
}
add_filter('woocommerce_placeholder_img_src', 'woocommerce_update_placeholder_image', 10);

/****************************************************
 *
 * WOOCOMMERCE - CUSTOM PRODUCT TYPE - COURSE
 *
 ****************************************************/

/**
 * Register Course product type
 *
 */
function register_course_product_type()
{
    class WC_Product_Course extends WC_Product_Simple
    {

        public function __construct($product)
        {

            parent::__construct($product);

            $this->product_type = 'course';
        }

        /**
         * Get internal type.
         *
         * @return string
         */
        public function get_type()
        {
            return 'course';
        }
    }
}
add_action('init', 'register_course_product_type');

/**
 * Add Course product type to product type selection
 *
 */
function add_course_product($types)
{
    $types['course'] = __('Course');

    return $types;
}
add_filter('product_type_selector', 'add_course_product');

/**
 * Add product type 'course' filters to the Product Data meta box tabs
 *
 * @param $product_data_tabs
 * @return mixed
 */
function course_custom_product_tabs($product_data_tabs)
{
    $product_data_tabs['inventory']['class'][]      = 'show_if_course';
    $product_data_tabs['linked_product']['class'][] = 'hide_if_course';
    $product_data_tabs['attribute']['class'][]      = 'hide_if_course';
    $product_data_tabs['advanced']['class'][]       = 'hide_if_course';

    return $product_data_tabs;
}
add_filter('woocommerce_product_data_tabs', 'course_custom_product_tabs');

/**
 *  Print product type 'course' Javascript to add filters and show/hide panels in Product Data meta box
 *
 */
function course_custom_js()
{
    if ('product' != get_post_type()) {
        return;
    }

?>
    <script type='text/javascript'>
        jQuery(document).ready(function($) {

            $('#product-type').on('change', function(e) {
                if ($(this).val() == 'course') {
                    $('#_virtual').attr('checked', true);
                }
            });

            // Show/hide price fields and show General tab
            $('.options_group.pricing').addClass('show_if_course').show();
            $('.product_data_tabs').find('li a[href="#general_product_data"]').trigger('click').parent().show();

            // Show/hide inventory fields
            $('#inventory_product_data ._manage_stock_field').addClass('show_if_course').show();
            $('#inventory_product_data ._backorders_field').addClass('hide_if_course').hide();

        });
    </script>
<?php

}
add_action('admin_footer', 'course_custom_js');

/****************************************************
 *
 * WOOCOMMERCE - CUSTOM PRODUCT TYPE - MEMBERSHIP
 *
 ****************************************************/


/**
 * Register Membership product type
 *
 */
function register_membership_product_type()
{
    class WC_Product_Membership extends WC_Product_Simple
    {

        public function __construct($product)
        {

            parent::__construct($product);

            $this->product_type = 'membership';
        }

        /**
         * Get internal type.
         *
         * @return string
         */
        public function get_type()
        {
            return 'membership';
        }
    }
}
add_action('init', 'register_membership_product_type');

/**
 * Add Membership product type to product type selection
 *
 */
function add_membership_product($types)
{
    $types['membership'] = __('Membership');

    return $types;
}
add_filter('product_type_selector', 'add_membership_product');

/**
 * Add product type 'membership' filters to the Product Data meta box tabs
 *
 * @param $product_data_tabs
 * @return mixed
 */
function membership_custom_product_tabs($product_data_tabs)
{
    $product_data_tabs['inventory']['class'][]      = 'show_if_membership';
    $product_data_tabs['linked_product']['class'][] = 'hide_if_membership';
    $product_data_tabs['attribute']['class'][]      = 'hide_if_membership';
    $product_data_tabs['advanced']['class'][]       = 'hide_if_membership';

    return $product_data_tabs;
}
add_filter('woocommerce_product_data_tabs', 'membership_custom_product_tabs');

/**
 *  Print product type 'membership' Javascript to add filters and show/hide panels in Product Data meta box
 *
 */
function membership_custom_js()
{
    if ('product' != get_post_type()) {
        return;
    }

?>
    <script type='text/javascript'>
        jQuery(document).ready(function($) {

            $('#product-type').on('change', function(e) {
                if ($(this).val() == 'membership') {
                    $('#_virtual').attr('checked', true);
                }
            });

            // Show/hide price fields and show General tab
            $('.options_group.pricing').addClass('show_if_membership').show();
            $('.product_data_tabs').find('li a[href="#general_product_data"]').trigger('click').parent().show();

            // Show/hide inventory fields
            $('#inventory_product_data ._manage_stock_field').addClass('show_if_membership').show();
            $('#inventory_product_data ._backorders_field').addClass('hide_if_membership').hide();

        });
    </script>
<?php

}
add_action('admin_footer', 'membership_custom_js');

/****************************************************
 *
 * WOOCOMMERCE - CUSTOM PRODUCT TYPE - PRINT AWARD
 *
 ****************************************************/


/**
 * Register Membership product type
 *
 */
function register_print_award_product_type()
{
    class WC_Product_Print_Award extends WC_Product_Simple
    {

        public function __construct($product)
        {

            parent::__construct($product);

            $this->product_type = 'print_award';
        }

        /**
         * Get internal type.
         *
         * @return string
         */
        public function get_type()
        {
            return 'print_award';
        }
    }
}
add_action('init', 'register_print_award_product_type');

/**
 * Add Membership product type to product type selection
 *
 */
function add_print_award_product($types)
{
    $types['print_award'] = __('Print Award');

    return $types;
}
add_filter('product_type_selector', 'add_print_award_product');

/**
 * Add product type 'membership' filters to the Product Data meta box tabs
 *
 * @param $product_data_tabs
 * @return mixed
 */
function print_award_custom_product_tabs($product_data_tabs)
{
    $product_data_tabs['inventory']['class'][]      = 'show_if_print_award';
    $product_data_tabs['linked_product']['class'][] = 'hide_if_print_award';
    $product_data_tabs['attribute']['class'][]      = 'hide_if_print_award';
    $product_data_tabs['advanced']['class'][]       = 'hide_if_print_award';
    $product_data_tabs['shipping']['class'][]       = 'hide_if_print_award';

    return $product_data_tabs;
}
add_filter('woocommerce_product_data_tabs', 'print_award_custom_product_tabs');

/**
 *  Print product type 'membership' Javascript to add filters and show/hide panels in Product Data meta box
 *
 */
function print_award_custom_js()
{
    if ('product' != get_post_type()) {
        return;
    }

?>
    <script type='text/javascript'>
        jQuery(document).ready(function($) {

            $('#product-type').on('change', function(e) {
                if ($(this).val() == 'print_award') {
                    $('#_virtual').attr('checked', true);
                }
            });

            // Show/hide price fields and show General tab
            $('.options_group.pricing').addClass('show_if_print_award').show();
            $('.product_data_tabs').find('li a[href="#general_product_data"]').trigger('click').parent().show();

            // Show/hide inventory fields
            $('#inventory_product_data ._manage_stock_field').addClass('show_if_print_award').show();
            $('#inventory_product_data ._backorders_field').addClass('hide_if_print_award').hide();

        });
    </script>
<?php

}
add_action('admin_footer', 'print_award_custom_js');


/**
 * Show a successful payment notification for anyone that purchases a Print Award submission
 *
 * @param $string
 * @param $order
 * @return string
 */
function print_award_payment_success_notification($string, $order)
{

    if (! $order || ! is_a($order, 'WC_Order')) {
        return $string;
    }

    foreach ($order->get_items() as $order_item) {

        $_product = wc_get_product($order_item['product_id']);

        if ($_product && $_product->get_type() === 'print_award') {
            return 'Thank You. Your entry has been received.';
        }
    }

    return $string;
}
add_filter('woocommerce_thankyou_order_received_text', 'print_award_payment_success_notification', 10, 3);
