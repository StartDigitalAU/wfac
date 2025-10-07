<?php

/****************************************************
 *
 * COURSE MANAGEMENT - COURSE OVERVIEW
 *
 ****************************************************/


function view_admin_course_management_course_overview()
{
    $courses = get_active_courses();
?>
    <div class="wrap">
        <h1>Course Overview</h1>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Course Title</th>
                    <th>Tutor</th>
                    <th>Day</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Enrolments</th>
                    <th>Capacity</th>
                    <th>Percentage Full</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total_enrolments = 0;
                $total_courses = 0;
                foreach ($courses as $course) {
                    $course_id = $course->ID;
                    $course_title = $course->post_title;
                    $raw_start_date = get_field('start_date', $course_id);
                    $raw_end_date = get_field('end_date', $course_id);

                    $course_end_date = DateTime::createFromFormat('Ymd', $raw_end_date)->format('d/m/y');

                    $date_obj = DateTime::createFromFormat('Ymd', $raw_start_date);
                    $course_day = $date_obj->format('l');
                    $course_start_date = $date_obj->format('d/m/y');

                    $tutor = get_field('tutor', $course_id);

                    $stock_quantity = get_post_meta($course_id, '_stock', true);
                    $stock_quantity = $stock_quantity !== '' ? intval($stock_quantity) : 0;
                    

                    $tutor_obj = get_post($tutor);
                    if ($tutor_obj) {
                        $tutor_name = $tutor_obj->post_title;
                    } else {
                        $tutor_name = 'Unknown';
                    }


                    $enrolments = get_course_enrolments($course_id);
                    $enrolment_count = count($enrolments);
                    $capacity = $stock_quantity + $enrolment_count;

                    $percentage = ($enrolment_count / $capacity) * 100;
                    $percentage = round($percentage, 0);
                    $total_enrolments += $enrolment_count;
                    $total_courses++;
                ?>
                    <tr>
                        <td><?php echo esc_html($course_title); ?></td>
                        <td><?php echo esc_html($tutor_name); ?></td>
                        <td><?php echo esc_html($course_day); ?></td>
                        <td><?php echo esc_html($course_start_date); ?></td>
                        <td><?php echo esc_html($course_end_date); ?></td>
                        <td><?php echo esc_html($enrolment_count); ?></td>
                        <td><?php echo esc_html($capacity);?></td>
                        <td><?php echo esc_html($percentage);?>%</td>
                    </tr>
                <?php
                }
                ?>
                <tr class="summary-row" style="font-weight: bold; background-color: #f0f0f1;">
                    <td>Total Courses: <?php echo esc_html($total_courses); ?></td>
                    <td></td>
                    <td></td>
                    <td>Total Enrolments: <?php echo esc_html($total_enrolments); ?></td>

                </tr>
            </tbody>
        </table>
    </div>
<?php
}

function get_active_courses()
{

    $current_date = date('Y-m-d');
    $args = array(
        'posts_per_page'    => -1,
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'orderby'           => 'meta_value',
        'meta_key'         => 'start_date',
        'order'            => 'ASC',
        'tax_query'         => array(
            array(
                'taxonomy'  => 'product_type',
                'field'     => 'slug',
                'terms'     => 'course'
            )
        ),
        'meta_query'        => array(
            array(
                'key'       => 'start_date',
                'value'     => $current_date,
                'compare'   => '>=',
                'type'      => 'DATE'
            )
        )
    );

    $courses = get_posts($args);

    return $courses;
}

function get_course_enrolments($course_id)
{
    global $wpdb;

    $table_name = $wpdb->prefix . 'enrolments';
    $sql = "SELECT * FROM $table_name WHERE course_id = $course_id";
    $results = $wpdb->get_results($sql);
    return $results;
}
