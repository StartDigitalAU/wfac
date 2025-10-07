<?php

/****************************************************
 *
 * REPORTING - COURSES
 *
 ****************************************************/

/**
 * Retrieve the active courses
 *
 * @param  null    $date_from
 * @param  null    $date_to
 * @param  string  $is_kids_course
 *
 * @return array|object|null
 */
function get_admin_report_archived_courses($date_from = null, $date_to = null, $is_kids_course = 'all')
{
    global $wpdb;

    $sql_where_date_from      = ($date_from) ? " AND ac.start_date >= '{$date_from} 00:00:00'" : '';
    $sql_where_date_to        = ($date_to) ? " AND ac.start_date <= '{$date_to} 00:00:00'" : '';

    if ($is_kids_course === 'adults') {
        $sql_where_is_kids_course = " AND ac.is_kids_course = '0'";
    } else if ($is_kids_course === 'kids') {
        $sql_where_is_kids_course = " AND ac.is_kids_course = '1'";
    } else {
        $sql_where_is_kids_course = '';
    }

    $sql = "SELECT
                ac.course_id AS course_id,
                ac.course_title AS course_name,
                '1' AS is_archived,
                ac.tutor_name AS tutor,
                ac.tutor_email AS tutor_email,
                ac.start_date AS start_date,
                ac.end_date AS end_date,
                ac.duration AS duration,
                ac.difficulty AS difficulty,
                ac.price AS regular_price,
                ac.cost AS cost,
                ac.is_kids_course AS is_kids_course,
                ac.qty_available AS capacity_remaining,
                ac.post_content AS content,
                ac.qty_sold AS capacity_sold,
                ac.categories AS categories
                
            FROM wp_archived_courses AS ac
            
            WHERE 1 = 1
            {$sql_where_date_from}
            {$sql_where_date_to}
            {$sql_where_is_kids_course}
            ";

    return $wpdb->get_results($sql, OBJECT);
}

/**
 * Retrieve the active courses
 *
 * @param  null    $date_from
 * @param  null    $date_to
 * @param  string  $is_kids_course
 *
 * @return array|object|null
 */
function get_admin_report_active_courses($date_from = null, $date_to = null, $is_kids_course = 'all')
{
    global $wpdb;

    $sql_where_date_from        = ($date_from) ? " AND pm1.meta_value >= " . str_replace('-', '', $date_from) : '';
    $sql_where_date_to          = ($date_to) ? " AND pm1.meta_value <= " . str_replace('-', '', $date_to) : '';

    if ($is_kids_course === 'adults') {
        $sql_where_is_kids_course = " AND pm6.meta_value = '0'";
    } else if ($is_kids_course === 'kids') {
        $sql_where_is_kids_course = " AND pm6.meta_value = '1'";
    } else {
        $sql_where_is_kids_course = '';
    }

    $sql = "SELECT
                p.ID AS course_id,
                p.post_title AS course_name,
                '0' AS is_archived,
                p_tutor.post_title AS tutor,
                pm1.meta_value AS start_date,
                pm2.meta_value AS end_date,
                pm3.meta_value AS duration,
                pm4.meta_value AS difficulty,
                pm5.meta_value AS regular_price,
                pm6.meta_value AS is_kids_course,
                pm7.meta_value AS capacity_remaining,
                pm9.meta_value AS cost,
                pm10.meta_value AS tutor_email,
                pm11.meta_value AS additional_fields,
                p.post_content AS content,
            
                (SELECT COUNT(*) FROM wp_enrolments WHERE course_id = p.ID AND trashed != 1 AND is_archived != 1) AS capacity_sold,
            
                (SELECT
                    GROUP_CONCAT(t.name SEPARATOR ', ')
                    FROM wp_term_relationships AS tr
            
                    INNER JOIN wp_term_taxonomy AS tt
                    ON tt.term_taxonomy_id = tr.term_taxonomy_id
            
                    INNER JOIN wp_terms AS t
                    ON t.term_id = tt.term_id
            
                    WHERE object_id = p.ID
                    AND (tt.taxonomy = 'course_adults_category' OR tt.taxonomy = 'course_kids_category')
                ) AS categories
            
            FROM wp_posts AS p
            
            
            LEFT JOIN wp_postmeta AS pm1
            ON pm1.post_id = p.ID
            AND pm1.meta_key = 'start_date'
            
            LEFT JOIN wp_postmeta AS pm2
            ON pm2.post_id = p.ID
            AND pm2.meta_key = 'end_date'
            
            LEFT JOIN wp_postmeta AS pm3
            ON pm3.post_id = p.ID
            AND pm3.meta_key = 'duration'
            
            LEFT JOIN wp_postmeta AS pm4
            ON pm4.post_id = p.ID
            AND pm4.meta_key = 'difficulty'
            
            LEFT JOIN wp_postmeta AS pm5
            ON pm5.post_id = p.ID
            AND pm5.meta_key = '_regular_price'
            
            LEFT JOIN wp_postmeta AS pm6
            ON pm6.post_id = p.ID
            AND pm6.meta_key = 'is_kids_course'
            
            LEFT JOIN wp_postmeta AS pm7
            ON pm7.post_id = p.ID
            AND pm7.meta_key = '_stock'
            
            LEFT JOIN wp_postmeta AS pm8
            ON pm8.post_id = p.ID
            AND pm8.meta_key = 'tutor'
            
            LEFT JOIN wp_postmeta AS pm9
            ON pm9.post_id = p.ID
            AND pm9.meta_key = 'course_cost'
            
            LEFT JOIN wp_posts AS p_tutor
            ON pm8.meta_value = p_tutor.ID
            
            LEFT JOIN wp_postmeta AS pm10
            ON pm10.post_id = p_tutor.ID
            AND pm10.meta_key = 'email_address'
            
            LEFT JOIN wp_postmeta AS pm11
            ON pm11.post_id = p.ID
            AND pm11.meta_key = 'additional_fields'
            
            WHERE p.post_type = 'product'
            {$sql_where_date_from}
            {$sql_where_date_to}
            {$sql_where_is_kids_course}";

    return $wpdb->get_results($sql, OBJECT);
}

