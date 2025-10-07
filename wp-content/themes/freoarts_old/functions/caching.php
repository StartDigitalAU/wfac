<?php

/**
 * Set the cache headers of the Product/Course pages to ideally not cache anything
 *
 */
function set_product_cache_headers()
{
    if (is_singular('product')) {
        if (is_user_logged_in()) {
            header('Cache-Control: max-age=0, no-store');
        } else {
            header('Cache-Control: max-age=86400, no-cache');
        }
    }
}
add_action('template_redirect', 'set_product_cache_headers');
