<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Membership Product Class
 * 
 * @extends WC_Product_Simple
 */
class WC_Product_Membership extends WC_Product_Simple
{

    public $product_type;

    /**
     * Initialize membership product.
     *
     * @param mixed $product
     */
    public function __construct($product = 0)
    {
        $this->product_type = 'membership';
        parent::__construct($product);
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
