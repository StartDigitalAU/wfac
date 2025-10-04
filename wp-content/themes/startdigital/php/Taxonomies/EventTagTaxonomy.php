<?php

namespace TheStart\Taxonomies;

use TheStart\Taxonomies\AbstractTaxonomy;

class EventTagTaxonomy extends AbstractTaxonomy
{
    protected function get_taxonomy_key(): string
    {
        return 'event_tag';
    }
    protected function get_post_types(): array
    {
        return array('whatson');
    }
    protected function get_singular_name(): string
    {
        return 'Event Tag';
    }
    protected function get_plural_name(): string
    {
        return 'Event Tags';
    }
    protected function get_slug(): string
    {
        return 'whats-on/event_tag';
    }
}
