<?php

/****************************************************
 *
 * COURSE MANAGEMENT - BULK SMS
 *
 ****************************************************/

$HumaanSMSScheduler = null;

/**
 * Create sms scheduler
 *
 */
function freoarts_create_sms_scheduler()
{

    global $HumaanSMSScheduler;

    $smsClient = HumaanVonageSMSClient::getInstance();
    $HumaanSMSScheduler = new HumaanSMSScheduler($smsClient);
}
add_action('init', 'freoarts_create_sms_scheduler', 1);

/**
 * Display the Course Management form
 *
 */
function view_admin_course_management_bulk_sms()
{

    /*
    $result = wc_create_refund(array(
        'order_id' => 538,
        'amount' => 50
    ));
    */

    $notification = freoarts_bulk_send_course_enrolee_sms();

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

        <h1>Bulk SMS Enrolees</h1>

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
                                    <p class="description">SMS will be sent to all enrolees of this course.</p>
                                </div>
                            </div>
                        <?php } ?>


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

                                    // wp_editor(stripslashes($content), 'content');
                                    ?>
                                    <textarea id="content" name="content" rows="8"><?php if (isset($_POST['content']) && !empty($_POST['content'])) { echo $_POST['content']; } ?></textarea>
                                    <!-- textarea id="content" name="content" rows="20"><?php if (isset($_POST['content']) && !empty($_POST['content'])) { echo $_POST['content']; } ?></textarea -->
                                </div>
                            </div>
                        </div>

                        <div class="acf-field acf-field-text">
                            <div class="acf-label">
                                <label for="test_sms_to">Test SMS To</label>
                            </div>
                            <div class="acf-input">
                                <div class="acf-input-wrap">
                                    <input type="text" id="test_sms_to" name="test_sms_to" value="<?php if (isset($_POST['test_sms_to']) && !empty($_POST['test_sms_to'])) { echo $_POST['test_sms_to']; } ?>">
                                </div>
                                <p class="description">The mobile number to send a test sms to.</p>
                            </div>
                        </div>

                        <div class="acf-field acf-field-text">
                            <div class="acf-label">
                            </div>
                            <div class="acf-input">
                                <div class="acf-input-wrap">
                                    <input class="button" type="submit" name="send_test" value="Send Test SMS" />
                                    <input class="button button-primary" type="submit" name="send" value="Send to Enrolees" onclick="confirm('Are you sure you want to send the bulk sms?');" />
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

function freoarts_bulk_send_course_enrolee_sms()
{
    global $wpdb;
    /**
     * @var \HumaanSMSScheduler
     */
    global $HumaanSMSScheduler;

    // TODO: For testing sending scheduled sms
    // $HumaanSMSScheduler->sendAllScheduledSMS();

    // Was a form submitted?
    if (
        !(isset($_POST['send']) && !empty($_POST['send'])) &&
        !(isset($_POST['send_test']) && !empty($_POST['send_test']))
    ) {

        return null;
    }

    // Validate the submitted fields
    $errors = array();

    $archived_course = false;
    if (isset($_POST['archived_course']) && !empty($_POST['archived_course'])) {
        $archived_course = true;
    }

    if (!(isset($_POST['course_id']) && !empty($_POST['course_id']))) {
        $errors['course_id'] = 'Please select a course in order to send the SMS to the enrolees.';
    }
    else {
        $course_id = $_POST['course_id'];
    }

    if (!(isset($_POST['content']) && !empty($_POST['content']))) {
        $errors['content'] = 'Please provide content for the SMS.';
    }
    else {
        $content = stripslashes($_POST['content']);
    }

    if (isset($_POST['send_test']) && !empty($_POST['send_test'])) {
        if (!(isset($_POST['test_sms_to']) && !empty($_POST['test_sms_to']))) {
            $errors['content'] = 'Please provide a test contact number to send the SMS to.';
        }
    }

    // If errors, stop
    if (!empty($errors)) {

        return '<div id="message" class="notice notice-error"><p>' . implode('<br>', $errors) . '</p></div>';
    }

    // Store the list of recipients
    $recipients = array();

    // Is the sms a test?
    if (isset($_POST['send_test']) && !empty($_POST['send_test'])) {

        $recipients[] = array(
            'mobile_number' => $_POST['test_sms_to'],
            'full_name'    => 'Test',
        );
    }
    // Is it a real bulk sms?
    else {

        if ($archived_course) {

            $sql = "SELECT
                    e.phone,
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
                    e.phone,
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
                'mobile_number'         => $enrolee->phone,
                'full_name'    => join(' ', [$enrolee->first_name, $enrolee->last_name]),
            );
        }
    }

    // Loop through the recipients and send the sms
    if (!empty($recipients)) {

        $template_id = $HumaanSMSScheduler->addTemplate(array(
            'course_id'     => $course_id,
            'content'       => $content,
        ));

        if (!$template_id) {

            return '<div id="message" class="notice notice-error"><p>SMS template could not be saved to the database.</p></div>';
        }

        foreach ($recipients as $recipient) {

            $recipient_id = $HumaanSMSScheduler->addRecipient(array(
                'template_id'   => $template_id,
                'mobile_number' => $recipient['mobile_number'],
                'full_name'     => $recipient['full_name'],
                'status'        => 'pending'
            ));
        }
    }

    return '<div id="message" class="notice notice-success"><p>SMS were successfully scheduled.</p></div>';
}