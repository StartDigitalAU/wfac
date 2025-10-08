<?php

namespace TheStart\Services;

/**
 * Configuration Service
 * Handles general WordPress configuration and setup
 */
class ConfigurationService
{
    /**
     * Set JPEG image quality to 100%
     *
     * @return int
     */
    public function setImageQuality(): int
    {
        return 100;
    }

    /**
     * Replace default WordPress email address with site email address
     *
     * @param string $old The old email address
     * @return string The new email address
     */
    public function customMailFrom(string $old): string
    {
        $email_address = get_bloginfo('admin_email');

        if (!empty($email_address)) {
            return $email_address;
        }

        return $old;
    }

    /**
     * Replace default WordPress email address name with site title
     *
     * @param string $old The old email name
     * @return string The new email name
     */
    public function customMailFromName(string $old): string
    {
        $name = get_bloginfo('name');

        if (!empty($name)) {
            return $name;
        }

        return $old;
    }

    /**
     * Remove REST API links from head
     */
    public function removeApiLinks(): void
    {
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
    }

    /**
     * Load global variables for theme
     */
    public function loadGlobals(): void
    {
        global $post;

        if (is_admin()) {
            return;
        }

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

        // Default images
        if (!isset($GLOBALS['default_img'])) {
            $GLOBALS['default_img'] = $GLOBALS['template_url'] . '/img/ui/placeholder.jpg';
        }

        if (!isset($GLOBALS['default_img_small'])) {
            $GLOBALS['default_img_small'] = $GLOBALS['template_url'] . '/img/ui/placeholder-50x50.png';
        }
    }

    /**
     * Disable admin bar for non-admin users
     *
     * @return bool
     */
    public function disableAdminBar(): bool
    {
        return false;
    }

    /**
     * Disable Gravity Forms theme CSS
     *
     * @return bool
     */
    public function disableGravityFormsCSS(): bool
    {
        return true;
    }


    public function loadImagesExternally($dirs)
    {
        $hosts = [
            'wfac.test',
            'staging.wfac.org.au'
        ];

        if (in_array($_SERVER['HTTP_HOST'], $hosts)) {
            $dirs['baseurl'] = 'https://wfac.org.au/wp-content/uploads';
            $dirs['url']     = $dirs['baseurl'] . $dirs['subdir'];
        }
        return $dirs;
    }
}
