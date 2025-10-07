<?php

/**
 * @var array $pages
 */
$pages = $pages ?? [];
$show_grid_month = $show_grid_month ?? true;
?>

<section class="landing-grid">
    <?php if ($show_grid_month) : ?>
        <div class="landing-grid__month">
            <span class="title title--h1"></span>
        </div>
    <?php endif; ?>
    <div class="container container--gutters">
        <div class="grid--std">
            <?php
            if (count($pages)) :
                foreach ($pages as $page) :
                    $dates = '';
                    $start_date = get_field('start_date', $page->ID) ?? '';
                    $end_date = get_field('end_date', $page->ID) ?? '';
                    if ($start_date != '') {
                        $date = DateTime::createFromFormat('Ymd', $start_date);
                        $dates = $date->format('D j M');
                        if ($end_date != '' && $start_date !== $end_date) {
                            $date = DateTime::createFromFormat('Ymd', $end_date);
                            $dates .= ' &mdash; ' . $date->format('D j M');
                        }
                    }
                    $card_image = get_field('card_image', $page->ID) ?: get_field('hero_image', $page->ID);
                    $card = [
                        "img_src" => get_resized_image($card_image, 'grid_card_image_2x') ?: ifne($GLOBALS, 'default_img'),
                        "img_alt" => "",
                        "date" => get_field('start_date', $page->ID),
                        "date_formatted" => $dates,
                        "pre_title" => get_field('type', $page->ID) == 'exhibition' ? "Exhibition" : "Event",
                        "title" => get_the_title($page->ID),
                        "description" => get_field('summary', $page->ID) ?: get_the_excerpt($page->ID),
                        "link_text" => "Read more",
                        "link_url" => get_the_permalink($page->ID),
                        "link_target" => "_self",
                        "sold_out" => false,
                    ];
                    render('parts/whats-on/grid-card.php', $card);
                endforeach;
            else :
                echo "<span>No items were found.</span>";
            endif;
            ?>
        </div>
    </div>
</section>