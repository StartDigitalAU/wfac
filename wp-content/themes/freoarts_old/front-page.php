<?php

global $body_class, $Feed;
$body_class = 'page-front';

get_header();

?>

<h1 class="u-vis-hide">Fremantle Arts Centre</h1>

<?php get_template_part('parts/front-page/hero'); ?>

<div id="main" class="main-content" tabindex="0">
    <div class="container container--gutters">
        <?php get_template_part('parts/front-page/whats-on'); ?>

        <?php
            $image_aside = get_field('content_cards') ?? [];
            if(!empty($image_aside)) {
                foreach($image_aside as $aside) {
                    $link = ifne($aside, 'link');
                    $image_id = ifne($aside, 'image_src');
                    if($image_id != '') {
                        $image_src = get_resized_image($image_id, 's680x400');
                    } else {
                        $image_src = '';
                    }
                    render('parts/front-page/sidexside.php', [
                        "title" => ifne($aside, 'title'),
                        "wysiwyg" => ifne($aside, 'wysiwyg'),
                        "img_src" => $image_src,
                        "img_alt" => ifne($aside, 'image_alt'),
                        "img_position" => ifne($aside, 'image_position') ?? false,
                        "link_url" => ifne($link, 'url'),
                        "link_text" => ifne($link, 'title'),
                        "link_target" => ifne($link, 'target') ?? '_self',
                    ]);
                }
            }
        ?>

        <?php get_template_part('parts/front-page/news'); ?>
    </div>
</div><!-- main -->

<?php get_footer(); ?>