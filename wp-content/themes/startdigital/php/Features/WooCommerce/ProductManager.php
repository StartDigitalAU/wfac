<?php

namespace TheStart\Features\WooCommerce;

use TheStart\Features\WooCommerce\ProductTypes\CourseProductType;
use TheStart\Features\WooCommerce\ProductTypes\MembershipProductType;
use TheStart\Features\WooCommerce\ProductTypes\PrintAwardProductType;

/**
 * Main manager class for WooCommerce custom products
 */
class ProductManager
{
    private array $product_types = [];

    public function __construct()
    {
        $this->init();
    }

    private function init(): void
    {
        add_filter('woocommerce_placeholder_img_src', [$this, 'update_placeholder_image'], 10);

        $this->product_types[] = new CourseProductType();
        $this->product_types[] = new MembershipProductType();
        $this->product_types[] = new PrintAwardProductType();
    }

    public function update_placeholder_image(string $image_url): string
    {
        return get_bloginfo('template_url') . '/img/ui/placeholder.jpg';
    }

    public function get_product_types(): array
    {
        return $this->product_types;
    }
}
