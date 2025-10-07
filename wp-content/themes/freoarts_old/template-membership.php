<?php

/**
 * Template Name: Purchase Membership
 *
 */

global $body_class;
$body_class = 'page-admin page-contact';

$fields = $GLOBALS['page_fields'];

get_header();

$disabled = $GLOBALS['theme_options']['membership_disable_purchase'];

?>


<section class="contact-hero contact-hero--membership" data-hero>
    <div class="container container--gutters">
        <div class="content">
            <h1 class="contact-hero__title title title--h1"><?= get_the_title() ?></h1>
            <p><?= get_field('description')?></p>
        </div>
        <img src="<?= get_field('card_image')['url'] ?>" alt="<?= get_field('card_image')['alt'] ?>" />
    </div>
</section>


<div id="main" class="main-content container container--gutters">

    <section class="member-content-wrapper">

        <?php if (isset($fields['options']) && !empty($fields['options'])) { ?>
            <?php $user_role = has_user_member_role(); ?>
            <div class="grid-wrapper member-options-grid waypoint">
                <?php foreach ($fields['options'] as $option) { ?>
                        <?php
                        $_product = wc_get_product($option['product_id']);
                        $membership_role_type = get_field('membership_type', $_product->get_id());
                        ?>
                        <?php if (empty($user_role) && !$disabled) { ?>
                            <a href="<?php eu($GLOBALS['site_url'] . '/cart/?add-to-cart=' . $option['product_id']) ?>" class="card member-option step-in" title="Purchase this membership">
                        <?php } else { ?>
                            <div class="card member-option<?php if ($user_role != $membership_role_type) { ?> inactive<?php } ?>">
                        <?php } ?>
                            <header>
                                <h2 class="title title--h2">
                                    <?php echo ifne($option, 'title'); ?>
                                </h2>
                                <h3 class="title title--h3"><?php echo ifne($option, 'sub_title'); ?></h3>
                            </header>
                            <div class="inner">
                                <p class=""><?php echo ifne($option, 'summary'); ?></p>
                                <span class="cost-outer">
                                    <span class="price">$<?php echo $_product->get_price(); ?></span>
                                    <span class="peryear">Per year</span>
                                </span>
                            </div>
                            <footer class="btn-black">
                                <span class="text">
                                <?php
                                if ($disabled == true) {
                                    echo 'Coming Soon';
                                }
                                elseif ($user_role != $membership_role_type) {
                                    echo 'Purchase membership';
                                }
                                else {
                                    echo 'You are already a member';
                                }
                                ?>
                                </span>
                                <span class="icon"></span>
                            </footer>
                        <?php if (empty($user_role) && !$disabled) { ?>
                            </a>
                        <?php } else { ?>
                            </div>
                        <?php } ?>
                <?php } ?>
            </div>
        <?php } ?>

    </section>

    <?php if (isset($fields['membership_perks']) && !empty($fields['membership_perks'])) { ?>
        <section class="member-content-wrapper">

            <div class="title-wrap">
                <h2 class="title title--h1">Membership perks</h2>
            </div>

            <div class="grid-wrapper member-perks-grid waypoint">
                <?php
                    $i = 0;
                    $icons = array('a', 'b', 'c', 'd', 'e', 'f');
                ?>
                <?php foreach ($fields['membership_perks'] as $perk) { ?>
                    <div class="col step-in">
                        <div class="card member-perk <?php echo $icons[$i]; ?>">
                            <?php
                            $image = ifne($perk, 'image') ?? [];
                            if(!empty($image)) {
                                $img_url = $image['sizes']['c516x240'];
                                echo '<img src="' . $img_url . '" alt="image for ' . ifne($option, 'title') . '" />';
                            }
                            ?>
                            <h2 class="title"><?php echo ifne($perk, 'title'); ?></h2>
                            <div class="inner">
                                <p><?php echo ifne($perk, 'summary'); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php
                    if ($i == 5) {
                        $i = 0;
                    }
                    else {
                        $i++;
                    }
                    ?>
                <?php } ?>
            </div>
        </section>
    <?php } ?>

    <section class="member-content-wrapper">

        <div class="title-wrap">
            <h2 class="title title--h1">Member benefits</h2>
        </div>

        <div class="grid-wrapper member-benefits-grid">
            <ul class="clearfix waypoint">
                <?php if (isset($fields['member_benefits']) && !empty($fields['member_benefits'])) { ?>
                    <?php foreach ($fields['member_benefits'] as $benefit) { ?>
                        <?php if (!empty($benefit['is_heading'])) { ?>
                            <li class="heading step-in bg-black">
                                <h2 class="title"><?php echo ifne($benefit, 'name'); ?></h2>
                            </li>
                        <?php } else { ?>
                            <li class="step-in">
                                <h2 class="title">
                                    <?php $url = ifne($benefit, 'url'); ?>
                                    <?php if (empty($url)) { ?>
                                        <a href="<?php echo $url; ?>" title="<?php echo ifne($benefit, 'name'); ?>"><?php echo ifne($benefit, 'name'); ?></a>
                                    <?php } else { ?>
                                        <?php echo ifne($benefit, 'name'); ?>
                                    <?php } ?>
                                </h2>
                                <span><?php echo ifne($benefit, 'summary'); ?></span>
                                <div class="content is-editable">
                                    <p><?php echo ifne($benefit, 'contact_details'); ?></p>
                                </div>
                            </li>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    </section>


</div>


<?php /*
<div id="main" class="main-content">

    <div class="banner-wrapper">
        <div class="container">
            <h1 class="page-title"></h1>
        </div>
    </div>

    <div class="member-content-wrapper">
        <div class="container">

            <h2 class="panel-title">Enjoy Fremantle Arts Centre in the most rewarding way possible &#8212; <span class="is-lime">as a member</span></h2>

            <?php if (isset($fields['options']) && !empty($fields['options'])) { ?>
                <?php $user_role = has_user_member_role(); ?>
                <div class="grid-wrapper member-options-grid waypoint">
                    <?php foreach ($fields['options'] as $option) { ?>
                        <div class="col step-in">
                            <?php
                            $_product = wc_get_product($option['product_id']);
                            $membership_role_type = get_field('membership_type', $_product->get_id());
                            ?>
                            <?php if (empty($user_role) && !$disabled) { ?>
                                <a href="<?php eu($GLOBALS['site_url'] . '/cart/?add-to-cart=' . $option['product_id']) ?>" class="card member-option" title="Purchase this membership">
                            <?php } else { ?>
                                <div class="card member-option<?php if ($user_role != $membership_role_type) { ?> inactive<?php } ?>">
                            <?php } ?>
                                <header>
                                    <h2 class="title">
                                        <?php echo ifne($option, 'title'); ?>
                                        <span><?php echo ifne($option, 'sub_title'); ?></span>
                                    </h2>
                                </header>
                                <div class="inner">
                                    <p><?php echo ifne($option, 'summary'); ?></p>
                                    <span class="cost-outer">$<?php echo $_product->get_price(); ?> <span>Per year</span></span>
                                </div>
                                <footer class="btn btn-block btn-teal">
                                    <?php
                                    if ($disabled == true) {
                                        echo 'Coming Soon';
                                    }
                                    elseif ($user_role != $membership_role_type) {
                                        echo 'Purchase membership';
                                    }
                                    else {
                                        echo 'You are already a member';
                                    }
                                    ?>
                                </footer>
                            <?php if (empty($user_role) && !$disabled) { ?>
                                </a>
                            <?php } else { ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <span class="border-helper"></span>
        </div>
    </div>

    <?php if (isset($fields['membership_perks']) && !empty($fields['membership_perks'])) { ?>
        <div class="member-content-wrapper">
            <div class="container">

                <h2 class="panel-title"><span class="border-helper"></span>membership perks</h2>

                <div class="grid-wrapper member-perks-grid waypoint">
                    <?php
                    $i = 0;
                    $icons = array('a', 'b', 'c', 'd', 'e', 'f');
                    ?>
                    <?php foreach ($fields['membership_perks'] as $perk) { ?>
                        <div class="col step-in">
                            <div class="card member-perk <?php echo $icons[$i]; ?>">
                                <h2 class="title"><?php echo ifne($perk, 'title'); ?></h2>
                                <div class="inner">
                                    <p><?php echo ifne($perk, 'summary'); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php
                        if ($i == 5) {
                            $i = 0;
                        }
                        else {
                            $i++;
                        }
                        ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>

    <div class="member-content-wrapper">
        <div class="container">

            <h2 class="panel-title">Member benefits</h2>

            <div class="grid-wrapper member-benefits-grid">
                <ul class="clearfix waypoint">
                    <?php if (isset($fields['member_benefits']) && !empty($fields['member_benefits'])) { ?>
                        <?php foreach ($fields['member_benefits'] as $benefit) { ?>
                            <?php if (!empty($benefit['is_heading'])) { ?>
                                <li class="heading step-in bg-<?php echo ifne($benefit, 'color', 'lime'); ?>">
                                    <h2 class="title"><?php echo ifne($benefit, 'name'); ?></h2>
                                </li>
                            <?php } else { ?>
                                <li class="step-in">
                                    <h2 class="title">
                                        <?php $url = ifne($benefit, 'url'); ?>
                                        <?php if (empty($url)) { ?>
                                            <a href="<?php echo $url; ?>" title="<?php echo ifne($benefit, 'name'); ?>"><?php echo ifne($benefit, 'name'); ?></a>
                                        <?php } else { ?>
                                            <?php echo ifne($benefit, 'name'); ?>
                                        <?php } ?>
                                    </h2>
                                    <span><?php echo ifne($benefit, 'summary'); ?></span>
                                    <div class="content is-editable">
                                        <p><?php echo ifne($benefit, 'contact_details'); ?></p>
                                    </div>
                                </li>
                            <?php } ?>
                        <?php } ?>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="colour-strip">
        <span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span>
    </div>
</div>

*/ ?>

<?php get_footer(); ?>
