<?php

namespace TheStart\Taxonomies;

use TheStart\Taxonomies\AbstractTaxonomy;

class CourseKidsCategoryTaxonomy extends AbstractTaxonomy
{
    protected function get_taxonomy_key(): string
    {
        return 'course_kids_category';
    }
    protected function get_post_types(): array
    {
        return array('product');
    }
    protected function get_singular_name(): string
    {
        return 'Course Kids Category';
    }
    protected function get_plural_name(): string
    {
        return 'Course Kids Categories';
    }
    protected function get_slug(): string
    {
        return 'learn/kids';
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
}
