<?php

global $body_class;
$body_class = 'page-internal page-woo';

get_header();

?>

<?php
    render('parts/landing/landing-hero.php', [
        "title" => null,
        "img_url" => null,
        "img_alt" => null,
        "colour" => "transparent",
        "bg_colour" => "transparent",
    ]);
?>

<div id="main" class="main-content">

    <div class="container container--gutters">
        <div class="section-404">
            <h1 class="title title--h1">
                <strong>404</strong>
                Page not found
            </h1>
            <p>Unfortunately the requested page could not be found.</p>

            <a href="/" class="btn-black">
                <span class="text">Go to the homepage</span>
                <span class="icon"></span>
            </a>
        </div>
    </div>

</div>

<?php get_footer(); ?>