<?php

/**
 * Template Name: Shop
 *
 */

global $body_class;
$body_class = 'page-shop is-shop';

$fields = get_fields($post->ID);

get_header();

?>

<div id="main" class="main-content">

    <h1 class="u-vis-hide">Shop</h1>
    
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

    <div class="shop-grid-wrapper">
        <div class="container">
            <h2 class="panel-title">FOUND at Fremantle Arts Centre</h2>

            <div class="content">
                <?php the_content(''); ?>
            </div>

            <div class="grid-wrapper shop-grid has-bg clearfix waypoint">

                <?php if (isset($fields['products']) && !empty($fields['products'])) { ?>
                    <?php foreach ($fields['products'] as $product) { ?>
                        <div class="col step-in">
                            <div class="card shop">
                                <?php $product_image = get_resized_image($product['image'], 'shop_card', $GLOBALS['default_img']); ?>
                                <div class="img-wrapper" style="background-image: url(<?php echo $product_image; ?>)"></div>
                                <h2 class="title"><?php echo ifne($product, 'title'); ?></h2>
                                <div class="content">
                                    <p><?php echo ifne($product, 'summary'); ?></p>
                                    <?php if (!empty($product['link'])) { ?>
                                        <a href="<?php echo get_the_permalink($product['link']); ?>" title="Read more about <?php echo ifne($product, 'title'); ?>">
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

<?php get_footer(); ?>