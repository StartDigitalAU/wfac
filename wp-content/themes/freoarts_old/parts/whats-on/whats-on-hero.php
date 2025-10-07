<?php

/**
 * @var array $title
 * @var array $meta
 * @var array $calendar_url
 * @var array $slides
 */

$title = $title ?? '';
$meta = $meta ?? '';
$meta_extra = $meta_extra ?? '';
$calendar_url = $calendar_url ?? '';
$slides = $slides ?? [];
$colour = $colour ?? '#ffffff';
$bg_colour = $bg_colour ?? '#27282d';
?>
<section class="whats-on-hero" data-hero style="background-color: <?= $bg_colour ?>; color: <?= $colour ?>">
    <div class="whats-on-hero__header">
        <div class="container container--gutters">
            <h1 class="whats-on-hero__title title title--h2"><?= $title ?></h1>
            <p class="whats-on-hero__meta">
                <span class="title title--h4"><?= $meta ?></span>
                <?php if (!empty($meta_extra)) : ?>
                    <strong><?= $meta_extra ?></strong>
                <?php endif; ?>
            </p>
        </div>
    </div>
    <div class="whats-on-hero__slider-wrap">
        <?php
        count($slides) > 1 ? $classes = 'whats-on-hero__slider is-active container' : $classes = 'whats-on-hero__slider container';
        ?>
        <div class="<?= $classes ?>">
            <?php foreach ($slides as $slide) {
                /**
                 * @var string $img_src
                 * @var string $img_alt
                 */
                $image_id = ifne($slide, 'image');
                //  whats_on_hero_2x
                $img_alt = $slide['caption'] ?? '';
            ?>
                <div class="whats-on-hero__slide has-bg">
                    <?php
                    if ($image_id) :
                        $img_src = get_resized_image($image_id, 'whats_on_hero');
                        $img_src_2x = get_resized_image($image_id, 'whats_on_hero_2x');
                    ?>
                        <picture>
                            <source srcset="<?= $img_src_2x; ?>" media="(min-width: 1681px)" />
                            <source srcset="<?= $img_src  ?>, <?= $img_src_2x  ?> 2x" media="(max-width: 1680px)" />
                            <img src="<?= $img_src_2x  ?>" alt="<?= $img_alt ?>" />
                        </picture>
                    <?php endif; ?>




                    <canvas class="canvas" width="1" height="1"></canvas>
                    <div class="whats-on-hero__caption">
                        <div class="container container--gutters">
                            <p><?= $img_alt ?></p>
                        </div>
                    </div>
                    <a href="<?= $img_src  ?>" class="whats-on-hero__fullscreen" data-group="gallery" data-modaal-desc="<?= $img_alt ?>">
                        <span class="u-vis-hide">Fullscreen</span>
                        <span class="icon"></span>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="whats-on-hero__controls">

    </div>
</section>