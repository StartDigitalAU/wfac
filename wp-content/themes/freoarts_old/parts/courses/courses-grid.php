<?php

/**
 * @var array $pages
 */
$pages = $pages ?? [];
?>

<section class="landing-grid">
    <div class="landing-grid__month">
        <span class="title title--h1"></span>
    </div>
    <div class="container container--gutters">
        <div class="grid--std">
            <?php
            if (count($pages)) :
                foreach ($pages as $page) :
                    $pre_title = '';
                    $taxonomy = 'course_adults_category';
                    if (get_field('is_kids_course', $page->ID)) {
                        $taxonomy = 'course_kids_category';
                    }
                    $terms = get_the_terms($page->ID, $taxonomy);
                    if (!empty($terms)) {
                        foreach ($terms as $term) {
                            $pre_title .= $term->name;
                            break;
                        }
                    }

                    $time_period = get_field('time_period', $page->ID);
                    if ($time_period) {
                        $pre_title .= ' | ' . implode(' | ', $time_period);
                    }

                    $card_image = get_field('hero_image', $page->ID) ?: get_field('info_image', $page->ID);

                    $card = [
                        "img_src" => get_resized_image($card_image, 'grid_card_image_2x') ?: ifne($GLOBALS, 'default_img'),
                        "img_alt" => "",
                        "date" => get_field('start_date', $page->ID),
                        "pre_title" => $pre_title,
                        "title" => get_the_title($page->ID),
                        "description" => "",
                        "link_text" => "Read more",
                        "link_url" => get_the_permalink($page->ID),
                        "link_target" => "_self",
                        'sold_out' => !wc_get_product($page->ID)->is_in_stock(),
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