<?php

/****************************************************
 *
 * TAXONOMIES
 *
 ****************************************************/


function register_custom_taxonomies()
{

    $labels = array(
        'name' => _x('Event Category', 'taxonomy general name'),
        'singular_name' => _x('Event Category', 'taxonomy singular name'),
        'search_items' => __('Search Event Category'),
        'all_items' => __('All Event Categories'),
        'parent_item' => __('Parent Event Category'),
        'parent_item_colon' => __('Parent Event Category:'),
        'edit_item' => __('Edit Event Category'),
        'update_item' => __('Update Event Category'),
        'add_new_item' => __('Add New Event Category'),
        'new_item_name' => __('New Event Category'),
        'menu_name' => __('Event Categories')
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'whats-on/event_category')
    );
    register_taxonomy('event_category', array('whatson'), $args);


    $labels = array(
        'name' => _x('Event Tag', 'taxonomy general name'),
        'singular_name' => _x('Event Tag', 'taxonomy singular name'),
        'search_items' => __('Search Event Tags'),
        'all_items' => __('All Event Tags'),
        'parent_item' => __('Parent Event Tag'),
        'parent_item_colon' => __('Parent Event Tag:'),
        'edit_item' => __('Edit Event Tag'),
        'update_item' => __('Update Event Tag'),
        'add_new_item' => __('Add New Event Tag'),
        'new_item_name' => __('New Event Tag'),
        'menu_name' => __('Event Tags')
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'whats-on/event_tag')
    );
    register_taxonomy('event_tag', array('whatson'), $args);


    $labels = array(
        'name' => _x('Exhibition Tag', 'taxonomy general name'),
        'singular_name' => _x('Exhibition Tag', 'taxonomy singular name'),
        'search_items' => __('Search Exhibition Tags'),
        'all_items' => __('All Exhibition Tags'),
        'parent_item' => __('Parent Exhibition Tag'),
        'parent_item_colon' => __('Parent Exhibition Tag:'),
        'edit_item' => __('Edit Exhibition Tag'),
        'update_item' => __('Update Exhibition Tag'),
        'add_new_item' => __('Add New Exhibition Tag'),
        'new_item_name' => __('New Exhibition Tag'),
        'menu_name' => __('Exhibition Tags')
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'whats-on/exhibition_tag')
    );
    register_taxonomy('exhibition_tag', array('whatson'), $args);

    /**
     * Tags used to display related posts within single post type templates
     *
     */
    $labels = array(
        'name' => _x('Related Tag', 'taxonomy general name'),
        'singular_name' => _x('Related Tag', 'taxonomy singular name'),
        'search_items' => __('Search Related Tags'),
        'all_items' => __('All Related Tags'),
        'parent_item' => __('Parent Related Tag'),
        'parent_item_colon' => __('Parent Related Tag:'),
        'edit_item' => __('Edit Related Tag'),
        'update_item' => __('Update Related Tag'),
        'add_new_item' => __('Add New Related Tag'),
        'new_item_name' => __('New Related Tag'),
        'menu_name' => __('Related Tags')
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => false,
        'query_var' => true,
        'rewrite' => array('slug' => 'related_tag')
    );
    register_taxonomy('related_tag', array('whatson', 'post', 'product'), $args);

    $labels = array(
        'name' => _x('Course Kids Category', 'taxonomy general name'),
        'singular_name' => _x('Course Kids Category', 'taxonomy singular name'),
        'search_items' => __('Search Course Kids Categories'),
        'all_items' => __('All Course Kids Categories'),
        'parent_item' => __('Parent Course Kids Category'),
        'parent_item_colon' => __('Parent Course Kids Category:'),
        'edit_item' => __('Edit Course Kids Category'),
        'update_item' => __('Update Course Kids Category'),
        'add_new_item' => __('Add New Course Kids Category'),
        'new_item_name' => __('New Course Kids Category'),
        'menu_name' => __('Course Kids Categories')
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => false,
        'query_var' => true,
        'rewrite' => [
            'slug' => 'learn/kids',  // This controls the front-end URL
            'with_front' => false,     // Prevents default /taxonomy/ prefix
        ],
    );
    register_taxonomy('course_kids_category', array('product'), $args);

    $labels = array(
        'name' => _x('Course Adults Category', 'taxonomy general name'),
        'singular_name' => _x('Course Adults Category', 'taxonomy singular name'),
        'search_items' => __('Search Course Adults Categories'),
        'all_items' => __('All Course Adults Categories'),
        'parent_item' => __('Parent Course Adults Category'),
        'parent_item_colon' => __('Parent Course Adults Category:'),
        'edit_item' => __('Edit Course Adults Category'),
        'update_item' => __('Update Course Adults Category'),
        'add_new_item' => __('Add New Course Adults Category'),
        'new_item_name' => __('New Course Adults Category'),
        'menu_name' => __('Course Adults Categories')
    );
    $args = array(
        'labels' => $labels,
        'hierarchical' => false,
        'show_ui' => true,
        'show_admin_column' => false,
        'query_var' => true,
        'public' => true,
        'rewrite' => [
            'slug' => 'learn/adults',  // This controls the front-end URL
            'with_front' => false,     // Prevents default /taxonomy/ prefix
        ],
    );
    register_taxonomy('course_adults_category', array('product'), $args);
}
add_action('init', 'register_custom_taxonomies', 9);