/**
 * Retrieve list of courses
 *
 * @return array|null|object
 */
function get_admin_report_courses()
{
    $date_from = !empty($_GET['date_from']) ? $_GET['date_from'] : false;
    $date_to = !empty($_GET['date_to']) ? $_GET['date_to'] : false;
    $is_kids_course = !empty($_GET['type']) ? $_GET['type'] : 'all';

    if (!$date_from && !$date_to) {
        return [];
    }

    $active_courses = get_admin_report_active_courses($date_from, $date_to, $is_kids_course);
    $archived_courses = get_admin_report_archived_courses($date_from, $date_to, $is_kids_course);

    return array_merge($active_courses, $archived_courses);
}

/**
 * Display the admin Courses report
 *
 */
function view_admin_report_courses()
{

    $date_from = '';
    if (!empty($_GET['date_from'])) {
        $date_from = $_GET['date_from'];
    }

    $date_to = '';
    if (!empty($_GET['date_to'])) {
        $date_to = $_GET['date_to'];
    }

    $type = '';
    if (!empty($_GET['type'])) {
        $type = $_GET['type'];
    }

    $courses = get_admin_report_courses();
    ?>
    <div class="wrap">

        <h1>Courses Report</h1>

        <form id="posts-filter" action="" method="get">

            <input type="hidden" name="page" value="reports-courses" />

            <div class="tablenav top">
                <div class="alignleft actions">

                    <input type="text" name="date_from" class="date-picker" placeholder="From Date" value="<?php echo $date_from; ?>"/>
                    <input type="text" name="date_to" class="date-picker" placeholder="To Date" value="<?php echo $date_to; ?>"/>

                    <select name="type" class="postform">
                        <option value="">All Types</option>
                        <option value="adults"<?php if ($type == 'adults') { echo ' selected="selected"'; } ?>>Adults</option>
                        <option value="kids"<?php if ($type == 'kids') { echo ' selected="selected"'; } ?>>Kids</option>
                    </select>

                    <input class="button" type="submit" value="Filter">
                </div>

                <div class="alignright actions">
                    <?php
                    $courses_csv_url = admin_url('admin-post.php?action=print_report_courses.csv');
                    $course_enrolments_csv_url = admin_url('admin-post.php?action=print_report_courses_enrolments.csv');

                    $url_params = isset($_GET['date_from']) ? '&date_from=' . $_GET['date_from']: '';
                    $url_params .= isset($_GET['date_to']) ? '&date_to=' . $_GET['date_to']: '';
                    $url_params .= isset($_GET['type']) ? '&type=' . $_GET['type']: '';

                    ?>
                    <a href="<?php echo $courses_csv_url . $url_params; ?>" class="button">Export Filtered Courses CSV</a>
                    <a href="<?php echo $course_enrolments_csv_url . $url_params; ?>" class="button">Export Enrolments for Filtered Courses CSV</a>
                </div>
            </div>

            <div class="acf-field acf-field-repeater">

                <?php if (count($courses)) { ?>
                    <div class="acf-input">
                        <table class="acf-table">
                            <thead>
                            <tr>
                                <th class="acf-row-handle"></th>
                                <th class="acf-th acf-th-text">Course Name</th>
                                <th class="acf-th acf-th-text">Archived</th>
                                <th class="acf-th acf-th-text">Tutor</th>
                                <th class="acf-th acf-th-text">Categories</th>
                                <th class="acf-th acf-th-text">Type</th>
                                <th class="acf-th acf-th-text">Start Date</th>
                                <th class="acf-th acf-th-text">Capacity</th>
                                <th class="acf-th acf-th-text">Sold</th>
                                <th class="acf-th acf-th-text">Actions</th>
                                <th class="acf-th acf-th-text">Additional</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            $capacity_total = 0;
                            $sold_total = 0;
                            ?>
                            <?php foreach ($courses as $course) { ?>
                                <tr class="acf-row">
                                    <td class="acf-row-handle order">
                                        <span><?php echo $i; ?></span>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php if ($course->is_archived) { ?>
                                            <a href="<?php echo admin_url( 'admin.php?page=archived_courses_view&entry=' . $course->course_id ); ?>" title="<?php echo $course->course_name; ?>">
                                                <?php echo $course->course_name; ?>
                                            </a>
                                        <?php } else { ?>
                                            <a href="<?php echo get_edit_post_link($course->course_id); ?>" title="<?php echo $course->course_name; ?>">
                                                <?php echo $course->course_name; ?>
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $course->is_archived; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php
                                        echo $course->tutor;
                                        if (!empty($course->tutor_email)) {
                                            echo " ({$course->tutor_email})";
                                        }
                                        ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $course->categories; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $course->is_kids_course ? 'Kids': 'Adults'; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo date('Y-m-d', strtotime($course->start_date)); ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php
                                        if ($course->is_archived) {
                                            $capacity = (int)$course->capacity_remaining;
                                        } else {
                                            $capacity = (int)$course->capacity_remaining + (int)$course->capacity_sold;
                                        }
                                        $capacity_total += $capacity;
                                        echo $capacity;
                                        ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php
                                        $sold_total += (int) $course->capacity_sold;
                                        echo (int) $course->capacity_sold;
                                        ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php if (!empty($course->capacity_sold)) { ?>
                                            <?php
                                            if ($course->is_archived) {
                                                $url = admin_url('admin-post.php?action=print_course_enrolments.csv&archived_course_id=' . $course->course_id);
                                            }
                                            else {
                                                $url = admin_url('admin-post.php?action=print_course_enrolments.csv&course_id=' . $course->course_id);
                                            }
                                            ?>
                                            <a href="<?php echo $url; ?>" title="Export Enrolment CSV">
                                                Export Enrolments
                                            </a>
                                        <?php } ?>
                                    </td>

                                    <td class="acf-field acf-field-text">
                                    <?php if (!empty($course->additional_fields)) { ?>
                                    <?php } ?>
                                    </td>
                                </tr>
                                <?php $i++; ?>
                            <?php } ?>
                            <tr class="acf-row">
                                <td class="acf-row-handle order">
                                    <span>TOTAL</span>
                                </td>
                                <td class="acf-field acf-field-text">
                                </td>
                                <td class="acf-field acf-field-text">
                                </td>
                                <td class="acf-field acf-field-text">
                                </td>
                                <td class="acf-field acf-field-text">
                                </td>
                                <td class="acf-field acf-field-text">
                                </td>
                                <td class="acf-field acf-field-text">
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php echo $capacity_total; ?>
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php echo $sold_total; ?>
                                </td>
                                <td class="acf-field acf-field-text">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>

                    <p>There were no results for the chosen filter.</p>

                <?php } ?>
            </div>

        </form>

    </div>
    <?php
}

/**
 * CSV export the admin course report
 *
 */
function print_report_csv_courses()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $courses = get_admin_report_courses();

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=course-report.csv');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    fputcsv(
        $output,
        array(
            'Course Name',
            'Tutor',
            'Email',
            'Start Date',
            'End Date',
            'Duration',
            'Difficulty',
            'Regular Price',
            'Member Price',
            'Cost',
            'Type',
            'Capacity',
            'Capacity Remaining',
            'Capacity Sold',
            'Content',
            'Meta',
            'Category 01',
            'Category 02',
            'Category 03',
            'Category 04',
            'Category 05'
        )
    );

    $capacity_total = 0;
    $capacity_sold_total = 0;

    foreach ($courses as $course) {
        $categories = explode(', ', $course->categories);

        $capacity_total += (int) $course->capacity_remaining + (int) $course->capacity_sold;
        $capacity_sold_total += (int) $course->capacity_sold;

        $difficulty = @unserialize($course->difficulty);
        if ($difficulty === false) {
            $difficulty = $course->difficulty;
        } else {
            $difficulty = implode(', ', $difficulty);
        }

//        $tutor = $course->tutor;
//        if (!empty($course->tutor_email)) {
//            $tutor .= " ({$course->tutor_email})";
//        }

        fputcsv(
            $output,
            [
                $course->course_name,
                $course->tutor,
                $course->tutor_email,
                date('Y-m-d', strtotime($course->start_date)),
                date('Y-m-d', strtotime($course->end_date)),
                $course->duration,
                $difficulty,
                $course->regular_price,
                (float) $course->regular_price * 0.9,
                (float) $course->cost,
                ($course->is_kids_course) ? 'Kids' : 'Adults',
                (int) $course->capacity_remaining + (int) $course->capacity_sold,
                (int) $course->capacity_remaining,
                (int) $course->capacity_sold,
                $course->content,
                '',
                isset($categories[0]) ? $categories[0] : '',
                isset($categories[1]) ? $categories[1] : '',
                isset($categories[2]) ? $categories[2] : '',
                isset($categories[3]) ? $categories[3] : '',
                isset($categories[4]) ? $categories[4] : ''
            ]
        );
    }

    fputcsv(
        $output,
        [
            'TOTAL',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            $capacity_total,
            '',
            $capacity_sold_total
        ]
    );
}
add_action('admin_post_print_report_courses.csv', 'print_report_csv_courses');

