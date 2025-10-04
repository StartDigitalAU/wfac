<?php

namespace TheStart\PostTypes;

use TheStart\PostTypes\AbstractPostType;

class TutorPostType extends AbstractPostType
{
    protected function get_post_type_key(): string
    {
        return 'tutor';
    }
    protected function get_singular_name(): string
    {
        return 'Tutor';
    }
    protected function get_plural_name(): string
    {
        return 'Tutors';
    }
    protected function get_slug(): string
    {
        return 'tutor';
    }
}
