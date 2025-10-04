<?php

namespace TheStart\Taxonomies;

use TheStart\Taxonomies\AbstractTaxonomy;

class RelatedTagTaxonomy extends AbstractTaxonomy
{
    protected function get_taxonomy_key(): string
    {
        return 'related_tag';
    }
    protected function get_post_types(): array
    {
        return array('whatson', 'post', 'product');
    }
    protected function get_singular_name(): string
    {
        return 'Related Tag';
    }
    protected function get_plural_name(): string
    {
        return 'Related Tags';
    }
    protected function get_slug(): string
    {
        return 'related_tag';
    }
    protected function show_admin_column(): bool
    {
        return false;
    }
}
