<?php

namespace TheStart\Taxonomies;

use TheStart\Taxonomies\EventCategoryTaxonomy;
use TheStart\Taxonomies\EventTagTaxonomy;
use TheStart\Taxonomies\ExhibitionTagTaxonomy;
use TheStart\Taxonomies\RelatedTagTaxonomy;
use TheStart\Taxonomies\CourseAdultsCategoryTaxonomy;
use TheStart\Taxonomies\CourseKidsCategoryTaxonomy;

class TaxonomyManager
{
    private array $taxonomies = array();

    public function __construct()
    {
        $this->taxonomies = array(
            new EventCategoryTaxonomy(),
            new EventTagTaxonomy(),
            new ExhibitionTagTaxonomy(),
            new RelatedTagTaxonomy(),
            new CourseKidsCategoryTaxonomy(),
            new CourseAdultsCategoryTaxonomy(),
        );

        add_action('init', array($this, 'register_taxonomies'), 9);
        add_action('template_redirect', array($this, 'handle_legacy_redirects'));
        add_action('admin_menu', array($this, 'remove_meta_boxes'));
        add_filter('body_class', array($this, 'remove_body_classes'), 10, 2);
    }

    public function register_taxonomies(): void
    {
        foreach ($this->taxonomies as $taxonomy) {
            $taxonomy->register();
        }
    }

    public function handle_legacy_redirects(): void
    {
        $request_uri = $_SERVER['REQUEST_URI'];

        if (strpos($request_uri, '/course_adults_category/') !== false) {
            $this->redirect_legacy_url($request_uri, '/course_adults_category/', '/learn/adults/');
        }

        if (strpos($request_uri, '/course_kids_category/') !== false) {
            $this->redirect_legacy_url($request_uri, '/course_kids_category/', '/learn/kids/');
        }
    }

    private function redirect_legacy_url(string $request_uri, string $old_path, string $new_path): void
    {
        $path = parse_url($request_uri, PHP_URL_PATH);
        $parts = explode($old_path, $path);

        if (isset($parts[1])) {
            $slug = trim($parts[1], '/');
            $new_url = home_url($new_path . $slug . '/');
            wp_redirect($new_url, 301);
            exit;
        }
    }

    public function remove_meta_boxes(): void
    {
        remove_meta_box('tagsdiv-event_tag', 'whatson', 'side');
        remove_meta_box('tagsdiv-related_tag', 'whatson', 'side');
        remove_meta_box('tagsdiv-related_tag', 'product', 'side');
        remove_meta_box('tagsdiv-related_tag', 'post', 'side');
        remove_meta_box('tagsdiv-course_kids_category', 'product', 'side');
        remove_meta_box('tagsdiv-course_adults_category', 'product', 'side');
        remove_meta_box('tagsdiv-product_tag', 'product', 'side');
        remove_meta_box('product_catdiv', 'product', 'side');
    }

    public function remove_body_classes(array $wp_classes, $extra_classes): array
    {
        $classes_to_remove = array('tag');

        if (is_product()) {
            $classes_to_remove[] = 'woocommerce';
            $classes_to_remove[] = 'woocommerce-page';
        }

        $indexes = array();
        foreach ($wp_classes as $key => $class) {
            if (in_array($class, $classes_to_remove)) {
                $indexes[] = $key;
            }
        }

        foreach ($indexes as $index) {
            unset($wp_classes[$index]);
        }

        return array_merge($wp_classes, (array) $extra_classes);
    }
}
