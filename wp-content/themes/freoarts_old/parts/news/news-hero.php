<?php
    /**
     * @var array $title
     * @var array $meta
     * @var array $meta_extra
     * @var array $img_url
     * @var array $img_alt
     * @var string $colour
     */

     $title = $title ?? '';
     $meta = $meta ?? '';
     $meta_extra = $meta_extra ?? '';
     $img_url = $img_url ?? '';
     $img_alt = $img_alt ?? '';
     $colour = $colour ?? '#a6b1cd';
     
?>
<section class="news-hero" data-hero>
    <div style="background-color: <?= $colour ?>">
        <div class="container container--gutters">
            <div class="news-hero__header ">
                <h1 class="news-hero__title title title--h2"><?= $title ?></h1>
                <p class="news-hero__meta">
                    <span class="title title--h4"><?= $meta ?></span>

                    <?php if(!empty($meta_extra)): ?>
                        <strong><?= $meta_extra ?></strong>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    <?php if(!empty($img_url)) : ?>
        <div class="container container--gutters">
            <div class="news-hero__image">
                <img src="<?= $img_url ?>" alt="<?= $img_alt ?>" />
            </div>
        </div>
    <?php endif; ?>
</section>