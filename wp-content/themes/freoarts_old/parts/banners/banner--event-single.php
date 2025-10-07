<?php

/**
 * @var string $title
 * @var string $meta
 * @var string $meta_extra
 * @var string $calendar_url
 * @var array $slides
 * @var string $colour
 * @var string $bg_colour
 */

$title = $title ?? false;
$meta = $meta ?? false;
$meta_extra = $meta_extra ?? false;
$calendar_url = $calendar_url ?? false;
$slides = $slides ?? [];
$colour = $colour ?? false;
$bg_colour = $bg_colour ?? false;

$styles = '';

if ($bg_colour) :
    $styles .= "--banner-background:" . $bg_colour . ';';
endif;

if ($colour) :
    $styles .= "--banner-color:" . $colour . ';';
endif;


if (!isset($GLOBALS['banner_index'])) :
    $GLOBALS['banner_index'] = 1;
endif;

?>

<section class="banner--event-single banner" <?= $styles ? ' style="' . $styles . '"' : ''; ?> data-hero>
    <div class="container container--gutters">
        <div class="banner__content">
            <h1 class="banner__title"><?= $title ?></h1>
            <p class="banner__meta">
                <span><a href="#"><?= $meta; ?></a></span>
                <?php if (!empty($meta_extra)) : ?>
                    <span><?= $meta_extra; ?></span>
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="banner__image-wrap">
        <div class="container container--gutters">
            <div class="banner__slider-wrap">
                <?php
                $slick_args = [
                    'appendArrows' => "#slider-pagination--" . ifne($GLOBALS, 'banner_index'),
                    'asNavFor' => "#slider--event-single-caption-" . ifne($GLOBALS, 'banner_index'),
                ];
                ?>
                <div class="slider--event-single" data-slick='<?= json_encode($slick_args, JSON_HEX_QUOT | JSON_HEX_TAG); ?>' id="slider--event-single-<?= ifne($GLOBALS, 'banner_index'); ?>">
                    <?php
                    foreach ($slides as $slide) {
                        $image_id = ifne($slide, 'image');
                        $img_alt = ifne($slide, 'caption');
                    ?>
                        <div class="slider__slide">
                            <?php if ($image_id) : ?>
                                <div class="slider__slide-image">
                                    <figure>
                                        <picture>
                                            <source srcset="<?= wp_get_attachment_image_url($image_id, 'c1100x520', false); ?>, <?= wp_get_attachment_image_url($image_id, 'c1100x520@2x', false); ?> 2x" media="(min-width: 481px)" />
                                            <source srcset="<?= wp_get_attachment_image_url($image_id, 'c480x227', false); ?>, <?= wp_get_attachment_image_url($image_id, 'c480x227@2x', false); ?> 2x" media="(max-width: 480px)" />
                                            <img src="<?= wp_get_attachment_image_url($image_id, 'c1100x520@2x', false); ?>" width="1100" height="520" alt="<?= get_post_meta($image_id, '_wp_attachment_image_alt', true); ?>" />
                                        </picture>

                                        <?php if ($img_alt) : ?>
                                            <figcaption class="u-vis-hide"><?= $img_alt; ?></figcaption>
                                    </figure>
                                <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <a href="<?= wp_get_attachment_image_url($image_id, 'c1100x520@2x', false); ?>" class="u-vis-hide banner__fullscreen" data-group="gallery" data-modaal-desc="<?= $img_alt ?>">
                                <span class="u-vis-hide">Fullscreen</span>
                                <span class="icon"></span>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="slider__controls" id="slider-pagination--<?= ifne($GLOBALS, 'banner_index'); ?>">
                <div class="slider__controls-captions">
                    <?php if (!empty($slides)) :
                        $slick_args_2 = [
                            'asNavFor' => "#slider--event-single-" . ifne($GLOBALS, 'banner_index'),
                        ];
                    ?>
                        <div class="slider--event-single-caption" id="slider--event-single-caption-<?= ifne($GLOBALS, 'banner_index'); ?>" aria-hidden="true" data-slick='<?= json_encode($slick_args_2, JSON_HEX_QUOT | JSON_HEX_TAG); ?>'>
                            <?php
                            foreach ($slides as $slide) :
                                $image_id = ifne($slide, 'image');
                                $img_alt = $slide['caption'] ?? '';
                            ?>

                                <div class="slider__slide">
                                    <?php if ($img_alt) : ?>
                                        <p><?= $img_alt; ?></p>
                                    <?php endif; ?>
                                </div>

                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <button class="btn--fullscreen" type="button" data-target="#slider--event-single-<?= ifne($GLOBALS, 'banner_index'); ?>">
                    <span class="u-vis-hide">Fullscreen</span>
                    <span class="icon"></span>
                </button>
            </div>
        </div>
    </div>
</section>
<?php $GLOBALS['banner_index']++; ?>