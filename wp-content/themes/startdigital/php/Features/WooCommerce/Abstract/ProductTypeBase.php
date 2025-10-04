<?php

namespace TheStart\Features\WooCommerce\Abstract;

/**
 * Abstract base class for custom WooCommerce product types
 */
abstract class ProductTypeBase
{
    protected $product_type;
    protected $display_name;
    protected $is_virtual = true;
    protected $show_tabs = ['inventory'];
    protected $hide_tabs = ['linked_product', 'attribute', 'advanced'];

    public function __construct()
    {
        $this->register_hooks();
    }

    /**
     * Register all WordPress hooks
     */
    protected function register_hooks()
    {
        add_action('init', [$this, 'register_product_type']);
        add_filter('product_type_selector', [$this, 'add_to_selector']);
        add_filter('woocommerce_product_data_tabs', [$this, 'customize_product_tabs']);
        add_action('admin_footer', [$this, 'output_admin_js']);
    }

    /**
     * Register the custom product type class
     */
    abstract public function register_product_type();

    /**
     * Add product type to the selector dropdown
     */
    public function add_to_selector($types)
    {
        $types[$this->product_type] = __($this->display_name);
        return $types;
    }

    /**
     * Customize which tabs are shown/hidden for this product type
     */
    public function customize_product_tabs($product_data_tabs)
    {
        foreach ($this->show_tabs as $tab) {
            if (isset($product_data_tabs[$tab])) {
                $product_data_tabs[$tab]['class'][] = 'show_if_' . $this->product_type;
            }
        }

        foreach ($this->hide_tabs as $tab) {
            if (isset($product_data_tabs[$tab])) {
                $product_data_tabs[$tab]['class'][] = 'hide_if_' . $this->product_type;
            }
        }

        return $product_data_tabs;
    }

    /**
     * Output admin JavaScript for this product type
     */
    public function output_admin_js()
    {
        if ('product' !== get_post_type()) {
            return;
        }

        $this->render_admin_js();
    }

    /**
     * Render the actual JavaScript
     */
    protected function render_admin_js()
    {
        $product_type = $this->product_type;
?>
        <script type='text/javascript'>
            jQuery(document).ready(function($) {
                $('#product-type').on('change', function(e) {
                    if ($(this).val() == '<?php echo esc_js($product_type); ?>') {
                        <?php if ($this->is_virtual): ?>
                            $('#_virtual').attr('checked', true);
                        <?php endif; ?>
                    }
                });

                // Show/hide price fields and show General tab
                $('.options_group.pricing').addClass('show_if_<?php echo esc_js($product_type); ?>').show();
                $('.product_data_tabs').find('li a[href="#general_product_data"]').trigger('click').parent().show();

                // Show/hide inventory fields
                $('#inventory_product_data ._manage_stock_field').addClass('show_if_<?php echo esc_js($product_type); ?>').show();
                $('#inventory_product_data ._backorders_field').addClass('hide_if_<?php echo esc_js($product_type); ?>').hide();
            });
        </script>
<?php
    }

    /**
     * Get the product type identifier
     */
    public function get_product_type()
    {
        return $this->product_type;
    }
}
