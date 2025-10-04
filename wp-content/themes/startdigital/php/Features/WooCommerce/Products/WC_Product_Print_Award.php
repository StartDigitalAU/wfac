<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Print Award Product Class
 * 
 * @extends WC_Product_Simple
 */
class WC_Product_Print_Award extends WC_Product_Simple
{

    public $product_type;

    /**
     * Initialize print award product.
     *
     * @param mixed $product
     */
    public function __construct($product = 0)
    {
        $this->product_type = 'print_award';
        parent::__construct($product);
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
