<?php

namespace TheStart\PostTypes;

use TheStart\PostTypes\AbstractPostType;

class WhatsOnPostType extends AbstractPostType
{
    protected function get_post_type_key(): string
    {
        return 'whatson';
    }
    protected function get_singular_name(): string
    {
        return "What's On";
    }
    protected function get_plural_name(): string
    {
        return "What's On";
    }
    protected function get_slug(): string
    {
        return 'whats-on/post';
    }

    protected function remove_supports(): void
    {
        remove_post_type_support('whatson', 'comments');
    }
}
