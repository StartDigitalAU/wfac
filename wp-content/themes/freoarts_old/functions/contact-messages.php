<?php

/****************************************************
 *
 * CONTACT MESSAGES
 *
 ****************************************************/

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

$ContactMessagesFactory = null;

function freoarts_create_contact_messages_table()
{

    global $ContactMessagesFactory;

    $ContactMessagesFactory = new ContactMessagesFactory();
    $ContactMessagesFactory->init();
}
add_action('init', 'freoarts_create_contact_messages_table', 1);

function freoarts_contact_messages_admin_assets()
{
    wp_enqueue_style('humaan_list_table', get_template_directory_uri() . '/functions/library/css/humaan-list-table.css');
}
add_action('admin_init', 'freoarts_contact_messages_admin_assets', 1);

/**
 * AJAX submitted form parsing
 *
 */
function ajax_contact_form_submission()
{
    $response = [
        'message' => ''
    ];

    $recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);

    $resp = $recaptcha->setExpectedHostname($_SERVER['SERVER_NAME'])
        ->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

    // if (!$resp->isSuccess()) {
    //     $response['status'] = 'ERROR';
    //     $response['message'] = 'Unable to register your interest at this time.  Please try again later.';
    //     // wp_die();
    // }

    // var_dump($response);

    $first_name         = filter_input(INPUT_POST, 'first_name',        FILTER_SANITIZE_STRING);
    $last_name          = filter_input(INPUT_POST, 'last_name',         FILTER_SANITIZE_STRING);
    $contact_number     = filter_input(INPUT_POST, 'contact_number',    FILTER_SANITIZE_STRING);
    $email              = filter_input(INPUT_POST, 'email',             FILTER_SANITIZE_EMAIL);
    $message            = filter_input(INPUT_POST, 'message',           FILTER_SANITIZE_STRING);

    /**
     * Save submission to the database
     *
     */

    global $wpdb;

    $table_name = $wpdb->prefix . 'contact_messages';

    $wpdb->insert(
        $table_name,
        array(
            'first_name'                => $first_name,
            'last_name'                 => $last_name,
            'contact_number'            => $contact_number,
            'email'                     => $email,
            'message'                   => $message,
            'created_at'                => date('Y-m-d H:i:s'),
            'updated_at'                => date('Y-m-d H:i:s')
        )
    );

    /**
     * Send transactional email via SendGrid
     *
     */

    // TODO: Style the email template
    ob_start();
    include_once(TEMPLATEPATH . '/parts/email/contact-message.php');
    $html_email = ob_get_contents();
    ob_end_clean();

    $from_email = get_field('email_address', 'option');
    $subject = 'Fremantle Arts Centre Contact Receipt';
    $bcc = $from_email;
    $from_name = 'Fremantle Arts Centre';
    $to = $email;

    $emailClient = new HumaanEmail();
    $email_successful = $emailClient->sendEmail($html_email, $subject, $from_name, $from_email, $to, $bcc);

    if (!$resp->isSuccess()) {
        $response['status'] = 'ERROR';
        $response['message'] = 'Unable to register your interest at this time.  Please try again later.';
    } elseif ($email_successful) {
        $response['status'] = 'OK';
    } else {
        $response['status'] = 'ERROR';
        $response['message'] = 'Unable to register your interest at this time.  Please try again later.';
    }

    echo json_encode($response);

    wp_die();
}
add_action('wp_ajax_nopriv_contact-form-submission', 'ajax_contact_form_submission');
add_action('wp_ajax_contact-form-submission', 'ajax_contact_form_submission');