<?php

/**
 * @var array $title
 * @var array $terms
 * @var array $img_url
 * @var array $img_alt
 * @var array $tutor
 * @var array $difficulties
 * @var string $colour
 * @var string $colour
 */

$title = $title ?? '';
$terms = $terms ?? '';
$img_url = $img_url ?? '';
$img_alt = $img_alt ?? '';
$tutor = $tutor ?? '';
$difficulties = $difficulties ?? '';
$colour = $colour ?? '#ffffff';
$bg_colour = $bg_colour ?? '#27282d';

?>
<section class="courses-hero" data-hero style="background-color: <?= $bg_colour ?>; color: <?= $colour ?>">
    <div class="container container--gutters">
        <div class="courses-hero__header">
            <h1 class="courses-hero__title title title--h2"><?= $title ?></h1>


            <p class="courses-hero__meta">
                <?php if (!empty($terms)) : ?>
                    <span class="title title--h4">
                        <?php foreach ($terms as $term) : ?>
                            <a href="<?php eu($GLOBALS['site_url'] . '/learn/?cat=' . $term->term_id) ?>"><?php echo $term->name; ?> / </a>
                        <?php endforeach; ?>
                    </span>
                <?php endif; ?>
                <strong>
                    Courses
                    <?php if (!empty($tutor)) : ?>
                </strong>

                <span class="title title--h4"><?php echo ' / ' ?></span>

                <strong>
                    <span class="instructor"><?php echo $tutor->post_title; ?></span>
                <?php endif; ?>
                </strong>

                <span class="title title--h4"><?php echo ' / ' ?></span>

                <strong>
                    <?php
                    foreach ($difficulties as $difficulty) :
                        echo '<span class="pill">' . $difficulty . '</span>';
                    endforeach;
                    ?>
                </strong>
            </p>
        </div>
    </div>
    <?php if (!empty($img_url)) : ?>
        <div class="courses-hero__image">
            <img src="<?= $img_url ?>" alt="<?= $img_alt ?>" />
        </div>
    <?php endif; ?>
</section>