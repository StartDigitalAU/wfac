<?php

/**
 * @var array $pages
 */
$pages = $pages ?? [];
?>

<section class="landing-grid news-grid">
    <div class="landing-grid__month">
        <span class="title title--h1"></span>
    </div>
    <div class="container container--gutters">
        <div class="landing-grid__grid news-grid__grid">
            <?php
            if (count($pages)) :
                foreach ($pages as $page) :
                    render('parts/news/grid-card.php', [
                        "img_src" => get_resized_image(get_field('hero_image', $page->ID), 'child_page_tile', $GLOBALS['default_img'] ?? ''),
                        "image_id" => get_field('hero_image', $page->ID),
                        "img_alt" => "",
                        "date" => get_the_date('Ymd', $page->ID),
                        "pre_title" => get_field('type', $page->ID) == 'media_release' ? "Media Release" : "News",
                        "title" => get_the_title($page->ID),
                        "description" => get_the_excerpt($page->ID),
                        "link_text" => "Read more",
                        "link_url" => get_the_permalink($page->ID),
                        "link_target" => "_self",
                    ]);
                endforeach;
            else :
                echo "<span>No items were found.</span>";
            endif;
            ?>
        </div>
    </div>
</section>