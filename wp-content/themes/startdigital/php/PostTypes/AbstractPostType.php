<?php

namespace TheStart\PostTypes;

abstract class AbstractPostType
{
    abstract protected function get_post_type_key(): string;
    abstract protected function get_singular_name(): string;
    abstract protected function get_plural_name(): string;
    abstract protected function get_slug(): string;

    protected function get_supports(): array
    {
        return array('title', 'page-attributes', 'editor');
    }

    protected function is_hierarchical(): bool
    {
        return false;
    }

    protected function has_archive(): bool
    {
        return false;
    }

    protected function get_menu_position(): ?int
    {
        return null;
    }

    protected function get_rewrite_args(): array
    {
        return array(
            'slug' => $this->get_slug(),
            'with_front' => false,
            'pages' => false,
            'feeds' => false,
        );
    }

    protected function get_additional_args(): array
    {
        return array();
    }

    public function register(): void
    {
        $labels = array(
            'name' => $this->get_plural_name(),
            'singular_name' => $this->get_singular_name(),
            'menu_name' => $this->get_plural_name(),
            'name_admin_bar' => $this->get_plural_name(),
            'parent_item_colon' => sprintf('Parent %s:', $this->get_singular_name()),
            'all_items' => sprintf('All %s', $this->get_plural_name()),
            'add_new_item' => sprintf('Add New %s', $this->get_singular_name()),
            'add_new' => 'Add New',
            'new_item' => sprintf('New %s', $this->get_singular_name()),
            'edit_item' => sprintf('Edit %s', $this->get_singular_name()),
            'update_item' => sprintf('Update %s', $this->get_singular_name()),
            'view_item' => sprintf('View %s', $this->get_singular_name()),
            'search_items' => sprintf('Search %s', $this->get_plural_name()),
            'not_found' => 'Not found',
            'not_found_in_trash' => 'Not found in Trash',
        );

        $args = array_merge(
            array(
                'label' => $this->get_plural_name(),
                'labels' => $labels,
                'supports' => $this->get_supports(),
                'hierarchical' => $this->is_hierarchical(),
                'public' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'menu_position' => $this->get_menu_position(),
                'show_in_admin_bar' => true,
                'show_in_nav_menus' => true,
                'can_export' => true,
                'has_archive' => $this->has_archive(),
                'exclude_from_search' => false,
                'publicly_queryable' => true,
                'rewrite' => $this->get_rewrite_args(),
                'capability_type' => 'post',
            ),
            $this->get_additional_args()
        );

        register_post_type($this->get_post_type_key(), $args);

        $this->remove_supports();
    }

    protected function remove_supports(): void
    {
        // Override in child classes to remove specific supports
    }
}
