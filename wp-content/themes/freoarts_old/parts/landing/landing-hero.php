<?php
    /**
     * @var array $title
     * @var array $img_url
     * @var array $img_alt
     * @var string $colour
     * @var string $bg_colour
     */

     $title = $title ?? '';
     $img_url = $img_url ?? '';
     $img_alt = $img_alt ?? '';
     $colour = $colour ?? '#27282d';
     $bg_colour = $bg_colour ?? '#a6b1cd';
     
?>
<section class="landing-hero" data-hero>
    <div style="background-color: <?= $bg_colour ?>; color: <?= $colour ?>">
        <div class="container container--gutters">
            <h1 class="landing-hero__title title title--h2"><?= $title ?></h1>
        </div>
    </div>
    <?php if(!empty($img_url)) : ?>
        <div class="container container--gutters">
            <div class="landing-hero__image">
                    <img src="<?= $img_url ?>" alt="<?= $img_alt ?>" />
            </div>
        </div>
    <?php endif; ?>
</section>