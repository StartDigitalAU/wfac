<?php

/****************************************************
 *
 * REPORTING - ALL ENROLMENTS
 *
 ****************************************************/

/**
 * Get list of all tutors for dropdown (simple version)
 */
function get_all_tutors_for_filter()
{
    global $wpdb;
    
    // Get tutors from active courses only (keep it simple)
    $tutors = $wpdb->get_results("
        SELECT DISTINCT 
            p_tutor.ID as tutor_id,
            p_tutor.post_title as tutor_name
        FROM wp_posts AS p
        LEFT JOIN wp_postmeta AS pm_tutor
            ON pm_tutor.post_id = p.ID AND pm_tutor.meta_key = 'tutor'
        LEFT JOIN wp_posts AS p_tutor
            ON pm_tutor.meta_value = p_tutor.ID
        WHERE p.post_type = 'product' 
            AND p_tutor.post_title IS NOT NULL
            AND p_tutor.post_title != ''
        ORDER BY p_tutor.post_title
    ", OBJECT);
    
    return $tutors;
}

/**
 * Retrieve list of course enrolments (with minimal tutor addition)
 *
 * @return array|null|object
 */
function get_admin_report_all_enrolments()
{
    global $wpdb;

    $filter = '';

    if (!empty($_GET['date_from'])) {
        $filter .= " WHERE e.created_at >= '" . date('Y-m-d H:i:s', strtotime($_GET['date_from'])) . "'";
    } else {
        $filter .= " WHERE e.created_at >= '" . date('Y-m-d H:i:s', strtotime(twelve_months_ago())) . "'";
    }

    if (!empty($_GET['date_to'])) {
        $filter .= (empty($filter)) ? ' WHERE': ' AND';
        $filter .= " e.created_at <= '" . date('Y-m-d H:i:s', strtotime($_GET['date_to'])) . "'";
    }

    if (!empty($_GET['type'])) {
        if ($_GET['type'] == 'adults') {
            $filter .= (empty($filter)) ? ' WHERE': ' AND';
            $filter .= " e.child = 0";
        }
        elseif ($_GET['type'] == 'kids') {
            $filter .= (empty($filter)) ? ' WHERE': ' AND';
            $filter .= " e.child = 1";
        }
    }

    // NEW: Add tutor filtering (simple approach)
    if (!empty($_GET['tutors']) && is_array($_GET['tutors'])) {
        $tutor_ids = array_map('intval', $_GET['tutors']);
        if (!empty($tutor_ids)) {
            $filter .= (empty($filter)) ? ' WHERE': ' AND';
            $filter .= " p_tutor.ID IN (" . implode(',', $tutor_ids) . ")";
        }
    }

    $filter_ignore_cancelled = (empty($filter)) ? ' WHERE e.trashed != 1': ' AND e.trashed != 1';

    if (!empty($_GET['get_cancelled'])) {
        $filter_ignore_cancelled = '';
    }

    $filter .= $filter_ignore_cancelled;

    $filter_remove_duplicates = false;
    if (!empty($_GET['remove_duplicates'])) {
        $filter_remove_duplicates = " GROUP BY e.email";
    }

    // MODIFIED: Add tutor joins to existing query
    $sql = "SELECT
        e.*,
        wpm1.meta_value AS postcode,
        p_tutor.post_title AS tutor_name

    FROM wp_enrolments AS e
    
    LEFT JOIN wp_postmeta AS wpm1
    ON wpm1.post_id = e.id AND wpm1.meta_key = '_billing_postcode'

    LEFT JOIN wp_posts AS p
    ON p.ID = e.course_id

    LEFT JOIN wp_postmeta AS pm_tutor
    ON pm_tutor.post_id = p.ID AND pm_tutor.meta_key = 'tutor'

    LEFT JOIN wp_posts AS p_tutor
    ON pm_tutor.meta_value = p_tutor.ID

    {$filter}
    {$filter_remove_duplicates}
    ";

    $results = $wpdb->get_results($sql, OBJECT);

    return $results;
}

/**
 * Display the admin Enrolments report (with tutor filter added)
 */
function view_admin_report_all_enrolments()
{
    global $wpdb;

    $date_from = twelve_months_ago();
    $default_text = '<br>Only shows the last 12 months by default; Please use filters to adjust dates';
    if (!empty($_GET['date_from'])) {
        $date_from = $_GET['date_from'];
        $default_text = '';
    }

    $date_to = '';
    if (!empty($_GET['date_to'])) {
        $date_to = $_GET['date_to'];
    }

    $type = '';
    if (!empty($_GET['type'])) {
        $type = $_GET['type'];
    }

    $remove_duplicates = false;
    if (!empty($_GET['remove_duplicates'])) {
        $remove_duplicates = $_GET['remove_duplicates'];
    }

    $get_cancelled = false;
    if (!empty($_GET['get_cancelled'])) {
        $get_cancelled = $_GET['get_cancelled'];
    }

    // NEW: Handle selected tutors
    $selected_tutors = array();
    if (!empty($_GET['tutors']) && is_array($_GET['tutors'])) {
        $selected_tutors = $_GET['tutors'];
    }

    $all_tutors = get_all_tutors_for_filter();
    $results = get_admin_report_all_enrolments();
    ?>
    <div class="wrap">

        <h1>Enrollees</h1>

        <small><strong>Note:</strong> Hiding duplicate email addresses will also hide the course field.<?= $default_text ?></small>

        <form id="posts-filter" action="" method="get">

            <input type="hidden" name="page" value="reports-all-enrolments" />

            <div class="tablenav top">
                <div class="alignleft actions">

                    <input type="text" name="date_from" class="date-picker" placeholder="From Date" value="<?php echo $date_from; ?>"/>
                    <input type="text" name="date_to" class="date-picker" placeholder="To Date" value="<?php echo $date_to; ?>"/>

                    <select name="type" class="postform">
                        <option value="">All Types</option>
                        <option value="adults"<?php if ($type == 'adults') { echo ' selected="selected"'; } ?>>Adults</option>
                        <option value="kids"<?php if ($type == 'kids') { echo ' selected="selected"'; } ?>>Kids</option>
                    </select>

                    <div style="display: inline-block; vertical-align: top; margin-right: 10px; margin-bottom: 16px;">
                        <label style="font-weight: bold; display: block; margin: 6px;">Select Tutors:</label>
                        <div style="border: 1px solid #ddd; padding: 12px; background: white; max-height: 120px; overflow-y: auto; min-width: 200px;">
                            <label style="display: block; margin-bottom: 3px;">
                                <input type="checkbox" id="select_all_tutors" style="margin-right: 5px;">
                                <strong>Select All Tutors</strong>
                            </label>
                            <hr style="margin: 5px 0;">
                            <?php foreach ($all_tutors as $tutor): ?>
                                <label style="display: block; margin-bottom: 3px; font-weight: normal;">
                                    <input type="checkbox" name="tutors[]" value="<?php echo $tutor->tutor_id; ?>" 
                                           class="tutor-checkbox" style="margin-right: 5px;"
                                           <?php if (in_array($tutor->tutor_id, $selected_tutors)) echo 'checked="checked"'; ?>>
                                    <?php echo esc_html($tutor->tutor_name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const selectAllCheckbox = document.getElementById('select_all_tutors');
                        const tutorCheckboxes = document.querySelectorAll('.tutor-checkbox');
                        
                        // Handle "Select All" functionality
                        selectAllCheckbox.addEventListener('change', function() {
                            tutorCheckboxes.forEach(checkbox => {
                                checkbox.checked = this.checked;
                            });
                        });
                        
                        // Handle individual checkbox changes
                        tutorCheckboxes.forEach(checkbox => {
                            checkbox.addEventListener('change', function() {
                                const allChecked = Array.from(tutorCheckboxes).every(cb => cb.checked);
                                const noneChecked = Array.from(tutorCheckboxes).every(cb => !cb.checked);
                                
                                if (allChecked) {
                                    selectAllCheckbox.checked = true;
                                    selectAllCheckbox.indeterminate = false;
                                } else if (noneChecked) {
                                    selectAllCheckbox.checked = false;
                                    selectAllCheckbox.indeterminate = false;
                                } else {
                                    selectAllCheckbox.checked = false;
                                    selectAllCheckbox.indeterminate = true;
                                }
                            });
                        });
                        
                        // Set initial state of "Select All" checkbox
                        const checkedCount = Array.from(tutorCheckboxes).filter(cb => cb.checked).length;
                        if (checkedCount === tutorCheckboxes.length && checkedCount > 0) {
                            selectAllCheckbox.checked = true;
                        } else if (checkedCount > 0) {
                            selectAllCheckbox.indeterminate = true;
                        }
                    });
                    </script>

                    <label><input type="checkbox" name="remove_duplicates" value="1"<?php if ($remove_duplicates) { echo ' checked="checked"'; } ?>/> Hide duplicate emails?</label>

                    <label><input type="checkbox" name="get_cancelled" value="1"<?php if ($get_cancelled) { echo ' checked="checked"'; } ?>/> Include cancelled?</label>

                    <input class="button" type="submit" value="Filter">
                </div>

                <div class="alignright actions">
                    <?php
                    $url = admin_url('admin-post.php?action=print_report_all_enrolments.csv');
                    $email_url = admin_url('admin-post.php?action=print_report_emails_only.csv');

                    $url_params = isset($_GET['date_from']) ? '&date_from=' . $_GET['date_from']: '';
                    $url_params .= isset($_GET['date_to']) ? '&date_to=' . $_GET['date_to']: '';
                    $url_params .= isset($_GET['type']) ? '&type=' . $_GET['type']: '';
                    $url_params .= isset($_GET['remove_duplicates']) ? '&remove_duplicates=1': '';
                    $url_params .= isset($_GET['get_cancelled']) ? '&get_cancelled=1': '';
                    
                    if (!empty($_GET['tutors']) && is_array($_GET['tutors'])) {
                        foreach ($_GET['tutors'] as $tutor) {
                            $url_params .= '&tutors[]=' . urlencode($tutor);
                        }
                    }
                    ?>
                    <a href="<?php echo $url . $url_params; ?>" class="button">Export CSV</a>
                    <a href="<?php echo $email_url . $url_params; ?>" class="button button-primary">Export Email List</a>
                </div>
            </div>

            <div class="acf-field acf-field-repeater">

                <?php if (count($results)) { ?>
                    <div class="acf-input gap-top">
                        <table class="acf-table">
                            <thead>
                            <tr>
                                <th class="acf-row-handle"></th>
                                <th class="acf-th acf-th-text">Enrollee</th>
                                <?php if (!$remove_duplicates) { ?>
                                    <th class="acf-th acf-th-text">Course Name</th>
                                    <th class="acf-th acf-th-text">Course Dates</th>
                                <?php } ?>
                                <th class="acf-th acf-th-text">Tutor</th>
                                <th class="acf-th acf-th-text">Email Address</th>
                                <th class="acf-th acf-th-text">Postcode</th>
                                <th class="acf-th acf-th-text">Age</th>
                                <th class="acf-th acf-th-text">Created At</th>
                                <th class="acf-th acf-th-text">Archived</th>
                                <?php if (isset($_GET['get_cancelled'])) { ?>
                                    <th class="acf-th acf-th-text">Cancelled</th>
                                <?php } ?>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i = 1;
                            ?>
                            <?php foreach ($results as $result) { ?>
                                <?php
                                if ($result->is_archived) {
                                    $fields = $wpdb->get_row('SELECT * FROM wp_archived_courses WHERE id = ' . $result->archived_course_id, ARRAY_A);
                                }
                                else {
                                    $fields = get_fields($result->course_id);
                                }
                                ?>
                                <tr class="acf-row"<?php if ($result->trashed) { ?> style="opacity: 0.5;"<?php } ?>>
                                    <td class="acf-row-handle order">
                                        <span><?php echo $i; ?></span>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <a href="<?php echo admin_url('admin.php?page=enrolments_view&entry=' . $result->id); ?>" title="View enrollee">
                                            <?php echo $result->last_name . ', ' . $result->first_name . ' (' . $result->title . ')'; ?>
                                        </a>
                                    </td>
                                    <?php if (!$remove_duplicates) { ?>
                                        <?php if ($result->is_archived) { ?>
                                            <td class="acf-field acf-field-text">
                                                <a href="<?php echo admin_url( 'admin.php?page=archived_courses_view&entry=' . $result->archived_course_id ); ?>">
                                                    <?php echo $fields['course_title']; ?>
                                                </a>
                                            </td>
                                        <?php } else { ?>
                                            <td class="acf-field acf-field-text">
                                                <a href="<?php echo get_edit_post_link($result->course_id); ?>">
                                                    <?php echo get_the_title($result->course_id); ?>
                                                </a>
                                            </td>
                                        <?php } ?>
                                        <td class="acf-field acf-field-text">
                                            <?php
                                            if (!empty($fields['start_date']) && !empty($fields['end_date'])) {
                                                echo date('Y-m-d', strtotime($fields['start_date'])) . ' to<br> ' . date('Y-m-d', strtotime($fields['end_date']));
                                            }
                                            ?>
                                        </td>
                                    <?php } ?>
                                    <td class="acf-field acf-field-text">
                                        <?php echo esc_html($result->tutor_name); ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $result->email; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $result->postcode; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $result->age; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $result->created_at; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $result->is_archived; ?>
                                    </td>
                                    <?php if (isset($_GET['get_cancelled'])) { ?>
                                        <?php if ($result->trashed) { ?>
                                            <td class="acf-th acf-th-text">Cancelled</td>
                                        <?php } else { ?>
                                            <td class="acf-th acf-th-text"></td>
                                        <?php } ?>
                                    <?php } ?>
                                </tr>
                                <?php $i++; ?>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } ?>
            </div>

        </form>

    </div>
    <?php
}

/**
 * CSV export of all enrolments (with tutor column added)
 */
function print_report_csv_all_enrolments()
{
    global $wpdb;

    if (!current_user_can('manage_options')) {
        return;
    }

    $results = get_admin_report_all_enrolments();

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=course-enrolment-report.csv');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    if (!empty($_GET['remove_duplicates'])) {
        fputcsv(
            $output,
            array(
                'Enrollee',
                'Tutor',
                'Email Address',
                'Postcode',
                'Age',
                'Created At',
                'Archived',
                'Cancelled'
            )
        );
    }
    else {
        fputcsv(
            $output,
            array(
                'Enrollee',
                'Course',
                'Course Dates',
                'Tutor',
                'Email Address',
                'Postcode',
                'Age',
                'Created At',
                'Archived',
                'Cancelled'
            )
        );
    }

    foreach ($results as $result) {

        if ($result->is_archived) {
            $fields = $wpdb->get_row('SELECT * FROM wp_archived_courses WHERE id = ' . $result->archived_course_id, ARRAY_A);
        }
        else {
            $fields = get_fields($result->course_id);
        }

        $cancelled = '';

        if (!empty($_GET['remove_duplicates'])) {
            fputcsv(
                $output,
                array(
                    $result->last_name . ', ' . $result->first_name . ' (' . $result->title . ')' . $cancelled,
                    $result->tutor_name,
                    $result->email,
                    $result->postcode,
                    $result->age,
                    $result->created_at,
                    $result->is_archived,
                    $result->trashed
                )
            );
        }
        else {
            $date = '';
            if (!empty($fields['start_date']) && !empty($fields['end_date'])) {
                $date = date('Y-m-d', strtotime($fields['start_date'])) . ' - ' . date('Y-m-d', strtotime($fields['end_date']));
            }

            fputcsv(
                $output,
                array(
                    $result->last_name . ', ' . $result->first_name . ' (' . $result->title . ')' . $cancelled,
                    get_the_title($result->course_id),
                    $date,
                    $result->tutor_name,
                    $result->email,
                    $result->postcode,
                    $result->age,
                    $result->created_at,
                    $result->is_archived,
                    $result->trashed
                )
            );
        }
    }
}
add_action('admin_post_print_report_all_enrolments.csv', 'print_report_csv_all_enrolments');

/**
 * NEW: Simple email-only export
 */
function print_report_emails_only()
{
    global $wpdb;

    if (!current_user_can('manage_options')) {
        return;
    }

    // Force remove duplicates for email export
    $_GET['remove_duplicates'] = 1;
    $results = get_admin_report_all_enrolments();

    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename=student-emails.txt');
    header('Pragma: no-cache');

    $emails = array();
    foreach ($results as $result) {
        if (!empty($result->email)) {
            $emails[] = $result->email;
        }
    }

    // Output comma-separated for easy copy/paste
    echo implode(', ', array_unique($emails));
}
add_action('admin_post_print_report_emails_only.csv', 'print_report_emails_only');

function twelve_months_ago() {
    $today = new DateTime();
    $dateMinus12 = $today->modify("-12 months");
    $last_year = $dateMinus12->format("Y-m-t");

    return $last_year;
}