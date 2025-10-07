<?php

/**
 * @var string $title_text
 * @var string $title_url
 * @var array $events
 */

$title_text = $title_text ?? '';
$title_url = $title_url ?? '';
$events = $events ?? [];

// $title_lowercase = strtolower($title_text);
if (!isset($GLOBALS['slider_index'])) :
    $GLOBALS['slider_index'] = 1;
endif;

$slick_args = [
    'appendDots' => "#slider-pagination--" . ifne($GLOBALS, 'slider_index'),
    'appendArrows' => "#slider-pagination--" . ifne($GLOBALS, 'slider_index'),
];
//

?>

<section class="whats-on__row step-in">
    <div class="whats-on__heading">
        <h3 class="title title--h1 title--arrow">
            <a href="<?= $title_url ?>">
                <span class="text"><?= $title_text ?></span>
                <sup class="icon"></sup>
            </a>
        </h3>
        <div class="whats-on__slick-pagination" id="slider-pagination--<?= ifne($GLOBALS, 'slider_index'); ?>"></div>
    </div>

    <div class="whats-on__slick" data-slick='<?= json_encode($slick_args, JSON_HEX_QUOT | JSON_HEX_TAG); ?>'>
        <?php
        foreach ($events as $event) :
            $event_id = $event->ID;
            $image = get_field('card_image', $event_id) ?: get_field('hero_image', $event_id);

            $type = get_field('type', $event);
            $related_tags = get_field('related_tags', $event);

            $tags = [];

            if (!empty($related_tags)) {
                // Set it to only get the first 3 related tags (if there are more than 3)
                $related_tags = array_slice($related_tags, 0, 3);

                foreach ($related_tags as $tag) {
                    $term = get_term_by('term_id', $tag, 'related_tag');
                    $tags[] = ucwords($term->name);
                }
                $pretitle = implode(" / ", $tags);
            } else {
                $pretitle = ucwords($type);
            }
        ?>
            <div class="col">
                <?php
                render('parts/front-page/card-whats-on.php', [
                    'img_src' => get_resized_image($image, 'grid_card_image_2x') ?: ifne($GLOBALS, 'default_img'),
                    'img_alt' => $image ? get_post_meta($image, '_wp_attachment_image_alt', TRUE) : false,
                    'start_date' => get_field('start_date', $event_id),
                    'end_date' => get_field('end_date', $event_id),
                    'pretitle' => $pretitle,
                    'title' => get_the_title($event_id),
                    'link_url' => get_the_permalink($event_id),
                    'link_title' => "Read more",
                    'link_target' => "_self",
                    'in_stock' => get_post_type($event_id) == 'product' ? wc_get_product($event_id)->is_in_stock() : true,
                ]);
                ?>
            </div>
        <?php endforeach; ?>
        <div class="col col-last">
            <div class="col-last__inner">
                <h3 class="title title--h1 title--arrow">
                    <span class="title__more">More</span>
                    <a href="<?= $title_url ?>">
                        <span class="text"><?= $title_text ?></span>
                        <sup class="icon"></sup>
                    </a>
                </h3>
            </div>
        </div>
    </div>
</section>
<?php
$GLOBALS['slider_index']++;
?>