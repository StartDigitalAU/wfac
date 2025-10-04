<?php

namespace TheStart\Taxonomies;

abstract class AbstractTaxonomy
{
    abstract protected function get_taxonomy_key(): string;
    abstract protected function get_post_types(): array;
    abstract protected function get_singular_name(): string;
    abstract protected function get_plural_name(): string;
    abstract protected function get_slug(): string;

    protected function is_hierarchical(): bool
    {
        return false;
    }

    protected function show_admin_column(): bool
    {
        return true;
    }

    protected function get_rewrite_args(): array
    {
        return array('slug' => $this->get_slug());
    }

    protected function get_additional_args(): array
    {
        return array();
    }

    public function register(): void
    {
        $labels = array(
            'name' => _x($this->get_plural_name(), 'taxonomy general name'),
            'singular_name' => _x($this->get_singular_name(), 'taxonomy singular name'),
            'search_items' => sprintf(__('Search %s'), $this->get_plural_name()),
            'all_items' => sprintf(__('All %s'), $this->get_plural_name()),
            'parent_item' => sprintf(__('Parent %s'), $this->get_singular_name()),
            'parent_item_colon' => sprintf(__('Parent %s:'), $this->get_singular_name()),
            'edit_item' => sprintf(__('Edit %s'), $this->get_singular_name()),
            'update_item' => sprintf(__('Update %s'), $this->get_singular_name()),
            'add_new_item' => sprintf(__('Add New %s'), $this->get_singular_name()),
            'new_item_name' => sprintf(__('New %s'), $this->get_singular_name()),
            'menu_name' => __($this->get_plural_name())
        );

        $args = array_merge(
            array(
                'labels' => $labels,
                'hierarchical' => $this->is_hierarchical(),
                'show_ui' => true,
                'show_admin_column' => $this->show_admin_column(),
                'query_var' => true,
                'rewrite' => $this->get_rewrite_args()
            ),
            $this->get_additional_args()
        );

        register_taxonomy($this->get_taxonomy_key(), $this->get_post_types(), $args);
    }
}
