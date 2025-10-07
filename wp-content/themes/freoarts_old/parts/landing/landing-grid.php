<?php
    /**
     * @var array $pages
     */
    $pages = $pages ?? [];
?>

<section class="landing-grid">
    <div class="container container--gutters">
        <div class="landing-grid__grid">
            <?php
                foreach($pages as $page) : 
                    $card = [
                        "img_src" => get_resized_image(get_field('hero_image', $page->ID), 'child_page_tile', $GLOBALS['default_img']),
                        "img_alt" => "",
                        "title" => get_the_title($page->ID),
                        "description" => get_the_excerpt($page->ID),
                        "link_text" => "Read more",
                        "link_url" => get_the_permalink($page->ID),
                        "link_target" => "_self",
                    ];
                    render('parts/landing/grid-card.php', $card);
                endforeach;
            ?>
        </div>
    </div>
</section>