<?php

/**
 * @var string $image_id
 * @var string $title
 */

$image_id = $image_id ?? false;
$title = $title ?? false;

$bg_colour = $bg_colour ?? false;
$colour = $colour ?? false;

$styles = '';

if ($bg_colour) :
    $styles .= "--banner-background:" . $bg_colour . ';';
endif;

if ($colour) :
    $styles .= "--banner-color:" . $colour . ';';
endif;
?>

<div class="banner--course-single" <?= $styles ? ' style="' . $styles . '"' : ''; ?> data-hero>
    <div class="container container--gutters">
        <div class="banner__inner">
            <div class="banner__content">
                <?php if ($title) : ?>
                    <h1 class="banner__title"><?= $title; ?></h1>
                <?php endif; ?>

                <p class="banner__meta">
                    <?php if (!empty($terms)) : ?>
                        <?php foreach ($terms as $term) : ?>
                            <span>
                                <a href="<?php eu($GLOBALS['site_url'] . '/learn/?cat=' . $term->term_id) ?>"><?php echo $term->name; ?></a>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <span>
                        Courses
                    </span>
                </p>
            </div>
        </div>
    </div>
    <?php if ($image_id) : ?>
        <div class="banner__image-wrap">
            <div class="container container--gutters">
                <div class="banner__image">
                    <picture>
                        <source srcset="<?= wp_get_attachment_image_url($image_id, 'c1100x520', false); ?>, <?= wp_get_attachment_image_url($image_id, 'c1100x520@2x', false); ?> 2x" media="(min-width: 481px)" />
                        <source srcset="<?= wp_get_attachment_image_url($image_id, 'c480x227', false); ?>, <?= wp_get_attachment_image_url($image_id, 'c480x227@2x', false); ?> 2x" media="(max-width: 480px)" />
                        <img src="<?= wp_get_attachment_image_url($image_id, 'c1100x520@2x', false); ?>" width="1100" height="520" alt="<?= get_post_meta($image_id, '_wp_attachment_image_alt', true); ?>" />
                    </picture>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>