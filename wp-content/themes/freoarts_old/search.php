<?php

global $body_class;
$body_class = 'page-about page-search';

global $wp_query;

/**
 * Get ALL posts for search query
 *
 */

// Search query null check
$search_term = isset($wp_query->query_vars['s']) ? $wp_query->query_vars['s'] : '';
$post_type = isset($wp_query->query_vars['post_type']) ? $wp_query->query_vars['post_type'] : 'any';

$args = array(
    's' => $search_term,
    'post_type' => $post_type,
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'sentence' => true
);

if ($post_type == 'any' || empty($post_type)) {
    $args['post_type'] = array(
        'post',
        'page',
        'whatson',
        'product'
    );
}

// Modify the query based on any filters applied
$filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '';

if (!empty($filter)) {
    switch ($filter) {
        case 'exhibitions':
            $args['post_type'] = 'whatson';
            $args['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => 'type',
                    'value' => 'exhibition',
                    'type' => 'string',
                    'compare' => '='
                )
            );
            break;
            
        case 'events':
            $args['post_type'] = 'whatson';
            $args['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => 'type',
                    'value' => 'event',
                    'type' => 'string',
                    'compare' => '='
                )
            );
            break;
            
        case 'news':
            $args['post_type'] = 'post';
            break;
            
        case 'pages':
            $args['post_type'] = 'page';
            break;
            
        case 'courses':
            $args['post_type'] = 'product';
            $args['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'key' => 'start_date',
                    'value' => date('Ymd'),
                    'type' => 'numeric',
                    'compare' => '>='
                )
            );
            break;
    }
}

$search_query = new WP_Query($args);

$total_posts = isset($search_query->found_posts) ? (int)$search_query->found_posts : 0;

// List of filters and counts
$filters = array(
    'exhibitions' => 0,
    'events' => 0,
    'courses' => 0,
    'news' => 0,
    'pages' => 0
);

// Loop through the posts and count the filter types
if ($search_query && $search_query->have_posts()) {
    while ($search_query->have_posts()) {
        $search_query->the_post();
        
        global $post;
        
        if (!$post || !isset($post->post_type)) {
            continue;
        }

        // What's On?
        switch ($post->post_type) {
            case 'whatson':
                // Use function_exists check for ACF functions
                if (function_exists('get_field')) {
                    $type = get_field('type');
                    if ($type == 'exhibition') {
                        $filters['exhibitions'] += 1;
                    } elseif ($type == 'event') {
                        $filters['events'] += 1;
                    }
                }
                break;
                
            case 'product':
                $filters['courses'] += 1;
                break;
                
            case 'post':
                $filters['news'] += 1;
                break;
                
            default:
                $filters['pages'] += 1;
                break;
        }
    }
}
wp_reset_postdata(); // Use wp_reset_postdata() instead of wp_reset_query()

/**
 * Get PAGINATED posts for search query
 *
 */

// Safely get paged variable
$paged = max(1, (int)get_query_var('paged'));

$args['posts_per_page'] = 8;
$args['paged'] = $paged;

// Create new query for paginated results
$paginated_search_query = new WP_Query($args);

get_header();

?>

<?php
// Check if render function exists
if (function_exists('render')) {
    render('parts/landing/landing-hero.php', [
        "title" => "Search results for " . get_search_query(),
        "img_url" => null,
        "img_alt" => null,
        "colour" => null,
        "bg_colour" => "transparent",
    ]);
}
?>

<div id="main" class="main-content container container--gutters">

    <div class="result-summary-wrapper clearfix">
        <span class="label"><?php echo $total_posts; ?> results</span>
        <?php
        $filters_active = 0;
        foreach ($filters as $filter_count) {
            if (!empty($filter_count)) {
                $filters_active += 1;
            }
        }
        ?>
        <?php if ($filters_active > 1) { ?>
            <ul>
                <?php if (!empty($filters['exhibitions'])) { ?>
                    <li>
                        <span class="count"><?php echo (int)$filters['exhibitions']; ?></span>
                        <span class="tag bg-red">
                            <span class="inner">
                                <?php if (function_exists('get_search_filter_url')) { ?>
                                    <a href="<?php echo esc_url(get_search_filter_url('exhibitions')); ?>">Exhibitions</a>
                                <?php } else { ?>
                                    <span>Exhibitions</span>
                                <?php } ?>
                            </span>
                        </span>
                    </li>
                <?php } ?>
                <?php if (!empty($filters['events'])) { ?>
                    <li>
                        <span class="count"><?php echo (int)$filters['events']; ?></span>
                        <?php if (function_exists('get_search_filter_url')) { ?>
                            <a href="<?php echo esc_url(get_search_filter_url('events')); ?>">
                        <?php } ?>
                            <span class="tag bg-teal">
                                <span class="inner">
                                    events
                                </span>
                            </span>
                        <?php if (function_exists('get_search_filter_url')) { ?>
                            </a>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if (!empty($filters['courses'])) { ?>
                    <li>
                        <span class="count"><?php echo (int)$filters['courses']; ?></span>
                        <?php if (function_exists('get_search_filter_url')) { ?>
                            <a href="<?php echo esc_url(get_search_filter_url('courses')); ?>">
                        <?php } ?>
                            <span class="tag bg-orange">
                                <span class="inner">
                                    courses
                                </span>
                            </span>
                        <?php if (function_exists('get_search_filter_url')) { ?>
                            </a>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if (!empty($filters['news'])) { ?>
                    <li>
                        <span class="count"><?php echo (int)$filters['news']; ?></span>
                        <?php if (function_exists('get_search_filter_url')) { ?>
                            <a href="<?php echo esc_url(get_search_filter_url('news')); ?>">
                        <?php } ?>
                            <span class="tag bg-blue">
                                <span class="inner">
                                    News
                                </span>
                            </span>
                        <?php if (function_exists('get_search_filter_url')) { ?>
                            </a>
                        <?php } ?>
                    </li>
                <?php } ?>
                <?php if (!empty($filters['pages'])) { ?>
                    <li>
                        <span class="count"><?php echo (int)$filters['pages']; ?></span>
                        <?php if (function_exists('get_search_filter_url')) { ?>
                            <a href="<?php echo esc_url(get_search_filter_url('pages')); ?>">
                        <?php } ?>
                            <span class="tag bg-red-alt">
                                <span class="inner">
                                    Pages
                                </span>
                            </span>
                        <?php if (function_exists('get_search_filter_url')) { ?>
                            </a>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>

    <div class="search-grid-wrapper has-bg">
        <div class="grid-wrapper search-grid waypoint">
            <?php if ($paginated_search_query && $paginated_search_query->have_posts()) : ?>
                <?php while ($paginated_search_query->have_posts()) : $paginated_search_query->the_post(); ?>
                    <?php 
                    $template_path = locate_template('parts/related-card.php');
                    if ($template_path) {
                        include($template_path);
                    }
                    ?>
                <?php endwhile;
                wp_reset_postdata(); ?>
            <?php endif; ?>
        </div>

        <?php 
        if (function_exists('paginationLinks') && $paginated_search_query) {
            paginationLinks($paginated_search_query);
        }
        ?>

    </div>
</div>

<?php get_footer(); ?>