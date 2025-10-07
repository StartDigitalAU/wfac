<?php

/**
 * This file contains all the hooks to include and present JavaScript in the native WordPress queue system.
 *
 * @package HumaanFunctions\JavaScriptIncludes
 *
 * @copyright 2013-2015 Humaan
 */

/**
 * Boolean check to see if the user isn't viewing the login page or admin section.
 *
 * This can be considered a "front-end only" hook.
 *
 * @author Lee Karolczak <lee@humaan.com.au>
 *
 * @link http://stackoverflow.com/questions/12148050/front-end-only-version-of-init-hook/12220501#12220501 StackOverflow answer where this function is from
 *
 * @return bool
 */
function is_login_page()
{
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}

function register_theme_scripts_on_init()
{
    $template_uri = get_stylesheet_directory_uri();
    if (is_admin()) {

        wp_enqueue_style('jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
        wp_enqueue_style('admin-css', $template_uri . '/css/admin.css');

        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('test123-functions', $template_uri . '/js/src/admin-functions.js', array('jquery'), false, true);
    }
}
add_action('init', 'register_theme_scripts_on_init');

/**
 * Registers all the theme scripts with the WordPress queue system.
 *
 * When in development, set `$dev` to `true` to use the source JavaScript files,
 * then when in production, set `$dev` to `false` to use the production JavaScript build.
 *
 * @return void
 */
function register_theme_scripts()
{

    $template_uri = get_stylesheet_directory_uri();

    if (!is_admin() && !is_login_page()) {

        wp_deregister_script('jquery');
        wp_enqueue_script('jquery', $template_uri . '/js/lib/jquery-1.11.2.min.js');


        wp_enqueue_script('google-maps-api', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyC3Gf6ZSbSJf6wPMynZ578cG8Q-UiiMsjM', array('jquery'), false, true);
        wp_enqueue_script('jquery-ui-datepicker');


        wp_enqueue_script('jquery-easing', $template_uri . '/js/lib/jquery.easing.1.3.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-validate', $template_uri . '/js/lib/jquery.validate.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-placeholder', $template_uri . '/js/lib/jquery.placeholder.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-slick', $template_uri . '/js/lib/slick.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-modaal', $template_uri . '/js/lib/modaal.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-share', $template_uri . '/js/lib/jquery.share.min.js', array('jquery'), false, true);
        wp_enqueue_script('device', $template_uri . '/js/lib/device.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-multiple-select', $template_uri . '/js/lib/jquery.multiple.select.min.js', array('jquery'), false, true);
        wp_enqueue_script('jquery-waypoints', $template_uri . '/js/lib/jquery.waypoints.min.js', array('jquery'), false, true);

        // The theme's JS
        cachebust_enqueue_script('theme-calendar', '/js/min/calendar-ugl.js', array('jquery'), false, true);
        cachebust_enqueue_script('theme-functions', '/js/min/functions-ugl.js', array('jquery'), '6', true);

        if (is_page_template('template-whats-on.php')) {
            cachebust_enqueue_script('theme-whatson', '/js/min/whatson-ugl.js', array('jquery'), false, true);
        }
        if (is_page_template('template-courses.php')) {
            cachebust_enqueue_script('theme-courses', '/js/min/courses-ugl.js', array('jquery'), false, true);
        }
        if (is_tax('course_adults_category') || is_tax('course_kids_category')) {
            cachebust_enqueue_script('theme-learn', '/js/min/learn-courses-ugl.js', array('jquery'), false, true);
        }

        if (is_home()) {
            cachebust_enqueue_script('theme-news', '/js/min/news-ugl.js', array('jquery'), false, true);
        }
    }
    // wp_enqueue_script('gsap', $template_uri . '/js/min/gsap.min.js', array('jquery'), false, true); // TODO Needs to be plonked in the right spot
    // wp_enqueue_script('gsapScrollTrigger', $template_uri . '/js/min/gsap.scrollTrigger.min.js', array('jquery'), false, true); // TODO Needs to be plonked in the right spot
    // wp_enqueue_script('gsapScrollTo', $template_uri . '/js/min/gsap.scrollTo.min.js', array('jquery'), false, true); // TODO Needs to be plonked in the right spot
}
add_action('wp_enqueue_scripts', 'register_theme_scripts');

if (!function_exists('cachebust_enqueue_script')) {
    function cachebust_enqueue_script($handle, $path_from_template, $dependencies = [])
    {

        $template_uri = get_stylesheet_directory_uri();

        $ver = filemtime(TEMPLATEPATH . $path_from_template);

        wp_enqueue_script($handle, $template_uri . $path_from_template, $dependencies, $ver, true);
    }
}
