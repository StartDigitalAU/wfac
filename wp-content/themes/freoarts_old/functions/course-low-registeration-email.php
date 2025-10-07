<?php

/**
 * CourseLowRegistrationEmail
 *
 * Sends alerts for courses with low registration at a set time before the class starts
 * 
 * @package FreoArts
 * @author  Cullen W.
 */

require_once TEMPLATEPATH . '/functions/EnrolmentsFactory.php';

class CourseLowRegistrationEmail
{
    protected $EnrolmentsFactory;

    public function __construct()
    {
        $this->initHooks();
        $this->EnrolmentsFactory = new EnrolmentsFactory();
    }

    public function initHooks()
    {
        if (!wp_next_scheduled('check_course_low_registration')) {
            wp_schedule_event(time(), 'daily', 'check_course_low_registration');
        }

        add_action('check_course_low_registration', array($this, 'checkCoursesForLowRegistration'));

        // Add settings to WooCommerce settings page
        add_filter('woocommerce_general_settings', array($this, 'addLowRegistrationSettings'));

        // Add admin page for testing
        // add_action('admin_menu', array($this, 'addTestPage'));
    }

    /**
     * Add test page to admin menu
     */
    public function addTestPage()
    {
        add_submenu_page(
            'tools.php',                     // Parent slug
            'Test Low Registration Email',   // Page title
            'Test Low Registration Email',   // Menu title
            'manage_options',                // Capability
            'test-low-registration-email',   // Menu slug
            array($this, 'renderTestPage')   // Callback function
        );
    }

