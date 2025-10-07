<?php

/****************************************************
 *
 * GENERAL CONFIGURATION
 *
 ****************************************************/

function noadminbar(){
    return false;
}
// add_filter('show_admin_bar', 'noadminbar');

// Remove shortlink from <head>
remove_action('wp_head', 'wp_shortlink_wp_head');

// Set image quality
add_filter('jpeg_quality', function () { return 100;});
add_filter('wp_editor_set_quality', function () { return 100;});

// Disable REST API link in header
function remove_api () {
    remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
    remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
}
add_action( 'after_setup_theme', 'remove_api' );

/**
 * Replace default Wordpress email address with site email address
 *
 * @param $old
 * @return string|void
 */
function wp_custom_new_mail_from($old)
{

    $email_address = get_bloginfo('admin_email');

    if (!empty($email_address)) {

        return $email_address;
    }

    return $old;
}
add_filter('wp_mail_from', 'wp_custom_new_mail_from');

/**
 * Replace default Wordpress email address name with site title
 *
 * @param $old
 * @return string|void
 */
function wp_custom_new_mail_from_name($old)
{

    $name = get_bloginfo('name');

    if (!empty($name)) {

        return $name;
    }

    return $old;
}
add_filter('wp_mail_from_name', 'wp_custom_new_mail_from_name');

/**
 * Load all global variables
 *
 */
function load_globals()
{
    global $post, $wp_query;

    if (!is_admin()) {

        if (!isset($GLOBALS['site_url'])) {
            $GLOBALS['site_url'] = get_bloginfo('url');
        }
        if (!isset($GLOBALS['template_url'])) {
            $GLOBALS['template_url'] = get_bloginfo('template_url');
        }
        if (!isset($GLOBALS['template_path'])) {
            $GLOBALS['template_path'] = get_template_directory();
        }

        // Set ACF plugin fields
        if (!isset($GLOBALS['theme_options'])) {
            $GLOBALS['theme_options'] = function_exists('get_fields') ? get_fields('options') : array();
        }
        if (!isset($GLOBALS['page_fields']) && !empty($post)) {
            $GLOBALS['page_fields'] = function_exists('get_fields') ? get_fields($post->ID) : array();
        }

        // Default image
        if (!isset($GLOBALS['default_img'])) {
            $GLOBALS['default_img'] = $GLOBALS['template_url'] . '/img/ui/placeholder.jpg';
        }
        if (!isset($GLOBALS['default_img_small'])) {
            $GLOBALS['default_img_small'] = $GLOBALS['template_url'] . '/img/ui/placeholder-50x50.png';
        }
    }
}
add_action('template_redirect', 'load_globals');

add_filter( 'gform_disable_form_theme_css', '__return_true' );