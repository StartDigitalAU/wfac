<?php

global $body_class;
$body_class = 'page-about has-red';

get_header();

?>

<?php
$hero_image = null;
$img_alt = null;
$header_background_colour = get_field('header_background_colour') ?? '';
$header_text_colour = get_field('header_text_colour') ?? '';
render('parts/landing/landing-hero.php', [
	"title" => get_the_title(),
	"img_url" => $hero_image,
	"img_alt" => $img_alt,
	"colour" => $header_text_colour,
	"bg_colour" => $header_background_colour,
]);
?>

<div id="main" class="main-content container container--gutters">


	<?php if (have_posts()) : ?>
		<?php while (have_posts()) : the_post(); ?>
			<div class=" about-content-wrapper">
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>
	<?php endif; ?>

	<?php
	$args = array(
		'post_type' => 'product',
		'ignore_sticky_posts' => 1,
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => 'course'
			)
		),
		'meta_query' => array(
			'relation' => 'AND',
			array(
				'key' => 'tutor',
				'value' => $post->ID,
				'type' => 'BOOLEAN',
				'compare' => '=='
			)
		)
	);

	$course_query = new WP_Query($args);
	?>
	<?php if ($course_query->have_posts()) : ?>
		<div class="related-grid-wrapper has-bg waypoint">
			<h3 class="title">also by <?php the_title(); ?></h3>
			<div class="grid-wrapper related-grid">
				<?php while ($course_query->have_posts()) : $course_query->the_post(); ?>
					<?php get_template_part('parts/related-card'); ?>
				<?php endwhile;
				wp_reset_postdata(); ?>
			</div>
		</div>
	<?php endif; ?>



</div>

<?php get_footer(); ?>