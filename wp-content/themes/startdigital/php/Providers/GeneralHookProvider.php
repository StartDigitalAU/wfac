<?php

namespace TheStart\Providers;

use TheStart\Services\ConfigurationService;

/**
 * General Hook Provider
 * Handles general WordPress configuration hooks
 */
class GeneralHookProvider extends HookProvider
{
    private ConfigurationService $configurationService;

    public function __construct(ConfigurationService $configurationService)
    {
        $this->configurationService = $configurationService;
    }

    /**
     * Register all hooks for general configuration
     */
    public function register(): void
    {
        // Remove shortlink from head
        remove_action('wp_head', 'wp_shortlink_wp_head');

        // Set image quality
        $this->addFilter('jpeg_quality', [$this->configurationService, 'setImageQuality']);
        $this->addFilter('wp_editor_set_quality', [$this->configurationService, 'setImageQuality']);

        // Remove REST API links from head
        $this->addAction('after_setup_theme', [$this->configurationService, 'removeApiLinks']);

        // Custom email configuration
        $this->addFilter('wp_mail_from', [$this->configurationService, 'customMailFrom']);
        $this->addFilter('wp_mail_from_name', [$this->configurationService, 'customMailFromName']);

        // Load global variables
        $this->addAction('template_redirect', [$this->configurationService, 'loadGlobals']);

        // Disable Gravity Forms theme CSS
        $this->addFilter('gform_disable_form_theme_css', [$this->configurationService, 'disableGravityFormsCSS']);

        // Load images externally because the media library is HUGE
        $this->addFilter('upload_dir', [$this->configurationService, 'loadImagesExternally']);

        // $this->addFilter('show_admin_bar', [$this->configurationService, 'disableAdminBar']);
    }
}
