<?php

// Global var init
global $post, $wp_query;

// Globals
if (!array_key_exists('theme_options', $GLOBALS)) {
    $GLOBALS['theme_options'] = $theme_options = get_fields('options');
}

$GLOBALS['wp_url'] = $wp_url = get_bloginfo('wpurl');
$GLOBALS['site_name'] = $site_name = get_bloginfo('name');
$GLOBALS['site_description'] = $site_description = get_bloginfo('description');

$GLOBALS['preload_assets'] = array();

$GLOBALS['template_directory_url'] = $template_directory_url = get_bloginfo('template_directory');
$GLOBALS['is_front_page'] = $is_front_page = is_front_page();
$GLOBALS['is_home_page'] = $is_home_page = is_home();
if (is_singular()) {
    $GLOBALS['id'] = $wp_query->get_queried_object();
    $GLOBALS['id'] = $GLOBALS['id']->ID;
}

$meta_title = wp_title('|', false , 'right');

if ($is_front_page) {
    $meta_title = $GLOBALS['site_name'];
}

$GLOBALS['meta_title'] = isset($GLOBALS['meta_title']) ? $GLOBALS['meta_title'] : $meta_title;
