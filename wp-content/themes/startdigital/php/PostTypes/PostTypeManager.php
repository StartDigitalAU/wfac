<?php

namespace TheStart\PostTypes;

use TheStart\PostTypes\WhatsOnPostType;
use TheStart\PostTypes\TutorPostType;

class PostTypeManager
{
    private array $post_types = array();

    public function __construct()
    {
        $this->post_types = array(
            new WhatsOnPostType(),
            new TutorPostType(),
        );

        add_action('init', array($this, 'modify_core_post_types'), 0);
        add_action('init', array($this, 'register_post_types'));
        add_action('admin_menu', array($this, 'change_posts_menu_labels'));
        add_action('init', array($this, 'change_posts_object_labels'));
    }

    public function modify_core_post_types(): void
    {
        // Remove core post type support before custom post types are registered
        remove_post_type_support('post', 'thumbnail');
        remove_post_type_support('post', 'excerpt');
    }

    public function register_post_types(): void
    {
        foreach ($this->post_types as $post_type) {
            $post_type->register();
        }
    }

    public function change_posts_menu_labels(): void
    {
        global $menu;
        global $submenu;

        $menu[5][0] = 'News';

        if (isset($submenu['edit.php'][5])) {
            $submenu['edit.php'][5][0] = 'News';
        }

        if (isset($submenu['edit.php'][10])) {
            $submenu['edit.php'][10][0] = 'Add News';
        }

        if (isset($submenu['edit.php'][16])) {
            $submenu['edit.php'][16][0] = 'News Tags';
        }
    }

    public function change_posts_object_labels(): void
    {
        global $wp_post_types;

        $labels = &$wp_post_types['post']->labels;
        $labels->name = 'News';
        $labels->singular_name = 'News';
        $labels->add_new = 'Add News';
        $labels->add_new_item = 'Add News';
        $labels->edit_item = 'Edit News';
        $labels->new_item = 'News';
        $labels->view_item = 'View News';
        $labels->search_items = 'Search News';
        $labels->not_found = 'No News found';
        $labels->not_found_in_trash = 'No News found in Trash';
        $labels->all_items = 'All News';
        $labels->menu_name = 'News';
        $labels->name_admin_bar = 'News';
    }
}
