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

    public function addQueryVars($vars)
    {
        $vars[] = 'display';
        return $vars;
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
