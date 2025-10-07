<?php

/**
 * This file contains all the overrides used to suppress default WordPress behavior.
 *
 * @package HumaanFunctions\Overrides
 *
 * @copyright 2013-2015 Humaan
 */

add_filter('wpseo_metabox_prio', 'yoast_metabox_priority');

/**
 * Overrides the default Yoast meta box to make it appear lower on the page.
 *
 * @author Lee Karolczak <lee@humaan.com.au>
 *
 * @return string A string of the priority, which is statically defined as `low`
 */
function yoast_metabox_priority()
{
    return 'low';
}

// Hide the admin bar completely
// add_filter( 'show_admin_bar', 'no_admin_bar' );

/**
 * Defines whether or not to show the Admin Bar or not.
 *
 * We statically define this as `false` so it doesn't appear.
 *
 * @author Lee Karolczak <lee@humaan.com.au>
 *
 * @return bool Boolean value to specify whether the Admin Bar should be shown
 */
function no_admin_bar()
{
    return false;
}

// Kill emoji with fire!
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Disable feeds
/**
 * Displays a HTML error message then exits the PHP script by calling `die()`.
 *
 * @author Lee Karolczak <lee@humaan.com.au>
 *
 * @link http://codex.wordpress.org/Function_Reference/wp_die Function reference for wp_die
 *
 * @return void
 */
function fb_disable_feed()
{
    wp_die(__('No feed available, please visit our <a href="' . site_url('/') . '">homepage</a>!'));
}

add_action('do_feed', 'fb_disable_feed', 1);
add_action('do_feed_rdf', 'fb_disable_feed', 1);
add_action('do_feed_rss', 'fb_disable_feed', 1);
add_action('do_feed_rss2', 'fb_disable_feed', 1);
add_action('do_feed_atom', 'fb_disable_feed', 1);
add_action('do_feed_rss2_comments', 'fb_disable_feed', 1);
add_action('do_feed_atom_comments', 'fb_disable_feed', 1);

// Remove feed link from header
remove_action('wp_head', 'e_extra', 3); // Extra feeds such as category feeds
remove_action('wp_head', 'feed_links', 2); // General feeds: Post and Comment Feed
remove_action('wp_head', 'feed_links_extra', 3);

add_filter('excerpt_more', 'new_excerpt_more');

// Removes the shortlink from the WordPress header as it's completely redundant.
remove_action('wp_head', 'wp_shortlink_wp_head');
remove_action('template_redirect', 'wp_shortlink_header', 11);

// Go away oEmbed and REST!
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
remove_action('wp_head', 'rest_output_link_wp_head', 11);

// Removes Comments from post and pages
add_action('init', 'humaan_remove_comment_support', 100);

function humaan_remove_comment_support()
{
    remove_post_type_support('page', 'comments');
}

/**
 * Replaces the default "More &raquo;" text with an ellipsis.
 *
 * @author Lee Karolczak <lee@humaan.com.au>
 *
 * @param  string $more A string that contains the WordPress excerpt when called via a filter - this value is ignored
 * @return string       A string with an ellipsis
 */
function new_excerpt_more($more)
{
    return '&hellip;';
}
