<?php

/**
 * Template Name: Contact
 *
 */

global $body_class;
$body_class = 'page-admin page-contact';

$fields = $GLOBALS['page_fields'];

get_header();

?>

<?php
$page_title = get_field('menu_heading') ?? '';
if($page_title == '') {
    $page_title = get_the_title();
}
$header_background_colour = get_field('header_background_colour') ?? '';
$header_text_colour = get_field('header_text_colour') ?? '';
?>

<section class="contact-hero" data-hero>
    <div class="container container--gutters">
        <h1 class="contact-hero__title title title--h1"><?= $page_title ?></h1>
        <?php /*<p>Enjoy Fremantle Arts Centre in the most rewarding way possible — as a member.</p>*/?>
    </div>
</section>


<div id="main" class="main-content container container--gutters">

    <div class="content-panel upper">
        <div class="content-grid">
            <section class="content-grid__column">
                <div class="title-wrap">
                    <h2 class="title">
                        <span>Send Us</span> a Message
                    </h2>
                </div>
                <div class="contact-form-wrapper">
                    <div class="contact-form">
                        <form id="contact-form" action="#contact-form" method="post" class="clearfix">
                            <input type="hidden" name="action" value="contact-form-submission" />
                            <div class="field">
                                <label class="" for="first-name">First Name <span>*</span></label>
                                <input type="text" name="first_name" id="first-name" required />
                            </div>
                            <div class="field">
                                <label class="" for="last-name">Surname <span>*</span></label>
                                <input type="text" name="last_name" id="last-name" required />
                            </div>
                            <div class="field">
                                <label class="" for="phone-number">Contact Number</label>
                                <input type="tel" name="contact_number" id="phone-number" />
                            </div>
                            <div class="field">
                                <label class="" for="email">Email Address <span>*</span></label>
                                <input type="email" name="email" id="email" required />
                            </div>
                            <div class="field textarea">
                                <label class="" for="message">Message <span>*</span></label>
                                <textarea name="message" id="message" required></textarea>
                            </div>
                            <div class="ahhahoney">
                                <label for="name"></label>
                                <input autocomplete="off" type="text" id="name" name="name" placeholder="Your name here">
                                <label for="work-email"></label>
                                <input autocomplete="off" type="email" id="work-email" name="work-email" placeholder="Your e-mail here">
                            </div>


                            <!-- Google reCAPTCHA box -->
                            <div class="field">
                                <div class="g-recaptcha" data-sitekey="<?= RECAPTCHA_SITE_KEY; ?>"></div>
                            </div>

                            <button class="btn-black" type="submit">
                                <span class="text">Send Message</span>
                                <span class="icon"></span>
                            </button>
                            <span class="u-vis-hide"><span>*</span> indicates a required field</span>
                        </form>
                    </div>
                </div>
            </section>
            <section class="content-grid__column">
                <div class="title-wrap">
                    <h2 class="title">Contact Us</h2>
                </div>
                <ul>
                    <li>
                        <span>Location</span>
                        <?php echo ifne($GLOBALS['theme_options'], 'street_address'); ?>
                    </li>
                    <li>
                        <span>Postal</span>
                        <?php echo ifne($GLOBALS['theme_options'], 'postal_address'); ?>
                    </li>
                    <li>
                        <span>Phone</span>
                        <?php echo ifne($GLOBALS['theme_options'], 'phone_number'); ?>
                    </li>
                    <li>
                        <span>Email</span>
                        <?php // TODO: Email obfusaction ?>
                        <a href="mailto:<?php echo ifne($GLOBALS['theme_options'], 'email_address'); ?>" title="Email us"><?php echo ifne($GLOBALS['theme_options'], 'email_address'); ?></a>
                    </li>
                </ul>

                <div class="title-wrap">
                    <h2 class="title">Opening Hours</h2>
                    <p>Free Admission</p>
                </div>

                <ul>
                    <?php if (isset($GLOBALS['theme_options']['opening_times']) && !empty($GLOBALS['theme_options']['opening_times'])) { ?>
                        <?php foreach ($GLOBALS['theme_options']['opening_times'] as $opening_times) { ?>
                        <li>
                            <span><?php echo ifne($opening_times, 'label'); ?></span>
                            <?php echo ifne($opening_times, 'value'); ?>
                        </li>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </section>
        </div>

    </div>

    <div class="content-panel lower">
        <div class="title-wrap">
            <h2 class="title">Find Us</h2>
        </div>
        <?php $map_image = get_resized_image($fields['map_image'], 'contact_image'); ?>
        <a href="https://www.google.com.au/maps/place/<?php echo urlencode(ifne($fields, 'google_map_address')); ?>/" class="content-col has-img" style="background-image: url(<?php echo $map_image; ?>)" title="View our location on Google Maps" target="_blank">
            <div class="content">
                <p><span class="bg-white"><?php echo ifne($fields, 'header_line_01'); ?></span></p>
                <p><span class="bg-black"><?php echo ifne($fields, 'header_line_02'); ?></span></p>
            </div>
        </a>
        <section class="content-col has-map">
            <div id="g-map" class="google-map" data-lat="<?php echo ifne($fields, 'latitude'); ?>" data-long="<?php echo ifne($fields, 'longitude'); ?>" data-marker="<?php echo $GLOBALS['template_url']; ?>/img/marker.png"></div>
        </section>
    </div>

</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php get_footer(); ?>