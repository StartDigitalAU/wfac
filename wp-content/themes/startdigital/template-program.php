<?php

/**
 * Template Name: Program Pages
 * Template Post Type: page
 */

use Timber\Timber;

global $post;

$context = Timber::context();
$timberPost = Timber::get_post($post->ID);
$context['post'] = $timberPost;
$templates = array("templates/template-program.twig", 'page.twig');

Timber::render($templates, $context);
