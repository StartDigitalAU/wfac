<?php

namespace TheStart\Controllers;

use \Timber\Timber;

class ProgramPageController
{
    private $post;
    private $posts_to_show;
    private $limit;

    public function __construct($post)
    {
        $this->post = $post;
        $this->posts_to_show = get_field('posts_to_show', $post->ID);
        $this->limit = get_field('posts_limit', $post->ID) ?: 12;
    }

    public function get_context()
    {
        $context = Timber::context();
        $context['post'] = Timber::get_post($this->post->ID);
        $context['program_posts'] = $this->get_program_posts();
        $context['pagination'] = $this->get_pagination();

        return $context;
    }

    private function get_program_posts()
    {
        $args = array(
            'post_type' => $this->get_post_types(),
            'posts_per_page' => $this->limit,
            'orderby' => 'date',
            'order' => 'DESC',
            'paged' => 1, // Initial load is page 1
        );

        $tax_query = $this->get_tax_query();

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        $posts = Timber::get_posts($args);

        if (!$posts) {
            return $posts;
        }

        foreach ($posts as $post) {
            $this->enhance_post_data($post);
        }

        return $posts;
    }

    private function get_pagination()
    {
        $args = array(
            'post_type' => $this->get_post_types(),
            'posts_per_page' => $this->limit,
        );

        $tax_query = $this->get_tax_query();

        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        }

        $query = new \WP_Query($args);

        return [
            'current' => 1,
            'has_more' => 1 < $query->max_num_pages,
            'total' => $query->max_num_pages
        ];
    }

    private function enhance_post_data($post)
    {
        // Add display price for products
        if ($post->post_type === 'product') {
            $post->display_price = $this->get_display_price($post);
        }
    }

    private function get_display_price($post)
    {
        // Check for custom cost field first
        $custom_cost = get_field('cost', $post->ID);

        if ($custom_cost) {
            return $custom_cost;
        }

        // Fallback to WooCommerce price
        $product = wc_get_product($post->ID);

        if ($product) {
            return $product->get_price();
        }

        return '';
    }

    private function get_post_types()
    {
        return match ($this->posts_to_show) {
            'all' => array('whatson', 'product'),
            'exhibition', 'event' => array('whatson'),
            default => array($this->posts_to_show),
        };
    }

    private function get_tax_query()
    {
        return match ($this->posts_to_show) {
            'exhibition' => array(
                array(
                    'taxonomy' => 'exhibition_tag',
                    'operator' => 'EXISTS'
                ),
            ),
            'event' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'event_tag',
                    'operator' => 'EXISTS'
                ),
                array(
                    'taxonomy' => 'event_category',
                    'operator' => 'EXISTS'
                )
            ),
            default => array(),
        };
    }
}
