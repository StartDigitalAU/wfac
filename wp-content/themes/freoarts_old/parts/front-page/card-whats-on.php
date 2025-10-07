<?php

/**
 * @var string $img_src
 * @var string $img_alt
 * @var string $start_date
 * @var string $end_date
 * @var string $pretitle
 * @var string $title
 * @var string $link_url
 * @var string $link_title
 * @var string $link_target
 */

$img_src = $img_src ?? '';
$img_alt = $img_alt ?? '';
$pretitle = $pretitle ?? '';
$title = $title ?? '';
$link_url = $link_url ?? '';
$link_text = $link_text ?? '';
$link_target = $link_target ?? '';
$start_date = $start_date ?? false;
$end_date = $end_date ?? false;
$in_stock = $in_stock ?? false;

if ($start_date) {
    $date = DateTime::createFromFormat('Ymd', $start_date);
    $timestamp = $date->format('D j M');

    if ($end_date && $start_date !== $end_date) {
        $date = DateTime::createFromFormat('Ymd', $end_date);
        $timestamp .= ' &mdash; ' . $date->format('D j M');
    }
}
?>

<article class="card-whats-on step-in">
    <div class="card-whats-on__image">
        <p class="card-whats-on__timestamp" data-instock="<?= $in_stock ? 'true' : 'false'; ?>"><?= $in_stock ? $timestamp : 'Sold Out'; ?></p>
        <img src="<?= $img_src ?>" alt="<? $img_alt ?>" />
    </div>
    <div class="card-whats-on__content">
        <p class="card-whats-on__pretitle"><?= $pretitle ?></p>
        <h3 class="card-whats-on__title">
            <span class="text"><?= $title ?></span>
            <span class="icon"></span>
        </h3>
    </div>
    <a href="<?= $link_url ?>" target="<?= $link_target ?>" class="card-whats-on__link">
        <span class="u-vis-hide"><?= $link_text ?></span>
    </a>
</article>