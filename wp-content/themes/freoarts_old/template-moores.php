<?php

/**
 * Template Name: Moores Building
 *
 */

global $body_class;
$body_class = 'page-shop is-shop';

$fields = get_fields($post->ID);

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

<div id="main" class="main-content container container--gutters">

    <div class="content-wrapper waypoint">

        <?php if (isset($fields['gallery']) && !empty($fields['gallery'])) { ?>
            <div class="shop-slider-wrapper">
                <div class="shop-slider">

                    <?php foreach ($fields['gallery'] as $slide) { ?>
                        <div class="slide has-bg">
                            <?php $slide_image = get_resized_image($slide['image'], 'hero_image', $GLOBALS['default_img']); ?>
                            <div class="img-wrapper" style="background-image: url(<?php echo $slide_image; ?>)"></div>
                        </div>
                    <?php } ?>

                    <div class="shop-slider-arrows slider-arrows"></div>
                    <div class="shop-slider-pagination slider-nav"></div>

                </div>
            </div><!-- shop slider -->
        <?php } ?>

        <div class="shop-grid-wrapper waypoint">
        
            <div class="content is-editable">
                <?php the_content(''); ?>
            </div>

            <div class="grid-wrapper shop-grid has-bg clearfix">

                <?php if (isset($fields['exhibitions']) && !empty($fields['exhibitions'])) { ?>
                    <?php foreach ($fields['exhibitions'] as $exhibition) { ?>
                        <div class="col step-in">
                            <div class="card shop">
                                <?php $exhibition_image = get_resized_image($exhibition['image'], 'shop_card', $GLOBALS['default_img']); ?>
                                <div class="img-wrapper" style="background-image: url(<?php echo $exhibition_image; ?>)"></div>
                                <h2 class="title"><?php echo ifne($exhibition, 'title'); ?></h2>
                                <div class="content">
                                    <p><?php echo ifne($exhibition, 'summary'); ?></p>
                                    <?php if (!empty($exhibition['link'])) { ?>
                                        <a href="<?php echo get_the_permalink($exhibition['link']); ?>" title="Read more about <?php echo ifne($exhibition, 'title'); ?>">
                                            <span class="faux-link u-color">Read more</span>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
        </div>

    </div>
</div>

<?php /*
<div id="main" class="main-content">

    <?php if (isset($fields['gallery']) && !empty($fields['gallery'])) { ?>
        <div class="shop-slider-wrapper">
            <div class="shop-slider container">

                <?php foreach ($fields['gallery'] as $slide) { ?>
                    <div class="slide has-bg">
                        <?php $slide_image = get_resized_image($slide['image'], 'hero_image', $GLOBALS['default_img']); ?>
                        <div class="img-wrapper" style="background-image: url(<?php echo $slide_image; ?>)"></div>
                    </div>
                <?php } ?>

                <div class="shop-slider-arrows slider-arrows"></div>
                <div class="shop-slider-pagination slider-nav"></div>

            </div>
        </div><!-- shop slider -->
    <?php } ?>

    <div class="shop-grid-wrapper waypoint">
        <div class="container">
      
            <div class="content is-editable">
                <?php the_content(''); ?>
            </div>

            <div class="grid-wrapper shop-grid has-bg clearfix">

                <?php if (isset($fields['exhibitions']) && !empty($fields['exhibitions'])) { ?>
                    <?php foreach ($fields['exhibitions'] as $exhibition) { ?>
                        <div class="col step-in">
                            <div class="card shop">
                                <?php $exhibition_image = get_resized_image($exhibition['image'], 'shop_card', $GLOBALS['default_img']); ?>
                                <div class="img-wrapper" style="background-image: url(<?php echo $exhibition_image; ?>)"></div>
                                <h2 class="title"><?php echo ifne($exhibition, 'title'); ?></h2>
                                <div class="content">
                                    <p><?php echo ifne($exhibition, 'summary'); ?></p>
                                    <?php if (!empty($exhibition['link'])) { ?>
                                        <a href="<?php echo get_the_permalink($exhibition['link']); ?>" title="Read more about <?php echo ifne($exhibition, 'title'); ?>">
                                            <span class="faux-link u-color">Read more</span>
                                        </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>

            </div>
        </div>
    </div>

</div>
*/?>

<?php get_footer(); ?>