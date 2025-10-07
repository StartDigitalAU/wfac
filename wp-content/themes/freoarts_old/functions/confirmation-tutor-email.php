<?php

/**
 * ConfirmationTutorEmail
 *
 * @package FreoArts
 * @author  Cullen W.
 */

require_once TEMPLATEPATH . '/functions/EnrolmentsFactory.php';

class ConfirmationTutorEmail
{

    protected $EnrolmentsFactory;

    public function __construct()
    {
        $this->initHooks();
    }

    protected function initHooks()
    {
        add_action('woocommerce_order_status_completed', array($this, 'sendEmail'));
    }

    public function sendEmail($order_id)
    {
        $order = wc_get_order($order_id);
        if (!$order) return false;

        $items = $order->get_items();

        if (empty($items)) return false;

        // Init EnrolmentsFactory
        $this->EnrolmentsFactory = new EnrolmentsFactory();
        $this->EnrolmentsFactory->init();

        // Get data
        $data = $this->getData($items);

        if (empty($data)) return false;

        $emails = $this->formatEmail($data);

        if (! $emails) return false;

        $sent = false;

        foreach ($emails as $email) {
            $sent = wp_mail($email['to'], $email['subject'], $email['message'], $email['headers']);
        }

        return $sent;
    }

    private function getData($items)
    {
        $data = array();

        // Loop through each item to get product IDs
        foreach ($items as $item) {
            $product_id = $item->get_product_id();
            $product = wc_get_product($product_id);

            $minimum_students = get_field('minimum_count', $product_id) ?: 5;
            // Check if product exists
            if (! $product) continue;

            // Check if product type is 'course'
            if ($product->get_type() !== 'course') continue;

            // Get tutor ID
            $tutor_id = get_field('tutor', $product_id);

            // Check if tutor ID exists
            if (! $tutor_id) continue;

            $tutor_email = get_field('email', $tutor_id);

            if (empty($tutor_email)) continue;

            $dataToAdd = array(
                'tutor_name' => get_the_title($tutor_id),
                'tutor_email' => $tutor_email,
                'course_id' => $product_id,
                'course_title' => $product->get_title(),
                'students' => array(),
            );

            $results = $this->EnrolmentsFactory->fetchEntries(
                array(
                    'course_id = ' . $product_id,
                    'is_archived = 0',
                    'trashed != 1'
                )
            );

            if (empty($results)) continue;

            $results_count = count($results);

            if ($results_count < $minimum_students) continue;

            $dataToAdd['minimum_count'] = $minimum_students;

            foreach ($results as $result) {
                $dataToAdd['students'][] = array(
                    'name' => $result->first_name . ' ' . $result->last_name,
                    'email' => $result->email,
                    'phone' => $result->phone,
                    'special_requirements' => $result->special_requirements
                );
            }

            $data[] = $dataToAdd;
        }

        return $data;
    }

    private function formatEmail($data)
    {
        if (empty($data)) {
            return false;
        }

        $emails = [];

        // Define email template once
        $template_start = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2>Course Confirmation</h2>
            <p>Dear %s,</p>
            <p>We are pleased to inform you that your course <strong>%s</strong> has reached the minimum enrollment requirement of %s students.</p>
            <h3>Student Information</h3>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Name</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Email</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Phone</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Special Requirements</th>
            </tr>';

        $template_end = '</table>
            <p>Please review the student information and note any special requirements.</p>
            <p>If you have any questions or need further information, please contact us.</p>
            <p>Thank you for teaching with Fremantle Arts Centre.</p>
            <p>Kind regards,<br>The Fremantle Arts Centre Team</p>
            </div>';

        foreach ($data as $course) {
            $subject = 'Course Confirmation: ' . $course['course_title'] . ' has reached minimum enrollment';

            // Format the start of the message
            $message = sprintf(
                $template_start,
                esc_html($course['tutor_name']),
                esc_html($course['course_title']),
                esc_html($course['minimum_count'])
            );

            // Build student rows
            $student_rows = '';
            foreach ($course['students'] as $student) {
                $student_rows .= sprintf(
                    '<tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    </tr>',
                    esc_html($student['name']),
                    esc_html($student['email']),
                    esc_html($student['phone']),
                    esc_html($student['special_requirements'])
                );
            }

            // Complete the message
            $message .= $student_rows . $template_end;

            $headers = array('Content-Type: text/html; charset=UTF-8');

            $emails[] = array(
                'to' => $course['tutor_email'],
                'subject' => $subject,
                'message' => $message,
                'headers' => $headers
            );
        }

        return $emails;
    }
}

$ConfirmationTutorEmail = new ConfirmationTutorEmail();