// Redirect old-style URLs to the new-style URLs
add_action('template_redirect', function () {
    // Check if on the old-style URL
    if (strpos($_SERVER['REQUEST_URI'], '/course_adults_category/') !== false) {
        // Extract the slug from the URL
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/course_adults_category/', $path);
        if (isset($parts[1])) {
            $slug = trim($parts[1], '/');
            $new_url = home_url('/learn/adults/' . $slug . '/');
            wp_redirect($new_url, 301);
            exit;
        }
    }

    if (strpos($_SERVER['REQUEST_URI'], '/course_kids_category/') !== false) {
        // Extract the slug from the URL
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = explode('/course_kids_category/', $path);
        if (isset($parts[1])) {
            $slug = trim($parts[1], '/');
            $new_url = home_url('/learn/kids/' . $slug . '/');
            wp_redirect($new_url, 301);
            exit;
        }
    }
});




/**
 * Remove the taxonomy meta boxes from the edit admin pages
 *
 */
function remove_custom_meta_boxes()
{

    // Custom
    // remove_meta_box('event_categorydiv', 'whatson', 'side');
    remove_meta_box('tagsdiv-event_tag', 'whatson', 'side');
    // remove_meta_box('tagsdiv-exhibition_tag', 'whatson', 'side');
    remove_meta_box('tagsdiv-related_tag', 'whatson', 'side');
    remove_meta_box('tagsdiv-related_tag', 'product', 'side');
    remove_meta_box('tagsdiv-related_tag', 'post', 'side');
    remove_meta_box('tagsdiv-course_kids_category', 'product', 'side');
    remove_meta_box('tagsdiv-course_adults_category', 'product', 'side');

    // WooCommerce
    remove_meta_box('tagsdiv-product_tag', 'product', 'side');
    remove_meta_box('product_catdiv', 'product', 'side');
}
add_action('admin_menu', 'remove_custom_meta_boxes');

/**
 * Remove classes from HTML <body> class output
 *
 * @param $wp_classes
 * @param $extra_classes
 * @return array
 */
function remove_body_classes($wp_classes, $extra_classes)
{

    $classes_to_remove = array(
        'tag'
    );

    if (is_product()) {
        $classes_to_remove[] = 'woocommerce';
        $classes_to_remove[] = 'woocommerce-page';
    }

    $indexes = array();
    foreach ($wp_classes as $key => $class) {

        if (in_array($class, $classes_to_remove)) {

            $indexes[] = $key;
        }
    }

    foreach ($indexes as $index) {

        unset($wp_classes[$index]);
    }

    // Add the extra classes back untouched
    return array_merge($wp_classes, (array) $extra_classes);
}
add_filter('body_class', 'remove_body_classes', 10, 2);
