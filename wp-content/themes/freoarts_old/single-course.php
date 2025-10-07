<?php

global $body_class;
$body_class = 'page-whatson page-courses-single is-course';

global $post;

$fields = $GLOBALS['page_fields'];

get_header();

$disabled = $GLOBALS['theme_options']['disable_course_enrolments'];

$_product = wc_get_product($post->ID);


$type = 'adults';
$taxonomy = 'course_adults_category';
if ($fields['is_kids_course']) {
    $type = 'kids';
    $taxonomy = 'course_kids_category';
}

$tutor = null;
if (isset($fields['tutor']) && !empty($fields['tutor'])) {
    $tutor = get_post($fields['tutor']);
}
$hero_image = get_resized_image($fields['info_image'], 'course');

$price = (float)$_product->get_price();
?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

        <?php

        render('parts/banners/banner--course-single.php', [
            'bg_colour' => get_field('header_background_colour') ?? '',
            'colour' => get_field('header_text_colour') ?? '',

            'image_id' => get_field('info_image'),
            "title" => get_the_title(),
            "terms" => get_the_terms(get_the_ID(), $taxonomy),
            'bg_colour' => get_field('header_background_colour'),
            'colour' => get_field('header_text_colour'),
        ]);

        // render('parts/whats-on/courses-hero.php', [
        //     "title" => get_the_title(),
        //     "img_url" => $hero_image,
        //     "img_alt" => "",
        //     "terms" => get_the_terms(get_the_ID(), $taxonomy),
        //     "tutor" => $tutor,
        //     "difficulties" => $fields['difficulty'],
        //     'bg_colour' => get_field('header_background_colour') ?? '',
        //     'colour' => get_field('header_text_colour') ?? ''
        // ]);

        include $GLOBALS['template_path'] . '/parts/courses/header-single.php';
        ?>

        <div id="main" class="single main-content container container--gutters">
            <div class="single__grid waypoint">

                <div class="single__content">
                    <div class="course-item-content">
                        <h2 class="u-vis-hide"><?php the_title(); ?><?= !empty($tutor) ? ' with ' . $tutor->post_title : ''; ?></h2>

                        <div class="single__mobile-cta">
                            <?php
                            render("parts/courses/cta.php", [
                                'hide_note' => true,
                                'is_released' => validate_release_date($fields),
                                'disabled' => $GLOBALS['theme_options']['disable_course_enrolments'],
                                '_product' => $_product,
                                'product_id' => get_the_ID(),
                                'product_title' => get_the_title(),
                            ]);
                            ?>
                        </div>

                        <div class="is-editable">
                            <?php echo wpautop(get_post_field('post_content')); ?>
                        </div>

                        <?php if (!empty($tutor)) { ?>
                            <div class="card person has-bg">
                                <?php $tutor_image = get_resized_image(get_field('card_image', $tutor->ID), 'tutor_card'); ?>
                                <div class="img-wrapper" style="background-image: url(<?php echo $tutor_image; ?>)"></div>
                                <div class="content">
                                    <h3 class="title"><span class="u-color">Tutor</span> <?php echo $tutor->post_title; ?></h3>
                                    <p><?php echo get_field('summary', $tutor->ID); ?></p>
                                    <a href="<?php echo get_the_permalink($tutor->ID); ?>" title="Read more about <?php echo $tutor->post_title; ?>" class="u-color faux-link">Read More</a>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        render("parts/courses/social.php", [
                            'classes' => ['hide-xmd'],
                        ]);
                        ?>
                    </div>
                </div>

                <aside class="single__share social-links" data-url="<?php echo get_the_permalink(); ?>" data-title="<?php echo get_the_title() ?>" data-image-url="<?php echo $hero_image ?>">

                    <?php
                    render('parts/courses/meta.php', [
                        'duration' => get_field('duration'),
                        'date' => get_formatted_datetime($fields),
                        'tutor' => $tutor,
                        'difficulties' => get_field('difficulty'),
                        'price' => $price,
                        'is_adult_type' => $type == 'adults',
                        'discount_price' => get_discount_price($price),
                        'additional_fields' => get_field('additional_fields'),
                        'cta' => [
                            'hide_note' => false,
                            'is_released' => validate_release_date($fields),
                            'disabled' => $GLOBALS['theme_options']['disable_course_enrolments'],
                            '_product' => $_product,
                            'product_id' => get_the_ID(),
                            'product_title' => get_the_title(),
                        ],
                    ]);
                    ?>

                    <?php
                    render("parts/courses/social.php", [
                        'classes' => ['show-xmd'],
                    ]);
                    ?>
                </aside>
            </div>
        </div>

    <?php endwhile; ?>
<?php endif; ?>

<?php get_template_part('parts/related-posts'); ?>

<?php get_footer(); ?>