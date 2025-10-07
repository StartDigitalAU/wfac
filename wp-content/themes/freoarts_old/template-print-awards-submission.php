<?php

/**
 * Template Name: Print Awards Submission
 *
 */

/*
    Modes:
    - terms (default)
    - contact
    - artwork
    - payment
    - completed

*/
$is_user_logged_in = is_user_logged_in();

if ( $is_user_logged_in ){
    
    $user_id = get_current_user_id();

    $submission = PrintAwardsSubmission::draftSubmission($user_id);
    $submission->loadImages();

    include_once( 'parts/print-awards/process-forms.php' );

    $available_stages = array(
        'terms',
        'contact',
        'artwork',
        'payment',
        'completed'
    );

    $user_id = get_current_user_id();


    /*
        Reload user draft again here to pickup changes from process form post
    */
    $submission = PrintAwardsSubmission::draftSubmission($user_id);
    $submission->loadImages();
    
    $current_stage = $submission->currentStage();

    if ($current_stage != 'completed') {

        $requested_stage = ifne($_REQUEST, 'stage', '');
        if (!in_array($requested_stage, $available_stages)){
            wp_redirect( get_permalink() . '?stage=' . urlencode( $current_stage ) );
            exit;
        }

        $requested_stage = in_array($requested_stage, $available_stages) ? $requested_stage : $current_stage;

        $current_stage_index = array_search( $current_stage, $available_stages );
        $requested_stage_index = array_search( $requested_stage, $available_stages );

        $the_stage = $requested_stage;
        //Is requested stage the same as current ideal stage OR one of the previous steps?

        if ( $requested_stage_index > $current_stage_index ){
            //Nope, can't do that
            wp_redirect( get_permalink() . '?stage=' . urlencode( $current_stage ) );
            exit;
        }
    }
    // If submission paid for and completed, only ever show Completed stage
    else {

        $the_stage = $current_stage;
    }
}


global $body_class;
$body_class = 'page-admin page-contact page-print-awards';

$fields = $GLOBALS['page_fields'];

get_header();
?>

<?php
    render('parts/landing/landing-hero.php', [
        "title" => 'FAC Print Award' . get_field('print_award_year', 'option'),
        "img_url" => null,
        "img_alt" => null,
        "colour" => null,
        "bg_colour" => 'transparent',
    ]);
?>

<div id="main" class="main-content container container--gutters">

     <div class="content-panel upper">
        <div class="print-awards-content">
            <?php
            
                if ( !$is_user_logged_in ){
    
                    get_template_part( 'parts/print-awards/please-login' );
    
                } else {
    
                    include_once( 'parts/print-awards/' . $the_stage . '.php' );
    
                }
    
            ?>
       </div>
    </div>

</div>

<?php get_footer(); ?>