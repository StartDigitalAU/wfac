<?php

/**
 * Template Name: Learn Categories
 *
 */

get_header();

global $wp;

$queried_object = get_queried_object();
$taxonomy = $queried_object->taxonomy;
$term_id = $queried_object->term_id;

// Pagination setup
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

// Set posts per page: 10 for first page, 9 for subsequent pages
$posts_per_page = ($paged === 1) ? 10 : 9;

// Calculate the correct offset for pagination
$offset = 0;
if ($paged > 1) {
    // First page has 10, subsequent pages have 9
    $offset = 10 + (($paged - 2) * 9);
}

$args = array(
    'post_type' => 'product',
    'tax_query' => array(
        array(
            'taxonomy' => $taxonomy,
            'field'    => 'term_id',
            'terms'    => $term_id,
        ),
    ),
    'posts_per_page' => $posts_per_page,
    'offset' => $offset,
    'orderby' => 'meta_value',
    'meta_key' => 'start_date',
    'order' => 'ASC',
    'meta_query' => array(
        'relation' => 'AND',
    ),
);

$args['meta_query'][] = [
    'key' => 'start_date',
    'value' => date('Ymd'),
    'compare' => '>=',
    'type' => 'DATE'
];

// Add tutor filter if present in URL
if (isset($_GET['tutor_id']) && !empty($_GET['tutor_id'])) {
    $tutor_id = intval($_GET['tutor_id']);
    $args['meta_query'][] = array(
        'key' => 'tutor',
        'value' => $tutor_id,
        'compare' => '='
    );
}

$query = new WP_Query($args);

// Calculate the correct max number of pages
$total_posts = $query->found_posts;
$max_pages = 1;
if ($total_posts > 10) {
    $remaining_posts = $total_posts - 10;
    $max_pages = 1 + ceil($remaining_posts / 9);
}
?>
<section class="main-content">
    <div class="container learn">
        <div class="learn__wrapper">
            <div class="learn__header-container">
                <div class="learn__breadcrumbs">
                    <a href="/" class="">Home</a>
                    <div class="circle"></div>
                    <a href="/learn" class="">Learn</a>
                </div>
                <h1 class="learn__header"><?php echo esc_html($queried_object->name); ?></h1>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            </div>
            <div class="learn__grid-container">
                <div class="learn__filter-wrappers">
                    <?php get_template_part('parts/learn/learn-menu'); ?>
                    <?php get_template_part('parts/courses/calendar'); ?>
                </div>
                <div class="learn__hero-grids">
                    <?php if ($query->have_posts()) :
                        $i = 1;
                        while ($query->have_posts()) : $query->the_post();
                            global $post;
                            if ($i === 1 && $paged === 1) {
                                render('parts/learn/learn-hero-card.php', [
                                    'learn' => $post,
                                ]);
                            } else {
                                render('parts/learn/learn-card.php', [
                                    'learn' => $post,
                                ]);
                            }
                            $i++;
                        endwhile;
                        wp_reset_postdata(); ?>
                    <?php else : ?>
                        <p>No courses found in this category.</p>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($max_pages > 1) : ?>
                    <div class="learn__pagination">
                        <?php
                        $big = 999999999;
                        echo paginate_links(array(
                            'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                            'format' => '?paged=%#%',
                            'current' => max(1, $paged),
                            'total' => $max_pages,
                            'prev_text' => '&laquo; Previous',
                            'next_text' => 'Next &raquo;',
                        ));
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?php if ($queried_object->description) : ?>
    <div class="taxonomy-description">
        <?php echo wp_kses_post($queried_object->description); ?>
    </div>
<?php endif; ?>
<?php get_footer(); ?>