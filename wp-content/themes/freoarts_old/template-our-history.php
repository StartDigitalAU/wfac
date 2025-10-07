<?php

/**
 * Template Name: Our History
 *
 */

global $body_class;
$body_class = 'page-internal page-internal--history';

$fields = get_fields($post->ID);

get_header();

$colors = array(
    'green',
    'yellow',
    'blue',
    'pink'
);

?>
<?php
$page_title = get_field('menu_heading') ?? '';
if($page_title == '') {
    $page_title = get_the_title();
}
$hero_image = get_resized_image(get_field('hero_image'), 's1100x540');
$img_alt = get_post_meta($hero_image, '_wp_attachment_image_alt', TRUE);
$header_background_colour = get_field('header_background_colour') ?? '';
$header_text_colour = get_field('header_text_colour') ?? '';
render('parts/landing/landing-hero.php', [
    "title" => $page_title,
    "img_url" => $hero_image,
    "img_alt" => $img_alt,
    "colour" => $header_text_colour,
    "bg_colour" => $header_background_colour,
]);
?>

<?php
    if(!empty($post->post_parent)){
        $sidebar = 'has-sidebar';
    } else {
        $sidebar = 'no-sidebar';
    }
?>

<div id="main" class="main-content container container--gutters <?= $sidebar ?>">
    <?php get_template_part('parts/page-sidebar'); ?>

    <div class="content-wrapper about-content-wrapper waypoint">
        <div class="content-outer">

            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>

                    <?php /*
                    <header>
                        <span class="tag u-color"><?php echo get_the_title($post->post_parent); ?></span>
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </header>
                    */ ?>

                    <?php if (!empty(ifne($fields, 'introduction'))) { ?>
                        <div class="is-editable">
                            <p>
                                <strong><?php echo ifne($fields, 'introduction'); ?></strong>
                            <p>
                        </div>
                    <?php } ?>

                    <?php $i = 0; ?>
                    <?php if (isset($fields['history_cards']) && !empty($fields['history_cards'])) { ?>
                        <?php foreach ($fields['history_cards'] as $card) { ?>
                            <?php $image = get_resized_image($card['image'], 'timeline_item'); ?>
                            <div class="card history step-in">
                                <div class="card-upper">
                                    <span class="pretitle"><?php echo ifne($card, 'date'); ?></span>
                                    <h2 class="title"><?php echo ifne($card, 'title'); ?></h2>
                                    <?php if (!empty($image)) { ?>
                                        <img src="<?php echo $image; ?>" alt="image for <?= ifne($card, 'title'); ?>">
                                    <?php } ?>
                                </div>
                                <div class="card-content is-editable">
                                    <?php echo ifne($card, 'summary'); ?>
                                </div>
                            </div>
                            <?php
                            $i++;
                            if ($i >= count($colors)) {
                                $i = 0;
                            }
                            ?>
                        <?php } ?>
                    <?php } ?>

                <?php endwhile; ?>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php get_footer(); ?>