<?php

/****************************************************
 *
 * COURSE MANAGEMENT - BULK EMAIL
 *
 ****************************************************/

use SendGrid\Email;
use SendGrid\Exception as SendGridException;

$HumaanEmailScheduler = null;

/**
 * Create email scheduler
 *
 */
function freoarts_create_email_scheduler()
{

    global $HumaanEmailScheduler;

    $HumaanEmailScheduler = new HumaanEmailScheduler();
}
add_action('init', 'freoarts_create_email_scheduler', 1);

/**
 * Display the Course Management form
 *
 */
function view_admin_course_management_bulk_email()
{

    /*
    $result = wc_create_refund(array(
        'order_id' => 538,
        'amount' => 50
    ));
    */

    $notification = freoarts_bulk_send_course_enrolee_email();

    $archived_course = false;
    if (isset($_GET['archived_course']) && !empty($_GET['archived_course'])) {
        $archived_course = true;
    }

    $course_id = null;
    if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
        $course_id = $_GET['course_id'];
    }
    if (isset($_POST['course_id']) && !empty($_POST['course_id'])) {
        $course_id = $_POST['course_id'];
    }

    $args = array(
        'posts_per_page'    => -1,
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'orderby'           => 'title',
        'tax_query'         => array(
            array(
                'taxonomy'  => 'product_type',
                'field'     => 'slug',
                'terms'     => 'course'
            )
        )
    );

    $courses = get_posts($args);
    ?>
    <style type="text/css">

        span.label {display: block; font-weight: bold;}

        div.field {margin: 0 0 10px; border-bottom: 1px solid #eee; padding: 0 0 10px;}

        form.buttons { float: left; margin-right: 10px; }

        form.buttons input,
        a.button {
            display: block;
            float: left;
            cursor: pointer;
            font-family: sans-serif;
            margin-left: 4px;
            padding: 3px 8px;
            position: relative;
            top: -3px;
            text-decoration: none;
            font-size: 12px;
            border: 0 none;
            background: #f1f1f1;
            color: #21759b;
            margin: 10px 4px 20px;
            line-height: 15px;
            padding: 3px 10px;
            white-space: nowrap;
            -webkit-border-radius: 10px;
        }

        form.buttons input:hover {
            color: #d54e21;
        }

        div.clear { clear: both; }

    </style>

    <div class="wrap">

        <h1>Bulk Email Enrolees</h1>

        <?php if (!empty($notification)) { echo $notification; } ?>

        <form method="post" action="">

            <div id="poststuff">
                <div class="postbox acf-postbox">
                    <div class="inside acf-fields -left">

                        <?php if ($archived_course) { ?>
                            <input type="hidden" name="archived_course" value="true"/>
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>"/>
                        <?php } else { ?>
                            <div class="acf-field acf-field-select">
                                <div class="acf-label">
                                    <label for="course_id">Course</label>
                                </div>
                                <div class="acf-input">
                                    <select id="course_id" name="course_id">
                                        <?php
                                        foreach ($courses as $course) {

                                            $start_date = format_acf_date(get_field('start_date', $course->ID), 'Y-m-d');

                                            $selected = ($course->ID == $course_id) ? ' selected="selected"' : '';
                                            echo '<option value="' . $course->ID . '"' . $selected . '>' . $course->post_title . ' (' . $start_date . ')</option>';
                                        }
                                        ?>
                                    </select>
                                    <p class="description">Email will be sent to all enrolees of this course.</p>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="acf-field acf-field-text">
                            <div class="acf-label">
                                <label for="subject">Subject</label>
                            </div>
                            <div class="acf-input">
                                <div class="acf-input-wrap">
                                    <input type="text" id="subject" name="subject" value="<?php if (isset($_POST['subject']) && !empty($_POST['subject'])) { echo $_POST['subject']; } ?>">
                                </div>
                                <p class="description">Subject will be automatically prefixed with "Fremantle Arts Centre".</p>
                            </div>
                        </div>

                        <div class="acf-field acf-field-text">
                            <div class="acf-label">
                                <label for="email_from">Email From</label>
                            </div>
                            <div class="acf-input">
                                <div class="acf-input-wrap">
                                    <input type="text" id="email_from" name="email_from" value="<?php if (isset($_POST['email_from']) && !empty($_POST['email_from'])) { echo $_POST['email_from']; } ?>">
                                </div>
                                <p class="description">The email address that recipients will respond to.</p>
                            </div>
                        </div>

                        <div class="acf-field acf-field-text">
                            <div class="acf-label">
                                <label for="content">Content</label>
                            </div>
                            <div class="acf-input">
                                <div class="acf-input-wrap">
                                    <?php
                                    $content = '';
                                    if (isset($_POST['content']) && !empty($_POST['content'])) {
                                        $content = $_POST['content'];
                                    }

                                    wp_editor(stripslashes($content), 'content');
                                    ?>
                                    <!-- textarea id="content" name="content" rows="20"><?php if (isset($_POST['content']) && !empty($_POST['content'])) { echo $_POST['content']; } ?></textarea -->
                                </div>
                            </div>
                        </div>

                        <div class="acf-field acf-field-text">
                            <div class="acf-label">
                                <label for="test_email_to">Test Email To</label>
                            </div>
                            <div class="acf-input">
                                <div class="acf-input-wrap">
                                    <input type="text" id="test_email_to" name="test_email_to" value="<?php if (isset($_POST['test_email_to']) && !empty($_POST['test_email_to'])) { echo $_POST['test_email_to']; } ?>">
                                </div>
                                <p class="description">The email address to send a test email to.</p>
                            </div>
                        </div>

                        <div class="acf-field acf-field-text">
                            <div class="acf-label">
                            </div>
                            <div class="acf-input">
                                <div class="acf-input-wrap">
                                    <input class="button" type="submit" name="send_test" value="Send Test Email" />
                                    <input class="button button-primary" type="submit" name="send" value="Send to Enrolees" onclick="confirm('Are you sure you want to send the bulk email?');" />
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>

    </div>
    <?php
}

function freoarts_bulk_send_course_enrolee_email()
{
    global $wpdb;
    global $HumaanEmailScheduler;

    // TODO: For testing sending scheduled emails
    // $HumaanEmailScheduler->sendEmails();

    // Was a form submitted?
    if (
        !(isset($_POST['send']) && !empty($_POST['send'])) &&
        !(isset($_POST['send_test']) && !empty($_POST['send_test']))
    ) {

        return null;
    }

    $HumaanEmailScheduler->init();

    // Validate the submitted fields
    $errors = array();

    $archived_course = false;
    if (isset($_POST['archived_course']) && !empty($_POST['archived_course'])) {
        $archived_course = true;
    }

    if (!(isset($_POST['course_id']) && !empty($_POST['course_id']))) {
        $errors['course_id'] = 'Please select a course in order to send the email to the enrolees.';
    }
    else {
        $course_id = $_POST['course_id'];
    }

    if (!(isset($_POST['subject']) && !empty($_POST['subject']))) {
        $errors['subject'] = 'Please include a subject line.';
    }
    else {
        $subject = $_POST['subject'];
    }

    if (!(isset($_POST['email_from']) && !empty($_POST['email_from']) && is_email($_POST['email_from']))) {
        $errors['email_from'] = 'Please include an address the email will show as sent from.';
    }
    else {
        $email_from = $_POST['email_from'];
    }

    if (!(isset($_POST['content']) && !empty($_POST['content']))) {
        $errors['content'] = 'Please provide content for the email.';
    }
    else {
        $content = stripslashes($_POST['content']);
    }

    if (isset($_POST['send_test']) && !empty($_POST['send_test'])) {
        if (!(isset($_POST['test_email_to']) && !empty($_POST['test_email_to']) && is_email($_POST['test_email_to']))) {
            $errors['content'] = 'Please provide a test address to send the email.';
        }
    }

    // If errors, stop
    if (!empty($errors)) {

        return '<div id="message" class="notice notice-error"><p>' . implode('<br>', $errors) . '</p></div>';
    }

    // Store the list of recipients
    $recipients = array();

    // Is the email a test?
    if (isset($_POST['send_test']) && !empty($_POST['send_test'])) {

        $recipients[] = array(
            'email'         => $_POST['test_email_to'],
            'first_name'    => 'Test',
            'last_name'     => 'User'
        );
    }
    // Is it a real bulk email?
    else {

        if ($archived_course) {

            $sql = "SELECT
                    e.email,
                    e.first_name,
                    e.last_name
                FROM wp_enrolments AS e
                WHERE e.archived_course_id = {$course_id}
                AND is_archived = 1
                AND e.trashed != 1
                ";
        }
        else {

            $sql = "SELECT
                    e.email,
                    e.first_name,
                    e.last_name
                FROM wp_enrolments AS e
                WHERE e.course_id = {$course_id}
                AND is_archived = 0
                AND e.trashed != 1
                ";
        }

        $enrolees = $wpdb->get_results($sql, OBJECT);

        foreach ($enrolees as $enrolee) {

            $recipients[] = array(
                'email'         => $enrolee->email,
                'first_name'    => $enrolee->first_name,
                'last_name'     => $enrolee->last_name
            );
        }
    }

    // Loop through the recipients and send the email
    if (!empty($recipients)) {

        ob_start();
        include(TEMPLATEPATH . '/parts/email/enrolee-message.php');
        $html_email = ob_get_contents();
        ob_end_clean();

        $template_id = $HumaanEmailScheduler->addTemplate(array(
            'course_id'     => $course_id,
            'subject'       => $subject,
            'email_from'    => $email_from,
            'content'       => $html_email
        ));

        if (!$template_id) {

            return '<div id="message" class="notice notice-error"><p>Email template could not be saved to the database.</p></div>';
        }

        foreach ($recipients as $recipient) {

            $recipient_id = $HumaanEmailScheduler->addRecipient(array(
                'template_id'   => $template_id,
                'email'         => $recipient['email'],
                'full_name'     => $recipient['first_name'] . ' ' . $recipient['last_name'],
                'status'        => 'pending'
            ));
        }
    }

    return '<div id="message" class="notice notice-success"><p>Emails were successfully scheduled.</p></div>';
}