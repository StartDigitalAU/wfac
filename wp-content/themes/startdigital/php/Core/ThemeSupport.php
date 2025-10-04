<?php

namespace TheStart\Core;

class ThemeSupport
{
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'theme_supports'));
    }

    public function theme_supports()
    {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support('menus');
        add_theme_support('woocommerce');

        register_nav_menus(
            array(
                'primary' => __('Primary Menu', 'startdigital'),
            )
        );

        add_theme_support('editor-styles');
        add_editor_style('static/editor.css');
    }
}
