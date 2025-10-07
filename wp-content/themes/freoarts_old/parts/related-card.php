<?php

/**
 * @var string $excerpt_length
 */
$excerpt_length = $excerpt_length ?? false;


?>
<?php

$label = 'News';
$class = 'news';

$post_id = get_the_ID();

$card_image = get_field('card_image', $post_id) ?: get_field('hero_image', $post_id);

$post_type = get_post_type($post_id);


if ($post_type == 'post') {

    $label = 'News';
    $class = 'news';
} elseif ($post_type == 'page') {

    $label = 'Page';
    $class = 'about';
} elseif ($post_type == 'product') {

    $label = 'Course';
    // TODO: Filter by course
    $class = 'course';

    $card_image = get_field('hero_image', $post_id) ?: get_field('info_image', $post_id);
} elseif ($post_type == 'whatson') {

    $label = 'Exhibition';
    $class = 'exhibition';

    $type = get_field('type', $post_id);

    if (!empty($type)) {

        if ($type == 'exhibition') {
            $label = 'Exhibition';
            $class = 'exhibition';
        } else {
            $label = 'Event';
            $class = 'event';
        }
    }
}

$hero_image = get_resized_image($card_image, 'grid_card_image_690') ?: ifne($GLOBALS, 'default_img');

$custom_excerpt = get_field('custom_description', $post_id);
$excerpt = wp_trim_words(($custom_excerpt ?: get_the_excerpt($post_id)), ($excerpt_length ?: 20));
?>
<div class="col">
    <article class="card related-card is-<?php echo $class; ?> card-news step-in">
        <div class="card-news__image">
            <?php if ($post_type == 'whatson') { ?>
                <p class="card-whats-on__timestamp">
                    <?php
                    echo get_formatted_datetime_short(array(
                        'start_date' => get_field('start_date', $post_id),
                        'end_date' => get_field('end_date', $post_id)
                    ));
                    ?>
                </p>
            <?php } ?>
            <img src="<?php echo $hero_image; ?>" alt="<?= "Image for" . get_the_title();  ?>">
        </div>

        <span class="card-news__meta">
        </span>
        <p class="card-news__meta"><?php echo $label; ?></p>
        <h3 class="card-news__title"><?php the_title(); ?></h2>
            <p class="card-news__description"><?= $excerpt ?></p>
            <a href="<?php the_permalink(); ?>" class="card-news__link">
                <span class="u-vis-hide">View <?php the_title(); ?></span>
            </a>
    </article>
</div>