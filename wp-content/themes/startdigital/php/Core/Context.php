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

        // Main Menu Items
        $context['top_menu'] = Timber::get_menu('Top Menu');

        //Footer Menu Items
        $context['footer_bottom_menu'] = Timber::get_menu('Footer Bottom Menu');
        $context['program_menu'] = Timber::get_menu('Program');
        $context['art_classes_menu'] = Timber::get_menu('Art Classes');
        $context['visit_menu'] = Timber::get_menu('Visit');
        $context['engage_menu'] = Timber::get_menu('Engage');
        $context['about_menu'] = Timber::get_menu('About');

        $context['site'] = new \Timber\Site();

        return $context;
    }

    public function add_timber_functions($functions)
    {
        $method_names = [
            'timber_set_product',
            'juggle_taxonomies',
            'juggle_dates',
            'get_news_posts',
            'get_opening_hours',
            'get_exhibitions',
            'get_art_classes',
            'get_events',
            'get_child_pages',
            'get_tutors_filtering',
            'get_whats_on',
        ];

        foreach ($method_names as $method) {
            $functions[$method] = ['callable' => [$this, $method]];
        }

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

    public function get_exhibitions($limit = -1)
    {

        // An exhibition is a whats on post type with
        // an exhibition_tag
        $args = array(
            'post_type' => 'whatson',
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'exhibition_tag',
                    'operator' => 'EXISTS'
                ),
            )
        );

        return Timber::get_posts($args);
    }

    public function get_art_classes($limit = -1)
    {
        // All products on this site are an artclass
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        return Timber::get_posts($args);
    }

    public function get_events($limit = -1)
    {
        // An event is a whatson post type with
        // either an event tag or event category
        $args = array(
            'post_type' => 'whatson',
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
            'tax_query' => array(
                'relation' => 'OR',
                array(
                    'taxonomy' => 'event_tag',
                    'operator' => 'EXISTS'
                ),
                array(
                    'taxonomy' => 'event_category',
                    'operator' => 'EXISTS'
                )
            )
        );

        return Timber::get_posts($args);
    }

    public function get_child_pages(string $parent_page, int $limit = -1)
    {
        $parent = Timber::get_post_by('slug', $parent_page);

        if (!$parent) {
            return null;
        }

        $args = array(
            'post_type' => 'page',
            'post_parent' => $parent->ID,
            'orderby' => 'menu_order',
            'order' => 'ASC',
            'posts_per_page' => $limit,
        );

        return Timber::get_posts($args);
    }

    public function get_tutors_filtering()
    {
        global $wpdb;

        $tutors = $wpdb->get_results(
            "SELECT post_title as label, post_name as value 
             FROM {$wpdb->posts} 
             WHERE post_type = 'tutor' 
             AND post_status = 'publish'
             ORDER BY post_title ASC"
        );

        array_unshift($tutors, (object)[
            'value' => 'all',
            'label' => 'All',
            'default' => true
        ]);

        return $tutors;
    }

    public function get_whats_on($limit)
    {
        $args = array(
            'post_type' => array('whatson', 'product'),
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        return Timber::get_posts($args);
    }
}
