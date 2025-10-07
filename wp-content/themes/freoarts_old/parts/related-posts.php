<?php

global $post;

$related_tags = wp_get_post_terms($post->ID, 'related_tag');

if (!empty($related_tags)) {

    $related_tags_ids = array();
    foreach ($related_tags as $tag) {
        $related_tags_ids[] = $tag->term_id;
    }

    $args = array(
        'post__not_in' => array(get_the_ID()),
        'posts_per_page' => 4,
        'ignore_sticky_posts' => 1,
        'post_type' => array('post', 'whatson', 'product'),
        'meta_query' => array(
            array(
                'key' => 'start_date',
                'value' => date('Ymd'),
                'type' => 'numeric',
                'compare' => '>='
            )
        ),
        'tax_query' => array(
            array(
                'taxonomy' => 'related_tag',
                'field' => 'term_id',
                'terms' => $related_tags_ids,
                'operator' => 'IN'
            )
        )
    );

    $related_posts_query = new WP_Query($args);
}
?>
<?php if (isset($related_posts_query) && $related_posts_query->have_posts()) : ?>
    <div class="news waypoint">
        <div class="container container--gutters">
            <div class="news__title">
                <h2 class="news__title title title--h1 title--arrow step-in">
                    <a href="/news">
                        <span class="text">Also from FAC</span>
                        <sup class="icon"></sup>
                    </a>
                </h2>
            </div>
            <div class="news__grid">
                <?php while ($related_posts_query->have_posts()) : $related_posts_query->the_post(); ?>
                    <div class="col">
                        <?php
                        render('parts/related-card.php', [
                            // 'news_item_id'
                            'excerpt_length' => 20,
                        ]);
                        ?>
                    </div>
                <?php endwhile;
                wp_reset_postdata(); ?>

            </div>
        </div>
    </div>
<?php endif; ?>