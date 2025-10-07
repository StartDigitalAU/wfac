<?php

/**
 * Template Name: Courses - Listings
 *
 */

global $body_class;
$body_class = 'page-courses page-courses-category is-course';

$fields = $GLOBALS['page_fields'];

get_header();

/**
 * Get the course filter (i.e. kids or adults)
 *
 */

$url = get_permalink($post->ID);

$is_kids_course = 0;
$type = 'adults';
$type_label = 'Adults';

if (strpos($url, 'learn/kids') !== false) {
    $is_kids_course = 1;
    $type = 'kids';
    $type_label = 'Kids';
}

/**
 * Build the base query arguments
 *
 */

$args = array(
    'post_type' => 'product',
    'ignore_sticky_posts' => 1,
    'post_status' => 'publish',
    'tax_query' => array(
        array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'course'
        )
    ),
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => 'is_kids_course',
            'value' => $is_kids_course,
            'type' => 'BOOLEAN',
            'compare' => '=='
        ),
        array(
            'key' => 'start_date',
            'value' => date('Ymd'),
            'type' => 'numeric',
            'compare' => '>='
        )
    ),
    'orderby' => 'meta_value',
    'meta_key'=> 'start_date',
    'order' => 'ASC'
);

/**
 * Filter by categories
 */

$filtered_category = null;
$filtering_by_category = false;

if (isset($_GET['cat']) && !empty($_GET['cat'])) {

    $filtering_by_category = true;
    // $args['cat'] = $_GET['cat'];

    $filtered_category = get_term($_GET['cat']);

    $args['tax_query'][] = array(
        'taxonomy' => 'course_' . $type . '_category',
        'field' => 'term_id',
        'terms' => explode(',', $_GET['cat']),
        'operator' => 'IN'
    );
}

/**
 * Build the Sticky query
 *
 */

if (!$filtering_by_category) {

    $sticky_args = $args;
    $sticky_args['posts_per_page'] = 2;

    $tax_query   = WC()->query->get_tax_query();
    $tax_query[] = array(
        'taxonomy' => 'product_visibility',
        'field'    => 'name',
        'terms'    => 'featured',
        'operator' => 'IN',
    );

    $sticky_args['tax_query'] = $tax_query;

    $sticky_query = new WP_Query($sticky_args);
}

/**
 * Build the Main query
 *
 */

$args['posts_per_page'] = 9;
$args['paged'] = $paged;

if (isset($sticky_query)) {

    $args['post__not_in'] = array();
    foreach ($sticky_query->posts as $sticky_post) {
        $args['post__not_in'][] = $sticky_post->ID;
    }
}

$query = new WP_Query($args);

?>

<div class="sub-header-wrapper">
    <?php get_template_part('parts/courses/menu'); ?>
    <?php get_template_part('parts/courses/calendar'); ?>
</div>

