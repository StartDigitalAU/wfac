<?php

/****************************************************
 *
 * Archived Courses
 *
 ****************************************************/

$ArchivedCoursesFactory = null;

/**
 * Initialise the Archived Courses class
 *
 */
function freoarts_create_archived_courses_table()
{

    global $ArchivedCoursesFactory;

    $ArchivedCoursesFactory = new ArchivedCoursesFactory();
    $ArchivedCoursesFactory->init();
}
add_action('init', 'freoarts_create_archived_courses_table', 1);

/**
 * Add Archive button to the Product edit screen
 *
 */
function freoarts_add_archive_button_to_product_edit()
{
    global $post;

    $start_date = get_field('start_date', $post->ID);

    if (!empty($start_date)) {

        echo '<a class="button" style="float: left; margin-top: 10px;" href="' . admin_url('admin-post.php?action=archived_courses_archive_course&course_id=' . $post->ID) . '" title="Archive Course">Archive Course</a>';
    }
}
add_action('post_submitbox_minor_actions', 'freoarts_add_archive_button_to_product_edit');

/**
 * Add archive quick link to the Products list
 *
 * @param $actions
 * @param WP_Post $post
 * @return mixed
 */
function freoarts_archived_courses_row_actions($actions, WP_Post $post)
{

    if ($post->post_type == 'product') {

        $start_date = get_field('start_date', $post->ID);

        if (!empty($start_date)) {

            $actions['freoarts_archive_course'] = '<a href="' . admin_url('admin-post.php?action=archived_courses_archive_course&course_id=' . $post->ID) . '">Archive</a>';
        }
    }

    return $actions;
}
add_filter('post_row_actions', 'freoarts_archived_courses_row_actions', 10, 2);

/**
 * Get a list of courses to be archived, and then archive them
 *
 */
function auto_archive_courses()
{

    global $wpdb;

    $sql = "SELECT
        p.ID,
        pm1.meta_value AS start_date

    FROM wp_posts AS p

    INNER JOIN wp_postmeta AS pm1
    ON pm1.post_id = p.ID
    AND pm1.meta_key = 'start_date'
    AND pm1.meta_value != ''
    AND pm1.meta_value < '" . date('Ymd', strtotime('- 15 days')) . "'

    WHERE p.post_type = 'product'
    LIMIT 20
    ";

    $results = $wpdb->get_results($sql, OBJECT);

    if (!empty($results)) {

        foreach ($results as $course) {

            archive_course($course->ID);
        }
    }
}

/**
 * Archive the specified course
 *
 * @param $course_id
 * @return int
 */
function archive_course($course_id)
{

    global $wpdb;

    $product = wc_get_product($course_id);
    $fields = get_fields($course_id);

//    error_log(var_export($fields, true));
//    error_log(var_export($product, true));

    $sql = "SELECT * FROM wp_enrolments WHERE course_id = " . $course_id . " AND is_archived = 0 AND trashed != 1";
    $enrolments = $wpdb->get_results($sql, ARRAY_A);

    $category_names = array();

    if (!ifne($fields, 'is_kids_course')) {

        if (!empty($fields['adults_category'])) {

            foreach ($fields['adults_category'] as $adult_category_id) {

                $term = get_term($adult_category_id);
                $category_names[] = $term->name;
            }
        }
    }
    else {

        if (!empty($fields['kids_category'])) {
            foreach ($fields['kids_category'] as $kid_category_id) {

                $term = get_term($kid_category_id);
                $category_names[] = $term->name;
            }
        }
    }

    $term_value = '';
    $term = ifne($fields, 'term');
    if (isset($term['term']) && isset($term['year'])) {
        $term_value = "{$term['term']} {$term['year']}";
    }

    $data = array(
        'course_id' => $course_id,
        'course_title' => $product->get_name(),
        'post_content' => $product->get_description(),
        'price' => $product->get_price(),
        'cost' => ifne($fields, 'course_cost'),
        'qty_available' => $product->get_stock_quantity() + count($enrolments),
        'qty_sold' => count($enrolments),
        'is_kids_course' => ifne($fields, 'is_kids_course'),
        'categories' => implode(', ', $category_names),
        'release_date' => date('Y-m-d H:i:s', strtotime(ifne($fields, 'release_date'))),
        'start_date' => date('Y-m-d H:i:s', strtotime(ifne($fields, 'start_date'))),
        'end_date' => date('Y-m-d H:i:s', strtotime(ifne($fields, 'end_date'))),
        'term' => $term_value,
        'summary' => ifne($fields, 'summary'),
        'difficulty' => implode(', ', ifne($fields, 'difficulty', array())),
        'tutor_id' => ifne($fields, 'tutor'),
        'tutor_name' => get_the_title(ifne($fields, 'tutor')),
        'tutor_email' => get_field('email_address', ifne($fields, 'tutor')),
        'duration' => ifne($fields, 'duration'),
        'hero_image' => ifne($fields, 'hero_image'),
        'info_image' => ifne($fields, 'info_image'),
        'created_at' => date('Y-m-d H:i:s')
    );

    // Save archived course details to the database
    $wpdb->insert(
        'wp_archived_courses',
        $data
    );

    $archived_course_id = $wpdb->insert_id;

    // Update the enrolments to set them as archived
    $wpdb->update(
        'wp_enrolments',
        array(
            'archived_course_id' => $archived_course_id,
            'is_archived' => 1
        ),
        array(
            'course_id' => $course_id,
            'is_archived' => 0
        )
    );

    // Update the course product to reset everything

    wc_update_product_stock($product, $product->get_stock_quantity() + count($enrolments), 'set');

    $post = array(
        'ID' => $course_id,
        'post_status' => 'draft'
    );
    wp_update_post($post);

    update_field('release_date', null, $course_id);
    update_field('start_date', null, $course_id);
    update_field('end_date', null, $course_id);

    return $archived_course_id;
}
