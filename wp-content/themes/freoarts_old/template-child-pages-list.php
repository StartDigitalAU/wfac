<?php

/**
 * Template Name: Child Pages List
 *
 */

global $body_class;
$body_class = 'landing';
// $body_class = 'page-about page-about-landing has-red';

$page_title = get_field('menu_heading') ?? '';
if($page_title == '') {
    $page_title = get_the_title();
}
get_header();

?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

        <?php
            $hero_image = get_resized_image(get_field('hero_image'), 's1100x540');
            $img_alt = get_post_meta($hero_image, '_wp_attachment_image_alt', TRUE);
            $header_background_colour = get_field('header_background_colour') ?? '';
            $header_text_colour = get_field('header_text_colour') ?? '';
            render('parts/landing/landing-hero.php', [
                "title" => $page_title,
                "img_url" => $hero_image,
                "img_alt" => $img_alt,
                "colour" => $header_text_colour,
                "bg_colour" => $header_background_colour,
            ]);
        ?>

        <div id="main" class="main-content">
            <?php
                render('parts/landing/landing-intro.php', [
                    "title" => get_field('intro_headline') ?? '',
                    "column_1" => get_field('intro_column_one') != '' ? '<p><strong>' . get_field('intro_column_one') . '</strong></p>' : '',
                    "column_2" => get_field('intro_column_one') != '' ? '<p>' . get_field('intro_column_one') . '</p>' : '',
                ]);
            ?>

            <?php
                $args = array(
                    'sort_order' => 'asc',
                    'sort_column' => 'menu_order',
                    'parent' => $post->ID,
                    'hierarchical' => true,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                );

                $pages = get_pages($args);
                render('parts/landing/landing-grid.php', ["pages" => $pages]);
            ?>
        </div>

    <?php endwhile; ?>
<?php endif; ?>

<?php /* old markup
<div id="main" class="main-content">

    <div class="banner-wrapper">
        <div class="container">
            <h1 class="page-title">
                <span class="u-color"><?php echo get_the_title($post->ID); ?></span> Fremantle arts centre
            </h1>
        </div>
    </div>

    <div class="about-grid-wrapper has-bg">

        <div class="container">

            <h2 class="u-vis-hide">Links within the About section</h2>
            
            <div class="grid-wrapper waypoint">

                <?php
                $args = array(
                    'sort_order' => 'asc',
                    'sort_column' => 'menu_order',
                    'child_of' => $post->ID,
                    'post_type' => 'page',
                    'post_status' => 'publish'
                );

                $pages = get_pages($args);
                ?>
                <?php foreach ($pages as $page) { ?>
                    <div class="col step-in">
                        <a href="<?php echo get_the_permalink($page->ID); ?>" class="card about-tile" title="Read more about <?php echo get_the_title($page->ID); ?>">
                            <article>
                                <?php $hero_image = get_resized_image(get_field('hero_image', $page->ID), 'child_page_tile', $GLOBALS['default_img']); ?>
                                <div class="img-wrapper" style="background-image: url(<?php echo $hero_image; ?>)"></div>
                                <span class="tag"><?php echo get_the_title($page->ID); ?></span>
                            </article>
                        </a>
                    </div>
                <?php } ?>

            </div><!-- grid wrapper -->
        </div>

    </div>
</div>
*/?>



<?php get_footer(); ?>