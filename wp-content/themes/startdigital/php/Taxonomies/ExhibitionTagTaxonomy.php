<?php

namespace TheStart\Taxonomies;

use TheStart\Taxonomies\AbstractTaxonomy;

class ExhibitionTagTaxonomy extends AbstractTaxonomy
{
    protected function get_taxonomy_key(): string
    {
        return 'exhibition_tag';
    }
    protected function get_post_types(): array
    {
        return array('whatson');
    }
    protected function get_singular_name(): string
    {
        return 'Exhibition Tag';
    }
    protected function get_plural_name(): string
    {
        return 'Exhibition Tags';
    }
    protected function get_slug(): string
    {
        return 'whats-on/exhibition_tag';
    }
}