/**
 * Export CSV of filtered Courses' enrolments
 *
 */
function print_report_csv_courses_enrolments()
{
    global $wpdb;

    if (!current_user_can('manage_options')) {
        return;
    }

    $date_from      = !empty($_GET['date_from']) ? $_GET['date_from'] : false;
    $date_to        = !empty($_GET['date_to']) ? $_GET['date_to'] : false;
    $is_kids_course = !empty($_GET['type']) ? $_GET['type'] : 'all';

    $active_courses = get_report_active_courses_enrolments($date_from, $date_to, $is_kids_course);
    $archived_courses = get_report_archived_courses_enrolments($date_from, $date_to, $is_kids_course);

    $courses = array_merge($active_courses, $archived_courses);

    usort($courses, function($course_1, $course_2) {
        $date_1 = date('Ymd', strtotime($course_1->start_date));
        $date_2 = date('Ymd', strtotime($course_2->start_date));
        return ($date_1 <= $date_2);
    });

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=course-report.csv');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    fputcsv(
        $output,
        [
            'Purchaser / Order',
            'Course',
            'Start Date',
            'Student Name',
            'Age',
            'Contact Number',
            'Email Address',
            'Special Requirements',
            'Member',
            'Regular Price',
            'Member Price',
            'eWAY Transaction Number',
        ]
    );

    if ($courses) {
        foreach ($courses as $course) {
            fputcsv(
                $output,
                [
                    $course->user_nicename . ' - Order #' . $course->order_id,
                    $course->course_name,
                    date('Y-m-d', strtotime($course->start_date)),
                    $course->first_name . ' ' . $course->last_name,
                    $course->is_kids_course ? $course->age : '',
                    $course->phone,
                    $course->email,
                    $course->special_requirements,
                    $course->member_expiry && date('Ymd') <= date('Ymd', strtotime($course->member_expiry))
                        ? 'Yes'
                        : 'No',
                    number_format($course->regular_price, 2),
                    number_format($course->regular_price * 0.9, 2),
                    $course->transaction_id,
                ]
            );
        }
    }
}
add_action('admin_post_print_report_courses_enrolments.csv', 'print_report_csv_courses_enrolments');

