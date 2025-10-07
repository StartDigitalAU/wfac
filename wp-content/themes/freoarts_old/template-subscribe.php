<?php

/**
 * Template Name: Subscribe
 *
 */

global $body_class;
$body_class = 'page-subscribe';

$fields = $GLOBALS['page_fields'];

$content = get_the_content();

get_header();
?>

<?php
    render('parts/landing/landing-hero.php', [
        "title" => get_the_title(),
        "meta" => null,
        "img_url" => null,
        "bg_colour" => "transparent",
    ]);

?>
<main>
    <div class="container container--gutters">
        <div class="is-editable">
            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?= $content ?>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <form action="https://fremantleartscentre.createsend.com/t/r/s/kyuiduj/" method="post" class="subscribe-form">
            <label class="u-vis-hide" for="newsletter-email">Your email</label>
            <input type="email" id="newsletter-email" name="cm-kyuiduj-kyuiduj" placeholder="Your email" class="required email" aria-required="true">
            <button type="submit">
                <span class="text">Subscribe</span>
                <span class="icon"></span>
            </button>
        </form>
    </div>
</main>
    

<?php get_footer(); ?>