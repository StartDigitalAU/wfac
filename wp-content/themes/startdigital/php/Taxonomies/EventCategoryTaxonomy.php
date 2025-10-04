<?php

namespace TheStart\Taxonomies;

use TheStart\Taxonomies\AbstractTaxonomy;

class EventCategoryTaxonomy extends AbstractTaxonomy
{
    protected function get_taxonomy_key(): string
    {
        return 'event_category';
    }
    protected function get_post_types(): array
    {
        return array('whatson');
    }
    protected function get_singular_name(): string
    {
        return 'Event Category';
    }
    protected function get_plural_name(): string
    {
        return 'Event Categories';
    }
    protected function get_slug(): string
    {
        return 'whats-on/event_category';
    }
    protected function is_hierarchical(): bool
    {
        return true;
    }
}
