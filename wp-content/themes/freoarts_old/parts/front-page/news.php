<?php
$latest_news = get_field('latest_news') ?? [];
$count = $latest_news === false ? 0 : count($latest_news);

if ($count < 3) {
    $post_ids = ($count !== 0)
        ? $post_ids = array_map(function ($article) {
            return ifne($article, 'article');
        }, $latest_news)
        : [];

    $args = [
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 3 - $count,
        'post__not_in' => $post_ids,
        'order' => 'DESC',
        'order_by' => 'date'
    ];

    if ($articles = get_posts($args)) {
        foreach ($articles as $article) {
            $latest_news[] = [
                'article' => $article->ID,
                'custom_description' => '',
            ];
        }
    }
}
?>

<?php if (!empty($latest_news)) { ?>
    <div class="news waypoint ">
        <h2 class="news__title title title--h1 title--arrow step-in">
            <a href="<?= get_permalink(get_option('page_for_posts')) ?>">
                <span class="text">News</span>
                <sup class="icon"></sup>
            </a>
        </h2>
        <div class="news__grid">
            <?php foreach ($latest_news as $article) { ?>
                <?php
                $excerpt_length = 20;
                $news_item_id = ifne($article, 'article');
                if (!$news_item_id) {
                    continue;
                }
                $title = get_the_title($news_item_id);
                $custom_excerpt = ifne($article, 'custom_description');
                if ($custom_excerpt != '') {
                    $excerpt = wp_trim_words($custom_excerpt, $excerpt_length);
                } else {
                    $excerpt = wp_trim_words(get_the_excerpt($news_item_id), $excerpt_length);
                }
                $card_image = get_field('card_image', $news_item_id) ?: get_field('hero_image', $news_item_id);

                $image_src = get_resized_image($card_image, 'grid_card_image_690');
                $image_alt = get_post_meta($card_image, '_wp_attachment_image_alt', TRUE);
                ?>
                <div class="col">
                    <article class="card-news step-in">
                        <?php if ($image_src) { ?>
                            <div class="card-news__image">
                                <img src="<?php echo $image_src; ?>" alt="<?= $image_alt ?>">
                            </div>
                        <?php } ?>
                        <p class="card-news__meta"><?= get_the_date('j M Y', $news_item_id); ?></p>
                        <h3 class="card-news__title"><?= $title ?></h3>
                        <p class="card-news__description"><?= $excerpt ?>
                        </p>
                        <a class="card-news__link" href="<?= get_the_permalink($news_item_id); ?>">
                            <span class="u-vis-hide">Read <?= $title ?></span>
                        </a>
                    </article>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>