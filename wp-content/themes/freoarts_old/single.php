<?php

global $post;

$type = get_field('type', $post->ID);

if ($type == 'media_release') {
    $type_label = 'Media Release';
}
else {
    $type_label = 'News';
}

global $body_class;
$body_class = 'page-news page-news-single is-news';

$fields = get_fields($post->ID);

get_header();
?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

        <?php
            render('parts/news/news-hero.php', [
                "title" => get_the_title(),
                "meta" => $type_label . " / ",
                "meta_extra" => "Posted " . get_the_date('j M Y'),
                "img_url" => get_resized_image($fields['hero_image'], 'hero_image'),
                "img_alt" => "",
                "colour" => null,
            ]);
        ?>

        <?php // get_template_part('parts/news/header-single'); ?>
        <div id="main" class="single main-content container container--gutters">
            <div class="single__grid waypoint">

                <div class="single__content is-editable">
                    <?php the_content(); ?>
                </div>

                <aside class="single__share social-links"
                    data-url="<?php echo get_the_permalink(); ?>"
                    data-title="<?php echo get_the_title() ?>"
                    data-image-url="<?php echo $card_image; ?>"
                >
                    <?php get_template_part('parts/news/subscribe-form'); ?>

                    <div class="single__social step-in">
                        <h3 class="title title--h4">Share this Article:</h3>
                        <ul>
                            <li>
                                <a href="" class="facebook share-facebook">
                                    <span class="icon"></span>
                                    <span class="text">Share on Facebook</span>
                                </a>
                            </li>
                            <li>
                                <a href="" class="twitter share-twitter">
                                    <span class="icon"></span>
                                    <span class="text">Share on Twitter</span>
                                </a>
                            </li>
                            <li>
                                <a href="" class="share share-mailto">
                                    <span class="icon"></span>
                                    <span class="text">Share via Email</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </aside><!-- social-links -->

                <?php /*
                    <aside class="news-meta item-meta">

                        <?php $terms = get_the_term_list(get_the_ID(), 'post_tag', '<ul class="item-tags clearfix"><li>','</li><li>','</li></ul>'); ?>
                        <?php if (!empty($terms)) { ?>
                            <h2 class="title u-color">Tags</h2>
                            <?php echo $terms; ?>
                        <?php } ?>

                        <h2 class="title u-color">Popular Posts</h2>

                        <?php $popular_posts = get_popular_posts(3); ?>
                        <?php if (!empty($popular_posts)) { ?>
                            <?php foreach ($popular_posts as $popular_post_id) { ?>
                                <?php $popular_post = get_post($popular_post_id); ?>
                                <article class="card news">
                                    <a href="<?php echo get_the_permalink($popular_post_id); ?>" title="Read <?php echo get_the_title($popular_post_id); ?>">
                                        <h3 class="title"><?php echo get_the_title($popular_post_id); ?></h3>
                                        <span class="faux-link u-color">Read more</span>
                                    </a>
                                </article>
                            <?php } ?>
                        <?php } ?>

                    </aside>
                */ ?>

            </div>
        </div><!-- main-content -->

    <?php endwhile; ?>
<?php endif; ?>

<?php get_template_part('parts/related-posts'); ?>

<?php get_footer(); ?>