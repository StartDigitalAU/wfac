<?php

/**
 * @var string $img_src
 * @var string $img_alt
 * @var string $date
 * @var string $pre_title
 * @var string $title
 * @var string $description
 * @var string $link_text
 * @var string $link_url
 * @var string $link_target
 * @var string $image_id
 */

$img_src = $img_src ?? '';
$img_alt = $img_alt ?? '';
$date = $date ?? '';
$pre_title = $pre_title ?? '';
$title = $title ?? '';
$description = $description ?? '';
$link_text = $link_text ?? '';
$link_url = $link_url ?? '';
$link_target = $link_target ?? '';
$image_id = $image_id ?? false;

// Format date string
$date_info = date_parse($date);
$date_format = date("D d M", mktime(0, 0, 0, $date_info['month'], $date_info['day'], $date_info['year']));

// Format month
$month = $date_info['month'];
$month_format = date("F", mktime(0, 0, 0, $month, 1));
?>

<article class="grid-card" data-event="<?= $month_format ?>">
    <div class="grid-card__image">
        <span class="grid-card__date"><?= $date_format  ?></span>
        <img src="<?= $img_src ?>" alt="<?= $img_alt ?>" />
    </div>
    <div class="grid-card__content">
        <p class="grid-card__pretitle"><?= $pre_title ?></p>
        <h3 class="grid-card__title title title--h3 title--arrow">
            <a href="<?= $link_url ?>" target="<?= $link_target ?>" class="grid-card__link">
                <span class="text"><?= $title ?></span>
                <sup class="icon"></sup>
            </a>
        </h3>
        <div class="grid-card__description">
            <?= wpautop($description); ?>
        </div>
    </div>
</article>