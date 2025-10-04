<?php

namespace TheStart\Core;

use Timber\Timber;

class TimberBootstrap
{
    public function __construct()
    {
        $this->init_timber();
        $this->check_timber_dependency();
    }

    private function init_timber()
    {
        if (class_exists('Timber\Timber')) {
            Timber::init();
            Timber::$dirname = array('templates', 'views');
        }
    }

    private function check_timber_dependency()
    {
        if (!class_exists('Timber')) {
            add_action('admin_notices', [$this, 'show_error']);
            add_filter('template_include', [$this, 'fallback_template']);
            add_filter('timber/meta/transform_value', '__return_true');
        }
    }

    public function show_error()
    {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
    }

    public function fallback_template($template)
    {
        return get_stylesheet_directory() . '/static/no-timber.html';
    }
}