function get_report_active_courses_enrolments($date_from, $date_to, $is_kids_course)
{
    global $wpdb;

    $sql_where_date_from = ($date_from) ? " AND pm1.meta_value >= " . str_replace('-', '', $date_from) : '';
    $sql_where_date_to   = ($date_to) ? " AND pm1.meta_value <= " . str_replace('-', '', $date_to) : '';

    $sql_where_is_kids_course = '';
    if ($is_kids_course === 'adults') {
        $sql_where_is_kids_course = " AND pm3.meta_value = '0'";
    } else if ($is_kids_course === 'kids') {
        $sql_where_is_kids_course = " AND pm3.meta_value = '1'";
    }

    $sql = "SELECT
                p.ID AS course_id,
                p.post_title AS course_name,
                '0' AS is_archived,
                pm1.meta_value AS start_date,
                pm2.meta_value AS end_date,
                pm3.meta_value AS is_kids_course,
                pm4.meta_value AS regular_price,
                e.first_name AS first_name,
                e.last_name AS last_name,
                e.age AS age,
                e.phone AS phone,
                e.email AS email,
                e.special_requirements AS special_requirements,
                o.ID AS order_id,
                om1.meta_value AS transaction_id,
	            u.user_nicename AS user_nicename,
	            um1.meta_value AS member_expiry
            
            FROM wp_posts AS p
            
            INNER JOIN wp_postmeta AS pm1
            ON pm1.post_id = p.ID
            AND pm1.meta_key = 'start_date'
                
            INNER JOIN wp_postmeta AS pm2
            ON pm2.post_id = p.ID
            AND pm2.meta_key = 'end_date'
            
            LEFT JOIN wp_postmeta AS pm3
            ON pm3.post_id = p.ID
            AND pm3.meta_key = 'is_kids_course'
            
            LEFT JOIN wp_postmeta AS pm4
            ON pm4.post_id = p.ID
            AND pm4.meta_key = '_regular_price'
            
            LEFT JOIN wp_enrolments AS e
            ON e.course_id = p.ID 
            
            INNER JOIN wp_posts AS o
            ON e.order_id = o.ID
            
            LEFT JOIN wp_postmeta AS om1
            ON om1.post_id = o.ID
            AND om1.meta_key = '_transaction_id'
            
            LEFT JOIN wp_users AS u
            ON u.ID = e.user_id
            
            LEFT JOIN wp_usermeta AS um1
            ON um1.user_id = e.user_id
            AND um1.meta_key = 'expiry'
            
            WHERE p.post_type = 'product'
            {$sql_where_date_from}
            {$sql_where_date_to}
            {$sql_where_is_kids_course}
            AND e.is_archived = 0
            AND e.trashed = 0";

    return $wpdb->get_results($sql, OBJECT);
}

