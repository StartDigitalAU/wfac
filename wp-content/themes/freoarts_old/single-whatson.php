<?php

global $post;

$type = get_field('type', $post->ID);

if ($type == 'exhibition') {
    $type_plural = 'exhibitions';
} else {
    $type_plural = 'events';
}

global $body_class;
$body_class = 'page-whatson page-whatson-single is-' . $type;

$fields = $GLOBALS['page_fields'];

include($GLOBALS['template_path'] . '/parts/whats-on/cal.php');

get_header();

?>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>
        <?php
        if (isset($fields['gallery']) && !empty($fields['gallery']) || !empty($fields['hero_image'])) {
            $hero_meta = ucfirst($type);
            $hero_extra = 'runs from ' . get_formatted_datetime($fields);
            $hero_calendar = get_the_permalink();

            // echo get_field('hero_image');
            render('parts/banners/banner--event-single.php', [
                "title" => get_the_title(),
                "meta" => $hero_meta,
                "meta_extra" => $hero_extra,
                "calendar_url" => $hero_calendar . "?_cal=1",
                "slides" =>  !empty(get_field('gallery')) ? get_field('gallery') : [['image' => get_field('hero_image'), 'image_alt' => get_post_meta(get_field('hero_image'), '_wp_attachment_image_alt', TRUE)]],
                'bg_colour' => get_field('header_background_colour'),
                'colour' => get_field('header_text_colour')
            ]);

            // render('parts/whats-on/whats-on-hero.php', [
            //     "title" => get_the_title(),
            //     "meta" => $hero_meta,
            //     "meta_extra" => $hero_extra,
            //     "calendar_url" => $hero_calendar . "?_cal=1",
            //     "slides" => $fields['gallery'],
            //     'bg_colour' => get_field('header_background_colour') ?? '',
            //     'colour' => get_field('header_text_colour') ?? ''
            // ]);
        }
        ?>

        <div id="main" class="single-main main-content container container--gutters has-sidebar waypoint">
            <aside class="item-meta step-in">

                <h2 class="title title--h3"><?php echo ucfirst($type); ?> details</h2>
                <ul class="item-details">
                    <?php if (!empty(ifne($fields, 'opening_date'))) { ?>
                        <li>
                            <h3 class="title title--h4">Opening</h3>
                            <?php
                            $datetime_formatted = date('l, j F', strtotime($fields['opening_date']));

                            if (!empty($fields['opening_time'])) {
                                $datetime_formatted .= ', ' . get_formatted_time($fields['opening_time']);
                            }

                            echo $datetime_formatted;
                            ?>
                        </li>
                    <?php } ?>
                    <li>
                        <h3 class="title title--h4">Running</h3>
                        <?php echo get_formatted_datetime($fields); ?>
                        <a href="<?php the_permalink(); ?>?_cal=1" class="icon link-cal">Add to Calendar</a>
                    </li>
                    <?php if (!empty(ifne($fields, 'location'))) { ?>
                        <li>
                            <h3 class="title title--h4">Location</h3>
                            <?php echo $fields['location']; ?>
                        </li>
                    <?php } ?>
                    <?php if (!empty(ifne($fields, 'cost'))) { ?>
                        <li>
                            <h3 class="title title--h4">Cost</h3>
                            <?php echo $fields['cost']; ?>
                        </li>
                    <?php } ?>
                    <?php if (isset($fields['additional_fields']) && !empty($fields['additional_fields'])) { ?>
                        <?php foreach ($fields['additional_fields'] as $additional_field) { ?>
                            <li>
                                <h3 class="title title--h4"><?php echo $additional_field['label']; ?></h3>
                                <?php echo $additional_field['value']; ?>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>

                <?php if (!empty(ifne($fields, 'buy_tickets_url'))) { ?>
                    <a href="<?php echo $fields['buy_tickets_url']; ?>" class="btn btn-block btn-buy u-bg" target="_blank">Buy Tickets</a>
                <?php } ?>

                <?php $terms = get_the_term_list(get_the_ID(), $type . '_tag', '<ul class="item-tags clearfix"><li>', '</li><li>', '</li></ul>'); ?>
                <?php if (!empty($terms)) { ?>
                    <h2 class="title u-color">Tags</h2>
                    <?php echo $terms; ?>
                <?php } ?>

                <?php
                // Retrieve hero image
                $hero_image = get_resized_image($fields['hero_image'], 'whats_on_hero');
                ?>


                <div class="single__share social-links" data-url="<?php echo get_the_permalink(); ?>" data-title="<?php echo get_the_title() ?>" data-image-url="<?php echo $hero_image ?>">
                    <div class="single__social step-in">
                        <h3 class="title title--h4">Share this event:</h3>
                        <ul>
                            <li>
                                <a href="" class="facebook share-facebook" title="Share this on Facebook">
                                    <span class="icon"></span>
                                    <span class="text">Share on Facebook</span>
                                </a>
                            </li>
                            <li>
                                <a href="" class="twitter share-twitter" title="Share this on on Twitter">
                                    <span class="icon"></span>
                                    <span class="text">Share on Twitter</span>
                                </a>
                            </li>
                            <li>
                                <a href="" class="share share-mailto" title="Share via Email">
                                    <span class="icon"></span>
                                    <span class="text">Share via Email</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </aside>

            <div class="content-wrapper about-content-wrapper waypoint">
                <div class="content-outer">

                    <div class="whatson-item-content is-editable step-in">

                        <?php the_content(); ?>

                        <?php if (!empty(ifne($fields, 'trailing_quote'))) { ?>
                            <blockquote>
                                <p>
                                    "<?php echo ifne($fields, 'trailing_quote'); ?>"
                                </p>
                                <?php if (!empty(ifne($fields, 'trailing_quote_author'))) { ?>
                                    <footer>— <?php echo ifne($fields, 'trailing_quote_author'); ?></footer>
                                <?php } ?>
                            </blockquote>
                        <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_template_part('parts/related-posts'); ?>


<?php get_footer(); ?>