<?php

/**
 * Template Name: Our People
 *
 */

global $body_class;
$body_class = 'page-internal page-internal--team';

$fields = get_fields($post->ID);

get_header();

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

                    <?php if(!empty($fields['introduction'])) : ?>
                        <div class="is-editable">
                            <?php echo $fields['introduction']; ?>
                        </div>
                    <?php endif ?>
                    
                    <?php
                        $display_team_heading = get_field('display_team_heading') ?? false;
                        $team_heading = get_field('team_heading') ? get_field('team_heading') : 'Leadership Team';
                        if($display_team_heading) {
                            echo '<h2 class="sub-title">' . $team_heading . '</h2>';
                        }
                    ?>

                    <?php if (isset($fields['board']) && !empty($fields['board'])) { ?>
                        <?php foreach ($fields['board'] as $item) { ?>
                            <?php $image = get_resized_image($item['image'], 'staff_photo'); ?>
                            <div class="card person has-bg">
                                <div class="img-wrapper" style="background-image: url(<?php echo $image; ?>)"></div>
                                <div class="content">
                                    <h3 class="title">
                                        <span class="u-color"><?php echo ifne($item, 'role'); ?> </span>
                                        <?php echo ifne($item, 'name'); ?>
                                    </h3>
                                    <div class="excerpt"><p><?php echo ifne($item, 'excerpt'); ?></p></div>
                                    <div class="expand">
                                        <?php echo ifne($item, 'expanded_content'); ?>
                                    </div>
                                    <span class="faux-link u-color" title="Read more about <?php echo ifne($card, 'name'); ?>">Read More</span>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>

                <?php endwhile; ?>
            <?php endif; ?>

            <h2 class="sub-title">Staff</h2>

            <?php if (isset($fields['staff']) && !empty($fields['staff'])) { ?>
                <?php foreach ($fields['staff'] as $item) { ?>
                    <h3 class="staff-title u-color"><?php echo ifne($item, 'section_name'); ?></h3>
                    <?php if (isset($item['staff']) && !empty($item['staff'])) { ?>
                        <ul class="staff-list">
                            <?php foreach ($item['staff'] as $staff) { ?>
                                <li><?php echo ifne($staff, 'name'); ?></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>

<?php /*
<div id="main" class="main-content">

    <div class="about-content-wrapper">
        <div class="container">

            <?php get_template_part('parts/page-sidebar'); ?>

            <section class="content-outer has-bg">

                <?php if (have_posts()) : ?>
                    <?php while (have_posts()) : the_post(); ?>

                        <header>
                            <span class="tag u-color"><?php echo get_the_title($post->post_parent); ?></span>
                            <h1 class="page-title"><?php the_title(); ?></h1>
                        </header>

                        <div class="is-editable">
                            <?php echo $fields['introduction']; ?>
                        </div>
                        
                        <?php
                        $display_team_heading = get_field('display_team_heading') ?? false;
                        $team_heading = get_field('team_heading') ? get_field('team_heading') : 'Leadership Team';
                        if($display_team_heading) {
                            echo '<h2 class="sub-title">' . $team_heading . '</h2>';
                        }
                        ?>

                        <?php if (isset($fields['board']) && !empty($fields['board'])) { ?>
                            <?php foreach ($fields['board'] as $item) { ?>
                                <?php $image = get_resized_image($item['image'], 'staff_photo'); ?>
                                <div class="card person has-bg">
                                    <div class="img-wrapper" style="background-image: url(<?php echo $image; ?>)"></div>
                                    <div class="content">
                                        <h3 class="title">
                                            <span class="u-color"><?php echo ifne($item, 'role'); ?> </span>
                                            <?php echo ifne($item, 'name'); ?>
                                        </h3>
                                        <div class="excerpt"><p><?php echo ifne($item, 'excerpt'); ?></p></div>
                                        <div class="expand">
                                            <?php echo ifne($item, 'expanded_content'); ?>
                                        </div>
                                        <span class="faux-link u-color" title="Read more about <?php echo ifne($card, 'name'); ?>">Read More</span>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>

                    <?php endwhile; ?>
                <?php endif; ?>

                <h2 class="sub-title">Staff</h2>

                <?php if (isset($fields['staff']) && !empty($fields['staff'])) { ?>
                    <?php foreach ($fields['staff'] as $item) { ?>
                        <h3 class="staff-title u-color"><?php echo ifne($item, 'section_name'); ?></h3>
                        <?php if (isset($item['staff']) && !empty($item['staff'])) { ?>
                            <ul class="staff-list">
                                <?php foreach ($item['staff'] as $staff) { ?>
                                    <li><?php echo ifne($staff, 'name'); ?></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>

            </section>

        </div>
    </div>

</div>
*/ ?>

<?php get_footer(); ?>