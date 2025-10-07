<?php
/**
 * Template Name: Covid Register
 */
global $body_class;
$body_class = 'page-about page-covid-register';
get_header();
?>
<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

        <?php
            render('parts/landing/landing-hero.php', [
                "title" => get_the_title(),
                "img_url" => null,
                "img_alt" => null,
                "colour" => null,
                "bg_colour" => "transparent",
            ]);
        ?>

        <div id="main" class="main-content container container--gutters">
            <div class="about-content-wrapper">
                <?php get_template_part('parts/page-sidebar'); ?>
                <section class="content-outer has-bg">
                    <?php /*
                    <header>
                        <?php if ($post->post_parent): ?>
                            <span class="tag u-color"><?php echo get_the_title($post->post_parent); ?></span>
                        <?php endif; ?>
                        <h1 class="page-title"><?php the_title(); ?></h1>
                    </header>
                    */ ?>
                    <h2 class="u-vis-hide">Read more about <?php the_title(); ?></h2>
                    <div class="is-editable">
                        <?php the_content(); ?>
                    </div>
                    <div class="contact-form-wrapper">
                        <form id="covid-register-form" action="#" method="post" class="clearfix" novalidate="novalidate">
                            <input type="hidden" name="action" value="covid-parse-form-submission">
                            <div class="field">
                                <label class="" for="name">Full Name <span>*</span></label>
                                <input type="text" name="name" id="name" required="" aria-required="true">
                            </div>
                            <div class="field">
                                <label class="" for="email_address">Email Address</label>
                                <input type="email" name="email_address" id="email_address">
                            </div>
                            <div class="field">
                                <label class="" for="contact_number">Contact Number <span>*</span></label>
                                <input type="text" name="contact_number" id="contact_number" required="" aria-required="true">
                            </div>
                            <div class="field has-select">
                                <label for="number_in_group">Number in Group <span>*</span></label>
                                <div class="styled">
                                    <select name="number_in_group" id="number_in_group" class="required">
                                        <?php for ($i = 1; $i <= 10; $i++) : ?>
                                            <option value="<?= $i; ?>"><?= $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <span class="arrow"></span>
                                </div>
                            </div>
                            <div class="field">
                                <input type="checkbox" name="join_mailing_list" id="join_mailing_list" value="1" style="display: inline-block; width: auto; margin-right: 10px;">
                                <label class="" for="join_mailing_list" style="display: inline-block;">Join Our Mailing List</label>
                                <p><em>Receive monthly updates about FAC's exhibitions, concerts, courses, events and more.</em></p>
                            </div>
                            <button class="btn-black" type="submit">
                                <span class="text">Submit</span>
                                <span class="icon"></span>
                            </button>
                            <span class="note"><span>*</span> indicates a required field</span>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
<?php get_footer(); ?>