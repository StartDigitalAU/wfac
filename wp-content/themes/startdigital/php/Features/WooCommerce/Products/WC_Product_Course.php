<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Course Product Class
 * 
 * @extends WC_Product_Simple
 */
class WC_Product_Course extends WC_Product_Simple
{

    public $product_type;

    /**
     * Initialize course product.
     *
     * @param mixed $product
     */
    public function __construct($product = 0)
    {
        $this->product_type = 'course';
        parent::__construct($product);
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
