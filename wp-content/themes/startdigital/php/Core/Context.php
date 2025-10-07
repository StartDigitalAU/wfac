<?php

namespace TheStart\Core;

use Timber\Timber;
use TheStart\Services\Formatting\DateFormatter;
use TheStart\Services\Formatting\OpeningHoursFormatter;

class Context
{
    public function __construct()
    {
        add_filter('timber/context', array($this, 'add_to_context'));
        add_filter('timber/twig/functions', array($this, 'add_timber_functions'));
    }

    public function add_to_context($context)
    {
        if (function_exists('get_fields')) {
            $context['options'] = get_fields('options');
        }

        $context['menu'] = Timber::get_menu();
        $context['footer_bottom_menu'] = Timber::get_menu('Footer Bottom Menu');
        $context['site'] = new \Timber\Site();

        return $context;
    }

    public function add_timber_functions($functions)
    {
        $functions['timber_set_product'] = array(
            'callable' => array($this, 'timber_set_product')
        );

        $functions['juggle_taxonomies'] = array(
            'callable' => array($this, 'juggle_taxonomies')
        );

        $functions['juggle_dates'] = array(
            'callable' => array($this, 'juggle_dates')
        );

        $functions['get_news_posts'] = array(
            'callable' => array($this, 'get_news_posts')
        );

        $functions['get_opening_hours'] = array(
            'callable' => array($this, 'get_opening_hours')
        );

        return $functions;
    }

    public function timber_set_product($post)
    {
        global $product;
        $product = wc_get_product($post->ID);
        return $product;
    }

    public function juggle_taxonomies($post_id)
    {
        $taxonomies = ['event_category', 'exhibition_tag', 'course_kids_category', 'course_adults_category'];
        $terms = [];

        foreach ($taxonomies as $taxonomy) {
            $post_terms = get_the_terms($post_id, $taxonomy);

            if ($post_terms && !is_wp_error($post_terms)) {
                foreach ($post_terms as $term) {
                    $terms[] = array(
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'taxonomy' => $taxonomy
                    );
                }
            }
        }

        return $terms;
    }

    public function juggle_dates($post_id)
    {
        $formatter = new DateFormatter();
        $formatted_date = $formatter->format($post_id);
        return $formatted_date;
    }

    public function get_news_posts($limit = -1)
    {

        $args = array(
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        return Timber::get_posts($args);
    }

    public function get_opening_hours()
    {
        $opening_hours = new OpeningHoursFormatter();

        $return_array = array(
            'status' => $opening_hours->get_status(),
            'is_open' => $opening_hours->is_currently_open()
        );

        return $return_array;
    }
}
