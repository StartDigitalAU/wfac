<?php

namespace TheStart\Features\WooCommerce\ProductTypes;

use TheStart\Features\WooCommerce\Abstract\ProductTypeBase;

/**
 * Membership Product Type Handler
 */
class MembershipProductType extends ProductTypeBase
{
    public function __construct()
    {
        $this->product_type = 'membership';
        $this->display_name = 'Membership';
        parent::__construct();
    }

    /**
     * Register the WC_Product_Membership class
     */
    public function register_product_type()
    {
        // Load the product class file
        require_once get_template_directory() . '/php/Features/WooCommerce/Products/WC_Product_Membership.php';
    }
}
