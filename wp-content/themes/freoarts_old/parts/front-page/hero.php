<?php
$slides = get_field('hero_slides') ?? [];
if (!empty($slides)) {
?>
    <div class="hero-slider-wrapper" data-hero>
        <div class="hero-slider">
            <?php foreach ($slides as $slide) { ?>
                <?php
                $image_desktop = ifne($slide, 'image_desktop', []);
                $image_mobile = ifne($slide, 'image_mobile', []);
                $video_desktop_url = ifne($slide, 'video_desktop_url');
                $video_mobile_url = ifne($slide, 'video_mobile_url');
                $heading_line_01   = ifne($slide, 'heading_line_01');
                $heading_line_02   = ifne($slide, 'heading_line_02');
                $meta_info         = ifne($slide, 'meta_info');
                $link              = ifne($slide, 'link');
                $background_colour = ifne($slide, 'background_colour', '#C1B4B6');
                $text_colour       = ifne($slide, 'text_colour', '#363740');

                if(!empty($slide['background_colour'])) {
                    $styles = 'background: ' . $background_colour . '; color: ' .$text_colour .';';
                } else {
                    $styles = 'background: #C1B4B6; color: #363740;';
                }
            ?>
                <div class="hero-slider__slide">
                    <div class="hero-slider__header">
                        <div>
                            <?php if ($heading_line_01 != '') { ?>
                                <h2 class="hero-slider__title"><?= $heading_line_01 ?></h2>
                            <?php } ?>
                            <?php if ($heading_line_02 != '') { ?>
                                <p class="hero-slider__timestamp"><?= $heading_line_02 ?></p>
                            <?php } ?>
                            <?php if ($meta_info != '') { ?>
                                <p class="hero-slider__meta"><?= $meta_info ?></p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="hero-slider__image">
                        <?php
                            if(empty($video_desktop_url)) {
                        ?>
                            <picture>
                                <source media="(min-width: 480px)" srcset="<?= get_resized_image(ifne($image_desktop, 'ID'), 'front_page_hero') ?>"></source>
                                <?php if(!empty($image_mobile)) { ?>
                                    <source media="(max-width: 479px)" srcset="<?= get_resized_image(ifne($image_mobile, 'ID'), 'front_page_hero_mobile') ?>"></source>
                                <?php } ?>
                                <img src="<?= get_resized_image(ifne($image_desktop, 'ID'), 'front_page_hero') ?>" alt="" />
                            </picture>
                        <?php } else { ?>
                            <video
                                data-desktop="<?= $video_desktop_url ?>"
                                data-mobile="<?= $video_mobile_url ?>"
                                src="<?= $video_desktop_url ?>"
                                class="hero-slider__video"
                                preload="none"
                                playsinline
                                muted
                                autoplay
                                loop
                            >
                            </video>
                        <?php } ?>
                    </div>
                    <?php if ($link != '') { ?>
                        <a class="hero-slider__link" href="<?= $link ?>"><span class="u-vis-hide">Read more about <?= $heading_line_01 ?></span></a>
                    <?php } ?>
                </div>
            <?php } ?>
            <nav class="hero-slider__nav slider-nav clearfix"></nav>
        </div>

        <a href="#main" type="button" class="hero-slider__button">
            <span class="text-acumin text">Scroll</span>
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 409.01 421.05" width="20" height="20" fill="white"><path d="M409.01 181.48h-297.8L251.6 41.08 210.53 0 0 210.53l210.53 210.52 41.07-41.07-140.4-140.41h297.81z"/></svg>
        </a>
    </div>
<?php } ?>