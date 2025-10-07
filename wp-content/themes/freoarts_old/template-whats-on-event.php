<?php

/**
 * Template Name: What's On - Events
 *
 */

global $post;

global $body_class;
$body_class = 'page-whatson page-whatson-events is-event';

get_header(); ?>

<div class="whats-on-hero-landing" data-hero>
    <div class="container container--gutters">
        <?php
        $page_title = get_field('hero_title') ?? '';
        if ($page_title == '') {
            $page_title = get_the_title();
        }
        $hero_left_column = get_field('hero_left_column') ?? '';
        $hero_right_column = get_field('hero_right_column') ?? '';
        ?>
        <h1 class="whats-on-hero-landing__title title title--h1"><?= $page_title ?></h1>
        <?php if(!empty($hero_left_column) && !empty($hero_right_column)): ?>

            <div class="whats-on-hero-landing__grid">
                <div class="whats-on-hero-landing__col">
                    <?php if ($hero_left_column != '') { ?>
                        <p><strong><?= $hero_left_column ?></strong></p>
                    <?php } ?>
                </div>
                <div class="whats-on-hero-landing__col">
                    <?php if ($hero_right_column != '') { ?>
                        <p><?= $hero_right_column ?></p>
                    <?php } ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<div id="main" class="main-content whatson__landing-grid">
    <?php get_template_part('parts/whats-on/menu', '', ['type' => 'event']); ?>

    <div id="whats-on-loading">
        <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" preserveAspectRatio="xMidYMid" style="margin:auto;background:0 0;display:block;shape-rendering:auto" viewBox="0 0 100 100">
            <circle cx="50" cy="50" r="32" fill="none" stroke="#27282d" stroke-dasharray="50.26548245743669 50.26548245743669" stroke-linecap="round" stroke-width="8">
                <animateTransform attributeName="transform" dur="1.3513513513513513s" keyTimes="0;1" repeatCount="indefinite" type="rotate" values="0 50 50;360 50 50" />
            </circle>
        </svg>
    </div>

    <?php
    $ppp = 10;
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
    $args = array(
        'post_type' => 'whatson',
        'post_status' => 'publish',
        'posts_per_page' => $ppp,
        'order' => 'ASC',
        'orderby' => 'meta_value_num',
        'meta_key' => 'start_date',
        'paged' => $paged
    );
    $args['meta_query'] = array(
        'relation' => 'AND',
        array(
            'key'     => 'type',
            'value'   => 'event',
            'compare' => '=',
        ),
        array(
            'key'     => 'start_date',
            'value'   => date('Ymd'),
            'compare' => '>=',
        ),
    );

    $query = new WP_Query($args);
    ?>
    <div id="whats-on__ajax-wrapper">
        <?= render('parts/whats-on/whats-on-grid.php', ["pages" => $query->posts]); ?>
        <?php paginationLinks($query); ?>
    </div>
</div>

<?php get_footer(); ?>