<div id="main" class="main-content">

    <div class="banner-wrapper">
        <div class="container">
            <h1 class="page-title"><?php echo $type_label; ?> <span class="u-color">Courses</span></h1>
            <h2 class="sub-title"><?php echo $type_label; ?> <span class="u-color">Courses</span></h2>
        </div>
    </div>

    <div class="courses-grid-wrapper">

        <div class="container">

            <?php if (isset($sticky_query) && $paged < 2) { ?>
                <?php if ($sticky_query->have_posts()) : ?>
                    <div class="grid-wrapper featured-grid waypoint">
                        <?php while ($sticky_query->have_posts()) : $sticky_query->the_post(); ?>
                            <?php
                            $fields = get_fields();

                            $tutor = get_post($fields['tutor']);

                            $taxonomy = 'course_adults_category';
                            if ($fields['is_kids_course']) {
                                $taxonomy = 'course_kids_category';
                            }

                            // Retrieve hero image
                            $hero_image = get_resized_image($fields['hero_image'], 'course', $GLOBALS['default_img']);
                            ?>
                            <div class="col step-in">
                                <a href="<?php the_permalink(); ?>" class="card course" title="Read more about <?php the_title(); ?>">
                                    <article>
                                        <div class="img-wrapper-outer">
                                            <?php
                                            if ($filtering_by_category) {

                                                if (!empty($filtered_category)) {

                                                    echo '<span class="tag">' . $filtered_category->name . '</span>';
                                                }
                                            }
                                            else {

                                                $terms = get_the_terms(get_the_ID(), $taxonomy);
                                                if (!empty($terms)) {
                                                    foreach ($terms as $term) {
                                                        echo '<span class="tag">' . $term->name . '</span>';
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            <div class="img-wrapper" style="background-image: url(<?php echo $hero_image; ?>)">
                                            </div>
                                        </div>
                                        <div class="content">
                                            <h3 class="title"><?php the_title(); ?></h3>
                                            <div class="course-meta clearfix">
                                                <span class="instructor"><?php echo $tutor->post_title; ?></span>
                                                <?php
                                                $difficulties = $fields['difficulty'];
                                                foreach ($difficulties as $difficulty) {
                                                    echo '<span class="pill">' . $difficulty . '</span>';
                                                }
                                                ?>
                                            </div>
                                            <p><?php echo $fields['summary']; ?></p>
                                            <span class="faux-link">View Course Information</span>
                                        </div>
                                    </article>
                                </a>
                            </div>
                        <?php endwhile; ?>
                        <span class="border"></span>
                    </div><!-- featured grid -->
                <?php endif; ?>
            <?php } ?>

            <?php $product_factory = new WC_Product_Factory(); ?>

            <?php if ($query->have_posts()) : ?>
                <div class="grid-wrapper standard-grid waypoint">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <?php
                        $fields = get_fields();

                        $tutor = get_post($fields['tutor']);

                        $taxonomy = 'course_adults_category';
                        if ($fields['is_kids_course']) {
                            $taxonomy = 'course_kids_category';
                        }

                        // Retrieve hero image
                        $hero_image = get_resized_image($fields['hero_image'], 'course', $GLOBALS['default_img']);
                        $product = $product_factory->get_product(get_the_ID());
                        ?>

                        <div class="col step-in">
                            <a href="<?php the_permalink(); ?>" class="card course<?= $product->is_in_stock() ? "" : " out-of-stock"; ?>" title="Read more about <?php the_title(); ?>">
                                <article>
                                    <div class="img-wrapper-outer">
                                        <?php
                                            if ($filtering_by_category) {

                                                if (!empty($filtered_category)) {

                                                    echo '<span class="tag">' . $filtered_category->name . '</span>';
                                                }
                                            }
                                            else {

                                                $terms = get_the_terms(get_the_ID(), $taxonomy);
                                                if (!empty($terms)) {
                                                    foreach ($terms as $term) {
                                                        echo '<span class="tag">' . $term->name . '</span>';
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <?php if( !$product->is_in_stock() ) { ?> 
                                            <span class="tag out-of-stock">Sold out</span>
                                        <?php } ?>
                                        <div class="img-wrapper" style="background-image: url(<?php echo $hero_image; ?>)">
                                        </div>
                                    </div>
                                    <div class="content">
                                        <h3 class="title"><?php the_title(); ?></h3>
                                        <p><?php echo get_formatted_datetime($fields); ?></p>
                                        <div class="course-meta clearfix">
                                            <span class="instructor"><?php echo $tutor->post_title; ?></span>
                                            <?php
                                            $difficulties = $fields['difficulty'];
                                            foreach ($difficulties as $difficulty) {
                                                echo '<span class="pill">' . $difficulty . '</span>';
                                            }
                                            ?>
                                        </div>
                                        <p><?php echo $fields['summary']; ?></p>
                                        <span class="faux-link">View Course Information</span>
                                    </div>
                                </article>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div><!-- standard grid -->
            <?php else : ?>
                <p>No courses currently available.</p>
            <?php endif; ?>


            <?php paginationLinks($query); ?>

        </div>

    </div>

</div>

<?php get_footer(); ?>