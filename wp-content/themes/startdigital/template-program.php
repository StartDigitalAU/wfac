<?php

/**
 * Template Name: Program Pages
 * Template Post Type: page
 */

use \Timber\Timber;

use TheStart\Controllers\ProgramPageController;

global $post;

$controller = new ProgramPageController($post);
$context = $controller->get_context();

$templates = array("templates/template-program.twig", 'page.twig');

Timber::render($templates, $context);
