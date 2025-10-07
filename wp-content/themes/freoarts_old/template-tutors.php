<?php

/**
 * Template Name: Tutors
 *
 */

global $body_class;
$body_class = 'page-about page-team page-internal';

$fields = get_fields($post->ID);

get_header();

?>

<?php
    $page_title = get_field('menu_heading') ?? '';
    if($page_title == '') {
        $page_title = get_the_title();
    }
    $header_background_colour = get_field('header_background_colour') ?? '';
    $header_text_colour = get_field('header_text_colour') ?? '';
    render('parts/landing/landing-hero.php', [
        "title" => $page_title,
        "img_url" => null,
        "img_alt" => null,
        "colour" => $header_text_colour,
        "bg_colour" => $header_background_colour,
    ]);
?>

<?php
    if(!empty($post->post_parent)){
        $sidebar = 'has-sidebar';
    } else {
        $sidebar = 'no-sidebar';
    }
?>

<div id="main" class="main-content container container--gutters <?= $sidebar ?>">
    <?php get_template_part('parts/page-sidebar'); ?>

    <div class="content-wrapper about-content-wrapper waypoint">
        <div class="content-outer">

            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php
                    $tutor_query = new WP_Query(array(
                        'post_type' => 'tutor',
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'orderby' => 'title',
                        'order' => 'ASC'
                    ));
                    ?>
                    <?php if ($tutor_query->have_posts()) { ?>
                        <?php while ($tutor_query->have_posts()) { ?>
                            <?php
                            $tutor_query->the_post();

                            $fields = get_fields(get_the_ID());
                            ?>
                            <?php $image = get_resized_image($fields['card_image'], 'staff_photo', $GLOBALS['default_img']); ?>
                            <div class="card person has-bg">
                                <div class="img-wrapper" style="background-image: url(<?php echo $image; ?>)"></div>
                                <div class="content">
                                    <h2 class="title">
                                        <span class="u-color"><?php echo get_the_title(); ?></span>
                                        <?php echo ifne($fields, 'name'); ?>
                                    </h2>
                                    <div class="excerpt">
                                        <p><?php echo ifne($fields, 'summary'); ?></p>
                                        <a href="<?php echo get_the_permalink(get_the_ID()); ?>" title="Read more about <?php echo get_the_title(); ?>" class="u-color">Read More</a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php wp_reset_postdata(); ?>

                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>