<?php

/**
 * Template Name: WooCommerce - Page
 *
 */

global $body_class;
$body_class = 'page-admin page-woo';

get_header();

?>
<?php if ( have_posts() ): while ( have_posts() ): the_post(); ?>

<?php
    render('parts/landing/landing-hero.php', [
        "title" => get_the_title(),
        "img_url" => null,
        "img_alt" => null,
        "colour" => null,
        "bg_colour" => "transparent",
    ]);
?>

<div id="main" class="main-content container container--gutters">
    <?php the_content(); ?>
</div>

<?php /*
    <div class="banner-wrapper">
        <div class="container">
            <h1 class="page-title"><?php the_title(); ?></h1>
        </div>
    </div>

    <div class="content">
        <div class="container">

            <?php the_content(); ?>

        </div>
    </div>

    <div class="colour-strip">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
    </div>

</div>
*/ ?>

<?php endwhile; endif; ?>

<?php get_footer(); ?>