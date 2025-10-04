<?php

namespace TheStart\Taxonomies;

use TheStart\Taxonomies\AbstractTaxonomy;

class CourseAdultsCategoryTaxonomy extends AbstractTaxonomy
{
    protected function get_taxonomy_key(): string
    {
        return 'course_adults_category';
    }
    protected function get_post_types(): array
    {
        return array('product');
    }
    protected function get_singular_name(): string
    {
        return 'Course Adults Category';
    }
    protected function get_plural_name(): string
    {
        return 'Course Adults Categories';
    }
    protected function get_slug(): string
    {
        return 'learn/adults';
    }
    protected function show_admin_column(): bool
    {
        return false;
    }

    protected function get_rewrite_args(): array
    {
        return array(
            'slug' => $this->get_slug(),
            'with_front' => false,
        );
    }

    protected function get_additional_args(): array
    {
        return array('public' => true);
    }
}
