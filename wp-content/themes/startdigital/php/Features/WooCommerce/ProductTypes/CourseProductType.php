<?php

namespace TheStart\Features\WooCommerce\ProductTypes;

use TheStart\Features\WooCommerce\Abstract\ProductTypeBase;

/**
 * Course Product Type Handler
 */
class CourseProductType extends ProductTypeBase
{
    public function __construct()
    {
        $this->product_type = 'course';
        $this->display_name = 'Course';
        parent::__construct();
    }

    /**
     * Register the WC_Product_Course class
     */
    public function register_product_type()
    {
        require_once get_template_directory() . '/php/Features/WooCommerce/Products/WC_Product_Course.php';
    }
}
