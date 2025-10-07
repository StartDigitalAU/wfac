<?php

global $body_class;
$body_class = 'page-internal';

get_header();

?>

<?php
$page_title = get_field('menu_heading') ?? '';
if($page_title == '') {
    $page_title = get_the_title();
}
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
                    <div class="is-editable">
                        <?php the_content(); ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php /*
<div id="main" class="main-content">

    <div class="about-content-wrapper">
        <div class="container">

            <?php get_template_part('parts/page-sidebar'); ?>

            <section class="content-outer has-bg">

                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>

                        <header>
                            <?php if ($post->post_parent): ?>
                            <span class="tag u-color"><?php echo get_the_title($post->post_parent); ?></span>
                            <?php endif; ?>
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </header>
                        
                        <h2 class="u-vis-hide">Read more about <?php the_title(); ?></h2>

                        <div class="is-editable">
	                        <?php the_content(); ?>
                        </div>

                    <?php endwhile; ?>
                <?php endif; ?>

            </section>

        </div>
    </div>

</div>
*/ ?>

<?php get_footer(); ?>