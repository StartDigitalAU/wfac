<?php

namespace TheStart\Features\WooCommerce\ProductTypes;

use TheStart\Features\WooCommerce\Abstract\ProductTypeBase;

/**
 * Print Award Product Type Handler
 */
class PrintAwardProductType extends ProductTypeBase
{
    public function __construct()
    {
        $this->product_type = 'print_award';
        $this->display_name = 'Print Award';
        $this->hide_tabs[] = 'shipping'; // Additional tab to hide
        parent::__construct();
    }

    /**
     * Register the WC_Product_Print_Award class
     */
    public function register_product_type()
    {
        // Load the product class file
        require_once get_template_directory() . '/php/Features/WooCommerce/Products/WC_Product_Print_Award.php';
    }

    protected function register_hooks()
    {
        parent::register_hooks();
        add_filter('woocommerce_thankyou_order_received_text', [$this, 'customize_thank_you_message'], 10, 2);
    }

    /**
     * Customize the thank you message for Print Award orders
     */
    public function customize_thank_you_message($string, $order)
    {
        if (!$order || !is_a($order, 'WC_Order')) {
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
}