function get_report_archived_courses_enrolments($date_from, $date_to, $is_kids_course)
{
    global $wpdb;

    $sql_where_date_from = ($date_from) ? " AND ac.start_date >= " . str_replace('-', '', $date_from) : '';
    $sql_where_date_to   = ($date_to) ? " AND ac.start_date <= " . str_replace('-', '', $date_to) : '';

    $sql_where_is_kids_course = '';
    if ($is_kids_course === 'adults') {
        $sql_where_is_kids_course = " AND ac.is_kids_course = '0'";
    } else if ($is_kids_course === 'kids') {
        $sql_where_is_kids_course = " AND ac.is_kids_course = '1'";
    }

    $sql = "SELECT
                ac.course_id AS course_id,
                ac.course_title AS course_name,
                '1' AS is_archived,
                ac.start_date AS start_date,
                ac.end_date AS end_date,
                ac.is_kids_course AS is_kids_course,
                ac.price AS regular_price,
                e.first_name AS first_name,
                e.last_name AS last_name,
                e.age AS age,
                e.phone AS phone,
                e.email AS email,
                e.special_requirements AS special_requirements,
                o.ID AS order_id,
                om1.meta_value AS transaction_id,
	            u.user_nicename AS user_nicename,
	            um1.meta_value AS member_expiry
            
            FROM wp_archived_courses AS ac
            
            LEFT JOIN wp_enrolments AS e
            ON e.archived_course_id = ac.id
            
            INNER JOIN wp_posts AS o
            ON e.order_id = o.ID
            
            LEFT JOIN wp_postmeta AS om1
            ON om1.post_id = o.ID
            AND om1.meta_key = '_transaction_id'
            
            LEFT JOIN wp_users AS u
            ON u.ID = e.user_id
            
            LEFT JOIN wp_usermeta AS um1
            ON um1.user_id = e.user_id
            AND um1.meta_key = 'expiry'
            
            WHERE ac.trashed = 0
            {$sql_where_date_from}
            {$sql_where_date_to}
            {$sql_where_is_kids_course}
            AND e.is_archived = 1
            AND e.trashed = 0";

    return $wpdb->get_results($sql, OBJECT);
}
