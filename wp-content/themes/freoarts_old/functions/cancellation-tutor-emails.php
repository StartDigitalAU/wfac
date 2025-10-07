<?php

/**
 * CancellationTutorEmail 
 * 
 * WORK IN PROGRESS NOT ENABLED YET
 *
 * @package FreoArts
 * @author  Cullen W.
 */

require_once TEMPLATEPATH . '/functions/EnrolmentsFactory.php';

class CancellationTutorEmail
{
    protected $EnrolmentsFactory;

    public function __construct()
    {
        $this->initHooks();
        $this->EnrolmentsFactory = new EnrolmentsFactory();
    }

    public function initHooks()
    {
        // Schedule daily check for courses that don't meet minimum requirements
        if (!wp_next_scheduled('check_course_minimum_requirements')) {
            wp_schedule_event(time(), 'daily', 'check_course_minimum_requirements');
        }

        add_action('check_course_minimum_requirements', array($this, 'checkCoursesForCancellation'));

        // Add settings to WooCommerce settings page
        add_filter('woocommerce_general_settings', array($this, 'addCancellationSettings'));
    }

    /**
     * Add cancellation settings to WooCommerce settings
     */
    public function addCancellationSettings($settings)
    {
        $cancellation_settings = array(
            array(
                'title' => __('Course Cancellation Settings', 'freoarts'),
                'type' => 'title',
                'desc' => __('Settings for automatic course cancellation emails.', 'freoarts'),
                'id' => 'course_cancellation_settings'
            ),
            array(
                'title' => __('Days Before Start Date', 'freoarts'),
                'desc' => __('Number of days before course start date to check minimum enrollment requirements', 'freoarts'),
                'id' => 'course_cancellation_days_before',
                'type' => 'number',
                'default' => '5',
                'desc_tip' => true,
            ),
            array(
                'type' => 'sectionend',
                'id' => 'course_cancellation_settings'
            )
        );

        // Insert our settings before the last section end
        $position = count($settings) - 1;
        array_splice($settings, $position, 0, $cancellation_settings);

        return $settings;
    }

    /**
     * Check all upcoming courses for cancellation
     */
    public function checkCoursesForCancellation()
    {
        // Initialize EnrolmentsFactory if not already initialized
        if (!$this->EnrolmentsFactory) {
            $this->EnrolmentsFactory = new EnrolmentsFactory();
            $this->EnrolmentsFactory->init();
        }

        // Get days before setting (default to 5 if not set)
        $days_before = get_option('course_cancellation_days_before', 5);

        // Get all published course products
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'product',
            'post_status' => 'publish',
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_type',
                    'field' => 'slug',
                    'terms' => 'course'
                )
            ),
            'meta_query' => array(
                array(
                    'key' => 'start_date',
                    'value' => date('Ymd', strtotime('+' . $days_before . ' days')),
                    'compare' => '=',
                    'type' => 'DATE'
                )
            )
        );

        $courses = get_posts($args);

        if (empty($courses)) {
            return;
        }

        // Process each course
        foreach ($courses as $course) {
            $this->processCourse($course->ID);
        }
    }

    /**
     * Process a single course for potential cancellation
     */
    private function processCourse($course_id)
    {
        // Get minimum students required
        $minimum_students = get_field('minimum_count', $course_id) ?: 5;

        // Get current enrollment count
        $results = $this->EnrolmentsFactory->fetchEntries(
            array(
                'course_id = ' . $course_id,
                'is_archived = 0',
                'trashed != 1'
            )
        );

        $current_enrollment = count($results);

        // If enrollment is below minimum, cancel the course
        if ($current_enrollment < $minimum_students) {
            $this->cancelCourse($course_id, $results, $current_enrollment, $minimum_students);
        }
    }

    /**
     * Cancel a course and send notifications
     */
    private function cancelCourse($course_id, $enrollments, $current_enrollment, $minimum_students)
    {
        $product = wc_get_product($course_id);
        if (!$product) return;

        $course_title = $product->get_title();
        $start_date = get_field('start_date', $course_id);
        $formatted_date = format_acf_date($start_date, 'd F Y');

        // Get tutor information
        $tutor_id = get_field('tutor', $course_id);
        $tutor_name = $tutor_id ? get_the_title($tutor_id) : '';
        $tutor_email = $tutor_id ? get_field('email', $tutor_id) : '';

        // Send email to tutor
        if ($tutor_email) {
            $this->sendTutorCancellationEmail($tutor_email, $tutor_name, $course_title, $formatted_date, $current_enrollment, $minimum_students);
        }

        // Send emails to all enrolled students and process refunds
        foreach ($enrollments as $enrollment) {
            // Cancel the enrollment (which will handle refunds)
            freoarts_cancel_enrolment($enrollment->id, 'full');
        }

        // Log the cancellation
        $log_message = sprintf(
            'Course "%s" (ID: %d) automatically cancelled due to insufficient enrollment (%d/%d)',
            $course_title,
            $course_id,
            $current_enrollment,
            $minimum_students
        );

        error_log($log_message);
    }

    /**
     * Send cancellation email to tutor
     */
    private function sendTutorCancellationEmail($email, $name, $course_title, $start_date, $current_enrollment, $minimum_students)
    {
        $subject = sprintf('Course Cancellation: %s', $course_title);

        $message = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2>Course Cancellation Notice</h2>
            <p>Dear ' . esc_html($name) . ',</p>
            <p>We regret to inform you that your course <strong>' . esc_html($course_title) . '</strong> scheduled for ' . esc_html($start_date) . ' has been cancelled due to insufficient enrollment.</p>
            <p>Current enrollment: ' . esc_html($current_enrollment) . '<br>
            Minimum required: ' . esc_html($minimum_students) . '</p>
            <p>All enrolled students have been automatically notified and refunded.</p>
            <p>We apologize for any inconvenience this may cause. Please contact us if you have any questions or would like to discuss rescheduling options.</p>
            <p>Thank you for your understanding.</p>
            <p>Kind regards,<br>The Fremantle Arts Centre Team</p>
        </div>';

        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($email, $subject, $message, $headers);
    }

    /**
     * Send cancellation email to student
     */
    private function sendStudentCancellationEmail($email, $name, $course_title, $start_date, $current_enrollment, $minimum_students)
    {
        $subject = sprintf('Course Cancellation: %s', $course_title);

        $message = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2>Course Cancellation Notice</h2>
            <p>Dear ' . esc_html($name) . ',</p>
            <p>We regret to inform you that the course <strong>' . esc_html($course_title) . '</strong> scheduled for ' . esc_html($start_date) . ' has been cancelled due to insufficient enrollment.</p>
            <p>Unfortunately, we did not reach the minimum number of students required to run this course effectively.</p>
            <p>A full refund has been processed and will be returned to your original payment method. Please allow 3-5 business days for the refund to appear in your account.</p>
            <p>We apologize for any inconvenience this may cause. Please visit our website to explore other available courses that might interest you.</p>
            <p>Thank you for your understanding.</p>
            <p>Kind regards,<br>The Fremantle Arts Centre Team</p>
        </div>';

        $headers = array('Content-Type: text/html; charset=UTF-8');

        wp_mail($email, $subject, $message, $headers);
    }
}

// Initialize the class
$CancellationTutorEmail = new CancellationTutorEmail();