    /**
     * Render the test page
     */
    public function renderTestPage()
    {
        $test_results = array();
        $test_run = false;
        $email_sent = false;
        $preview_email = 'cullen@startdigital.com.au';

        // Check if the test button was clicked
        if (isset($_POST['test_low_registration']) && check_admin_referer('test_low_registration_nonce')) {
            $test_run = true;

            // Initialize EnrolmentsFactory if not already initialized
            if (!$this->EnrolmentsFactory) {
                $this->EnrolmentsFactory = new EnrolmentsFactory();
                $this->EnrolmentsFactory->init();
            }

            // Get threshold percentage from settings
            $threshold_percentage = get_option('course_low_registration_threshold', 30);

            $days_before = get_option('course_low_registration_days_before', 1);

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

            if (!empty($courses)) {
                foreach ($courses as $course) {
                    $course_data = $this->processCourse($course->ID, $threshold_percentage);
                    if ($course_data && !empty($course_data)) {
                        $test_results[] = $course_data;
                    }
                }
            }

            // Send preview email if requested
            if (isset($_POST['send_preview']) && !empty($test_results)) {
                $this->sendPreviewEmail($test_results, $preview_email);
                $email_sent = true;
            }
        }

        // Display the test page
?>
        <div class="wrap">
            <h1>Test Low Registration Email</h1>
            <p>This page allows you to test the low registration email functionality without sending actual emails.</p>

            <form method="post" action="">
                <?php wp_nonce_field('test_low_registration_nonce'); ?>
                <p>
                    <label>
                        <input type="checkbox" name="send_preview" value="1" <?php checked(isset($_POST['send_preview'])); ?>>
                        Send preview email to <?php echo esc_html($preview_email); ?>
                    </label>
                </p>
                <p>
                    <input type="submit" name="test_low_registration" class="button button-primary" value="Run Test">
                </p>
            </form>

            <?php if ($email_sent): ?>
                <div class="notice notice-success">
                    <p>Preview email sent to <?php echo esc_html($preview_email); ?></p>
                </div>
            <?php endif; ?>

            <?php if ($test_run): ?>
                <h2>Test Results</h2>
                <?php if (empty($test_results)): ?>
                    <p>No courses with low registration were found.</p>
                <?php else: ?>
                    <p>Found <?php echo count($test_results); ?> courses with low registration:</p>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Start Date</th>
                                <th>Tutor</th>
                                <th>Current Enrollment</th>
                                <th>Total Capacity</th>
                                <th>Filled %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($test_results as $course): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo admin_url('post.php?post=' . $course['course_id'] . '&action=edit'); ?>">
                                            <?php echo esc_html($course['course_title']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo esc_html($course['start_date']); ?></td>
                                    <td><?php echo esc_html($course['tutor_name']); ?></td>
                                    <td><?php echo esc_html($course['current_enrollment']); ?></td>
                                    <td><?php echo esc_html($course['total_capacity']); ?></td>
                                    <td><?php echo esc_html($course['enrollment_percentage']); ?>%</td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            <?php endif; ?>
        </div>
<?php
    }

    /**
     * Send preview email to specified address
     */
    private function sendPreviewEmail($courses, $email)
    {
        if (empty($email) || empty($courses)) return;

        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Send admin summary email
        $subject = '[PREVIEW] Low Registration Alert: Courses Requiring Attention';

        $message = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2>Low Registration Alert (PREVIEW)</h2>
            <p>The following courses have low registration and may require attention:</p>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Course</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Start Date</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Tutor</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Current</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Capacity</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Filled %</th>
                </tr>';

        foreach ($courses as $course) {
            $message .= sprintf(
                '<tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><a href="%s">%s</a></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s%%</td>
                </tr>',
                admin_url('post.php?post=' . $course['course_id'] . '&action=edit'),
                esc_html($course['course_title']),
                esc_html($course['start_date']),
                esc_html($course['tutor_name']),
                esc_html($course['current_enrollment']),
                esc_html($course['total_capacity']),
                esc_html($course['enrollment_percentage'])
            );
        }

        $message .= '</table>
            <p>These courses are currently below the registration threshold but still have time to reach the minimum enrollment.</p>
            <p>To view all course enrollments, <a href="' . admin_url('admin.php?page=course-management-course-enrolments') . '">click here</a>.</p>
            <p>This is a preview of an automated message from the Fremantle Arts Centre course management system.</p>
        </div>';

        // Send admin summary email
        wp_mail($email, $subject, $message, $headers);

        // Send individual course emails
        foreach ($courses as $course) {
            $course_subject = sprintf('[PREVIEW] Low Registration Alert: %s', $course['course_title']);

            $course_message = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2>Low Registration Alert (PREVIEW)</h2>
                <p>Dear ' . esc_html($course['tutor_name']) . ',</p>
                <p>We wanted to inform you that your course <strong>' . esc_html($course['course_title']) . '</strong> scheduled for ' . esc_html($course['start_date']) . ' currently has low enrollment.</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Current Enrollment</th>
                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Total Capacity</th>
                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Filled Percentage</th>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($course['current_enrollment']) . '</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($course['total_capacity']) . '</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($course['enrollment_percentage']) . '%</td>
                    </tr>
                </table>
                <p>While there is still time before the course starts, we wanted to make you aware of the current enrollment status.</p>
                <p>Kind regards,<br>The Fremantle Arts Centre Team</p>
                <p><em>This is a preview email sent to ' . esc_html($email) . ' for testing purposes.</em></p>
            </div>';

            wp_mail($email, $course_subject, $course_message, $headers);
        }
    }

    /**
     * Add low registration alert settings to WooCommerce settings
     */
    public function addLowRegistrationSettings($settings)
    {
        $low_registration_settings = array(
            array(
                'title' => __('Low Registration Alert Settings', 'freoarts'),
                'type' => 'title',
                'desc' => __('Settings for low registration alert emails.', 'freoarts'),
                'id' => 'course_low_registration_settings'
            ),
            array(
                'title' => __('Days Before Start Date', 'freoarts'),
                'desc' => __('Number of days before course start date to check for low enrollment', 'freoarts'),
                'id' => 'course_low_registration_days_before',
                'type' => 'number',
                'default' => '1',
                'desc_tip' => true,
            ),
            array(
                'title' => __('Low Registration Threshold', 'freoarts'),
                'desc' => __('Percentage of total capacity to trigger low registration alert (e.g., 30 means alert when enrollment is below 30% of capacity)', 'freoarts'),
                'id' => 'course_low_registration_threshold',
                'type' => 'number',
                'default' => '30',
                'desc_tip' => true,
            ),
            array(
                'type' => 'sectionend',
                'id' => 'course_low_registration_settings'
            )
        );

        // Insert our settings before the last section end
        $position = count($settings);
        array_splice($settings, $position, 0, $low_registration_settings);

        return $settings;
    }

    /**
     * Check all upcoming courses for low registration
     */
    public function checkCoursesForLowRegistration()
    {
        // Initialize EnrolmentsFactory if not already initialized
        if (!$this->EnrolmentsFactory) {
            $this->EnrolmentsFactory = new EnrolmentsFactory();
            $this->EnrolmentsFactory->init();
        }

        // Get days before setting (default to 10 if not set)
        $days_before = get_option('course_low_registration_days_before', 1);

        // Get threshold percentage (default to 70% if not set)
        $threshold_percentage = get_option('course_low_registration_threshold', 70);

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

        if (empty($courses)) return;


        $low_registration_courses = array();

        // Process each course
        foreach ($courses as $course) {
            $course_data = $this->processCourse($course->ID, $threshold_percentage);
            if (! $course_data ||  empty($course_data)) continue;
            $low_registration_courses[] = $course_data;
        }

        // Send email if there are courses with low registration
        if (!empty($low_registration_courses)) {
            $this->sendLowRegistrationEmail($low_registration_courses);
        }
    }

    /**
     * Process a single course for low registration
     */
    private function processCourse($course_id, $threshold_percentage)
    {
        // Get product
        $product = wc_get_product($course_id);
        if (!$product) return null;

        // Get stock quantity (total capacity)
        $stock_quantity = $product->get_stock_quantity();

        // If no stock is set, return as the class is sold out
        if (!$stock_quantity || $stock_quantity <= 0) return false;

        // Get current enrollment count
        $results = $this->EnrolmentsFactory->fetchEntries(
            array(
                'course_id = ' . $course_id,
                'is_archived = 0',
                'trashed != 1'
            )
        );

        if (empty($results)) return false;

        // Get current enrollment count
        $current_enrollment = count($results);

        // Calculate total capacity
        $total_capacity = $stock_quantity + $current_enrollment;

        // Calculate threshold count based on total stock
        $threshold_count = ceil($total_capacity * ($threshold_percentage / 100));

        // If enrollment is above threshold, return as the class is not at risk of low registration
        if ($current_enrollment > $threshold_count) return false;

        // Get course information
        $course_title = $product->get_title();
        $start_date = get_field('start_date', $course_id);
        $formatted_date = format_acf_date($start_date, 'd F Y');

        // Get tutor information
        $tutor_id = get_field('tutor', $course_id);
        $tutor_name = $tutor_id ? get_the_title($tutor_id) : 'No tutor assigned';
        $tutor_email = $tutor_id ? get_field('email', $tutor_id) : '';

        $enrollment_percentage = round(($current_enrollment / $total_capacity) * 100);

        return array(
            'course_id' => $course_id,
            'course_title' => $course_title,
            'start_date' => $formatted_date,
            'tutor_name' => $tutor_name,
            'tutor_email' => $tutor_email,
            'current_enrollment' => $current_enrollment,
            'total_capacity' => $total_capacity,
            'enrollment_percentage' => $enrollment_percentage
        );
    }

    /**
     * Send low registration email to admin and tutors
     */
    private function sendLowRegistrationEmail($courses)
    {
        $admin_email = 'learning_fac@fremantle.wa.gov.au';
        
        $headers = array('Content-Type: text/html; charset=UTF-8');

        // First, send a summary email to admin
        $subject = 'Low Registration Alert: Courses Requiring Attention';

        $message = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2>Low Registration Alert</h2>
            <p>The following courses have low registration and may require attention:</p>
            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Course</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Start Date</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Tutor</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Current</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Capacity</th>
                    <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Filled %</th>
                </tr>';

        foreach ($courses as $course) {
            $message .= sprintf(
                '<tr>
                    <td style="padding: 8px; border: 1px solid #ddd;"><a href="%s">%s</a></td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s</td>
                    <td style="padding: 8px; border: 1px solid #ddd;">%s%%</td>
                </tr>',
                admin_url('post.php?post=' . $course['course_id'] . '&action=edit'),
                esc_html($course['course_title']),
                esc_html($course['start_date']),
                esc_html($course['tutor_name']),
                esc_html($course['current_enrollment']),
                esc_html($course['total_capacity']),
                esc_html($course['enrollment_percentage'])
            );
        }

        $message .= '</table>
            <p>These courses are currently below the registration threshold but still have time to reach the minimum enrollment.</p>
            <p>To view all course enrollments, <a href="' . admin_url('admin.php?page=course-management-course-enrolments') . '">click here</a>.</p>
            <p>This is an automated message from the Fremantle Arts Centre course management system.</p>
        </div>';

        if ($admin_email) {
            wp_mail($admin_email, $subject, $message, $headers);
        }

        // Now send individual emails for each course to the respective tutors
        foreach ($courses as $course) {
            if (empty($course['tutor_email'])) continue;

            $course_subject = sprintf('Low Registration Alert: %s', $course['course_title']);

            $course_message = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <h2>Low Registration Alert</h2>
                <p>Dear ' . esc_html($course['tutor_name']) . ',</p>
                <p>We wanted to inform you that your course <strong>' . esc_html($course['course_title']) . '</strong> scheduled for ' . esc_html($course['start_date']) . ' currently has low enrollment.</p>
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr style="background-color: #f2f2f2;">
                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Current Enrollment</th>
                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Total Capacity</th>
                        <th style="padding: 8px; text-align: left; border: 1px solid #ddd;">Filled Percentage</th>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($course['current_enrollment']) . '</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($course['total_capacity']) . '</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">' . esc_html($course['enrollment_percentage']) . '%</td>
                    </tr>
                </table>
                <p>While there is still time before the course starts, we wanted to make you aware of the current enrollment status.</p>
                <p>Thank you for your continued support and dedication to teaching at Fremantle Arts Centre.</p>
                <p>Kind regards,<br>The Fremantle Arts Centre Team</p>
            </div>';

            wp_mail($course['tutor_email'], $course_subject, $course_message, $headers);
        }
    }
}

// Initialize the class
$CourseLowRegistrationEmail = new CourseLowRegistrationEmail();
