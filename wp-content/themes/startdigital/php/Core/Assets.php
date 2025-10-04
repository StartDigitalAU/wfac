<?php

namespace TheStart\Core;

class Assets
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'register_assets'));
    }

    public function register_assets()
    {
        $style_version = filemtime(get_stylesheet_directory() . '/static/style.css') ?: '';
        $script_version = filemtime(get_stylesheet_directory() . '/static/site.js') ?: '';

        wp_enqueue_style('startdigital', get_stylesheet_directory_uri() . '/static/style.css', false, $style_version);
        wp_enqueue_script('startdigital', get_stylesheet_directory_uri() . '/static/site.js', false, $script_version);
    }
}
