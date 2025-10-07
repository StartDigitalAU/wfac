<?php

/**
 * @var string $img_src
 * @var string $img_alt
 * @var string $title
 * @var string $description
 * @var string $link_text
 * @var string $link_url
 * @var string $link_target
 */

$img_src = $img_src ?? '';
$img_alt = $img_alt ?? '';
$title = $title ?? '';
$description = $description ?? '';
$link_text = $link_text ?? '';
$link_url = $link_url ?? '';
$link_target = $link_target ?? '';
?>

<article class="grid-card">
    <div class="grid-card__image">
        <img src="<?= $img_src ?>" alt="<?= $img_alt ?>" />
    </div>
    <h3 class="grid-card__title title title--h3 title--arrow">
        <a href="<?= $link_url ?>" target="<?= $link_target ?>" class="grid-card__link">
            <span class="text"><?= $title ?></span>
            <sup class="icon"></sup>
        </a>
    </h3>
    <div class="grid-card__description">
        <?= wpautop($description); ?>
    </div>
</article>