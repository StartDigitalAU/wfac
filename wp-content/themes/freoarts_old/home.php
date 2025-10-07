<?php

global $body_class;
$body_class = 'page-news page-news-landing is-news';

global $wp_query;

get_header();

$ppp = 12;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args = [
    'post_type' => 'post',
    'ignore_sticky_posts' => 1,
    'post_status' => 'publish',
    'posts_per_page' => $ppp,
    'order' => 'DESC',
    'orderby' => 'date',
    'paged' => $paged
];

if (isset($_GET['type']) && !empty($_GET['type'])) {
    $args['meta_query'][] = [
        'key' => 'type',
        'compare' => '=',
        'value' => $_GET['type'],
    ];
}

$query = new WP_Query($args);

if ($sticky_post_ids = get_option('sticky_posts')) {
    $sticky_args = $args;
    $sticky_args['posts_per_page'] = 1;
    $sticky_args['post__in'] = get_option('sticky_posts');
    $sticky_query = new WP_Query($sticky_args);
}
?>
<?php if (isset($sticky_query)) { ?>
<?php if ($sticky_query->have_posts()) : ?>
<?php while ($sticky_query->have_posts()) : $sticky_query->the_post(); ?>
<?php
            $fields = get_fields();
            $news_type = ifne($fields, 'type', 'news');
            ?>
<a href="<?php the_permalink(); ?>" title="Read more about <?php the_title(); ?>">
  <?php
                render('parts/news/news-hero.php', [
                    "title" => get_the_title(),
                    "meta" => ($news_type == 'media_release') ? 'Media Release' : 'News',
                    "img_url" => get_resized_image($fields['hero_image'], 'hero_image', $GLOBALS['default_img']),
                    "img_alt" => "",
                    "colour" => "transparent",
                ]);
                ?>
</a>
<?php endwhile; ?>
<?php endif; ?>
<?php } else { ?>
<div class="whats-on-hero-landing" data-hero>
  <div class="container container--gutters">
    <h1 class="whats-on-hero-landing__title title title--h1">News</h1>
  </div>
</div>
<?php } ?>
<div id="main" class="main-content waypoint">
  <?php get_template_part('parts/news/menu'); ?>

  <div id="whats-on-loading">
    <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" preserveAspectRatio="xMidYMid"
      style="margin:auto;background:0 0;display:block;shape-rendering:auto" viewBox="0 0 100 100">
      <circle cx="50" cy="50" r="32" fill="none" stroke="#27282d" stroke-dasharray="50.26548245743669 50.26548245743669"
        stroke-linecap="round" stroke-width="8">
        <animateTransform attributeName="transform" dur="1.3513513513513513s" keyTimes="0;1" repeatCount="indefinite"
          type="rotate" values="0 50 50;360 50 50" />
      </circle>
    </svg>
  </div>
  <div id="whats-on__ajax-wrapper">
  </div>
</div>

<?php get_footer(); ?>