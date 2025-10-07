<?php

/****************************************************
 *
 * COURSE MANAGEMENT
 *
 ****************************************************/

include_once(TEMPLATEPATH . '/functions/course-management-course-enrolments.php');
include_once(TEMPLATEPATH . '/functions/course-management-course-overview.php');
include_once(TEMPLATEPATH . '/functions/course-management-bulk-email.php');
include_once(TEMPLATEPATH . '/functions/course-management-bulk-email-list.php');

include_once(TEMPLATEPATH . '/functions/course-management-bulk-sms.php');
include_once(TEMPLATEPATH . '/functions/course-management-bulk-sms-list.php');

/**
 * Add Courses Management admin menu items
 *
 */

function add_admin_course_management_pages()
{

    add_menu_page(
        'Course Management',
        'Courses',
        'course_management_main', // Capability
        'course-management-main',
        'view_admin_course_management',
        'dashicons-book',
        190
    );

    add_submenu_page(
        'course-management-main',
        'Courses Overview',
        'Courses Overview',
        'course_management_enrolments', // Capability
        'course-management-course-overview',
        'view_admin_course_management_course_overview'
    );


    add_submenu_page(
        'course-management-main',
        'Course Info & Enrolments',
        'Info & Enrolments',
        'course_management_enrolments', // Capability
        'course-management-course-enrolments',
        'view_admin_course_management_course_enrolments'
    );

    add_submenu_page(
        'course-management-main',
        'Bulk Email Enrolees',
        'Bulk Email Enrolees',
        'course_management_bulk_email', // Capability
        'course-management-bulk-email',
        'view_admin_course_management_bulk_email'
    );

    add_submenu_page(
        'course-management-main',
        'Sent Email List',
        'Sent Email List',
        'course_management_main', // Capability
        'course-management-sent-email-list',
        'view_admin_course_management_sent_email_list'
    );

    add_submenu_page(
        'course-management-main',
        'Bulk SMS Enrolees',
        'Bulk SMS Enrolees',
        'course_management_bulk_email', // Capability
        'course-management-bulk-sms',
        'view_admin_course_management_bulk_sms'
    );

    add_submenu_page(
        'course-management-main',
        'Sent SMS List',
        'Sent SMS List',
        'course_management_main', // Capability
        'course-management-sent-sms-list',
        'view_admin_course_management_sent_sms_list'
    );
}
add_action('admin_menu', 'add_admin_course_management_pages');

/**
 * Display the Course Management page list
 *
 */
function view_admin_course_management()
{

?>
    <div class="wrap">

        <h1>Course Management</h1>

        <ul>
            <li>
                <a href="<?php echo admin_url('admin.php?page=course-management-course-enrolments'); ?>" title="Course Enrolments">
                    Course Enrolments
                </a>
            </li>
            <li>
                <a href="<?php echo admin_url('admin.php?page=course-management-bulk-email'); ?>" title="Bulk Email">
                    Bulk Email Enrolees
                </a>
            </li>
            <li>
                <a href="<?php echo admin_url('admin.php?page=course-management-bulk-email-list'); ?>" title="Sent Email List">
                    Sent Email List
                </a>
            </li>
        </ul>

    </div>
<?php
}

/****************************************************
 *
 * export course data for FAC eCommerce site
 *
 ****************************************************/

function export_courses()
{
    global $wp;

    $courses_by_tutor_id = array();

    // get courses by selecting products with a tutor value
    $courses = new WP_Query(array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'course'
            )
        ),
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'tutor',
                'compare' => 'EXISTS',
            ),
        )
    ));

    foreach ($courses->posts as $course) {
        $fields = get_fields($course->ID);
        $tutor = $fields['tutor'];
        $taxonomy = 'course_adults_category';
        if ($fields['is_kids_course']) {
            $taxonomy = 'course_kids_category';
        }
        $terms = get_the_terms($course->ID, $taxonomy);
        $_course = array(
            'id' => $course->ID,
            'tutor' => $tutor,
            'title' => $course->post_title,
            'permalink' => get_the_permalink($course->ID),
            'difficulties' => $fields['difficulty'],
            'terms' => array_map(function ($t) {
                return $t->name;
            }, $terms),
            'image' => get_resized_image($fields['hero_image'], 'course', ifne($GLOBALS, 'default_img')),
            'summary' => $fields['summary'],
        );
        if (!array_key_exists($tutor, $courses_by_tutor_id)) {
            $courses_by_tutor_id[$tutor] = array();
        }
        $courses_by_tutor_id[$tutor][] = $_course;
    }

    return $courses_by_tutor_id;
}
