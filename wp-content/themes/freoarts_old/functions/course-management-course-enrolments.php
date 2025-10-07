<?php

/****************************************************
 *
 * COURSE MANAGEMENT - COURSE ENROLMENTS
 *
 ****************************************************/

use SendGrid\Email;
use SendGrid\Exception as SendGridException;

/**
 * Retrieve list of course enrolments
 *
 * @return array|null|object
 */
function get_admin_course_management_enrolments($get_cancelled = false)
{

    global $wpdb;

    $course_id = null;
    $filter = '';

    // Is course selected?
    if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {

        $course_id = $_GET['course_id'];
        $filter = " WHERE e.course_id = {$course_id} AND is_archived = 0";
    } elseif (isset($_GET['archived_course_id']) && !empty($_GET['archived_course_id'])) {

        $course_id = $_GET['archived_course_id'];
        $filter = " WHERE e.archived_course_id = {$course_id} AND is_archived = 1";
    }
    // If course not selected?
    else {

        return array();
    }

    // Show cancelled enrolments?
    if (!$get_cancelled) {

        $filter .= empty($filter) ? " WHERE" : " AND";
        $filter .= ' e.trashed != 1';
    }

    $sql = "SELECT
        *,
        child AS is_kids_course

    FROM wp_enrolments AS e

    {$filter}
    ";

    $results = $wpdb->get_results($sql, OBJECT);

    return $results;
}

/**
 * Display the admin Courses report
 *
 */
function view_admin_course_management_course_enrolments()
{

    $get_cancelled = 0;
    if (isset($_GET['get_cancelled']) && !empty($_GET['get_cancelled'])) {
        $get_cancelled = 1;
    }

    $results = get_admin_course_management_enrolments($get_cancelled);

    $active_enrolees = 0;
    foreach ($results as $enrolee) {

        if (!$enrolee->trashed) {
            $active_enrolees++;
        }
    }
?>
    <div class="wrap">

        <h1>Course Enrolments</h1>

        <form id="posts-filter" action="" method="get">

            <input type="hidden" name="page" value="course-management-course-enrolments" />

            <?php
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
            $course_option_html = '';

            $active_course = null;

            foreach ($courses as $course) {

                $selected = '';

                if (!empty($_GET['course_id']) && $_GET['course_id'] == $course->ID) {

                    $selected = ' selected="selected"';
                    $active_course = $course;
                }

                $start_date = format_acf_date(get_field('start_date', $course->ID), 'Y-m-d');

                $course_option_html .= '<option value="' . $course->ID . '"' . $selected . '>' . $course->post_title . ' (' . $start_date . ')</option>';
            }
            ?>

            <div class="tablenav top">

                <div class="alignleft actions">
                    <select name="course_id" class="postform">
                        <option value="">Select Course</option>
                        <?php echo $course_option_html; ?>
                    </select>
                </div>

                <div class="alignleft actions">
                    <label>
                        <input type="checkbox" name="get_cancelled" value="1" <?php if ($get_cancelled) {
                                                                                    echo ' checked="checked"';
                                                                                } ?>> Include cancelled enrolees?
                    </label>
                </div>

                <div class="alignleft actions">
                    <input class="button" type="submit" value="Select">
                </div>

                <?php if (!empty($_GET['course_id'])) { ?>
                    <div class="alignright actions">
                        <?php $url = admin_url('admin.php?page=enrolments_new&course_id=' . $_GET['course_id']); ?>
                        <a href="<?php echo $url; ?>" class="button">Add Enrolee</a>
                        <?php $url = admin_url('admin-post.php?action=print_course_role.pdf&course_id=' . $_GET['course_id']); ?>
                        <a href="<?php echo $url; ?>" class="button">Print Course Role</a>
                        <?php $url = admin_url('admin-post.php?action=print_course_enrolments.csv&course_id=' . $_GET['course_id'] . '&get_cancelled=' . $get_cancelled); ?>
                        <a href="<?php echo $url; ?>" class="button">Export CSV</a>
                        <?php $url = admin_url('admin.php?page=course-management-bulk-email&course_id=' . $_GET['course_id']); ?>
                        <a href="<?php echo $url; ?>" class="button">Bulk Email</a>
                        <?php $url = admin_url('admin-post.php?action=cancel_course_enrolments&course_id=' . $_GET['course_id']); ?>
                        <a href="<?php echo $url; ?>" class="button" onclick="if (!confirm('Are you sure you want to cancel and refund all enrolees?')) { return false; }">Cancel All (Full Refund)</a>
                    </div>
                <?php } ?>
            </div>

            <?php if (!empty($active_course)) { ?>
                <?php
                $th_width = '200';
                $fields = get_fields($active_course->ID);
                $_product = wc_get_product($active_course->ID);
                ?>
                <div id="wpseo_meta" class="postbox ">
                    <div class="acf-input">
                        <table class="acf-table">
                            <tbody>
                                <tr>
                                    <th width="<?php echo $th_width; ?>" class="acf-th acf-th-text">Course Name</th>
                                    <td class="acf-field acf-field-text">
                                        <a href="<?php echo get_edit_post_link($active_course->ID); ?>"><?php echo $active_course->post_title; ?></a>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="<?php echo $th_width; ?>" class="acf-th acf-th-text">Duration</th>
                                    <td class="acf-field acf-field-text">
                                        <?php echo get_formatted_datetime($fields); ?><br>
                                        <?php echo ifne($fields, 'duration'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="<?php echo $th_width; ?>" class="acf-th acf-th-text">Enrolments</th>
                                    <td class="acf-field acf-field-text"><?php echo $active_enrolees; ?> / <?php echo $active_enrolees + $_product->get_stock_quantity(); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>

            <div class="acf-field acf-field-repeater">

                <?php if (count($results)) { ?>
                    <div class="acf-input">
                        <table class="acf-table">
                            <thead>
                                <tr>
                                    <th class="acf-row-handle"></th>
                                    <th class="acf-th acf-th-text">Purchaser / Order</th>
                                    <th class="acf-th acf-th-text">Student Name</th>
                                    <th class="acf-th acf-th-text">Age</th>
                                    <th class="acf-th acf-th-text">Contact Number</th>
                                    <th class="acf-th acf-th-text">Email Address</th>
                                    <th class="acf-th acf-th-text">Emergency Contact</th>
                                    <th class="acf-th acf-th-text">Emergency Phone</th>
                                    <th class="acf-th acf-th-text">Special Requirements</th>
                                    <th class="acf-th acf-th-text">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($results as $result) { ?>
                                    <?php
                                    $user = get_userdata($result->user_id);
                                    ?>
                                    <tr class="acf-row" <?php if ($result->trashed) { ?> style="opacity: 0.5;" <?php } ?>>
                                        <td class="acf-row-handle order">
                                            <span><?php echo $i; ?></span>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php if (!empty($result->user_id)) { ?>
                                                <a href="<?php echo get_edit_user_link($result->user_id); ?>" title="<?php echo $user->user_nicename; ?>">
                                                    <?php echo $user->user_nicename; ?>
                                                </a>
                                            <?php } else { ?>
                                                Guest
                                            <?php } ?>
                                            -
                                            <?php if (!empty($result->order_id)) { ?>
                                                <a href="<?php echo get_edit_post_link($result->order_id); ?>" title="View Order">
                                                    Order #<?php echo $result->order_id; ?>
                                                </a>
                                            <?php } else { ?>
                                                No Related Order
                                            <?php } ?>
                                            <?php if (!empty($result->order_id) && $result->trashed) { ?> (Refunded)<?php } ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <a href="<?php echo admin_url('admin.php?page=enrolments_view&entry=' . $result->id); ?>" title="<?php echo $result->title; ?>">
                                                <?php echo $result->last_name . ', ' . $result->first_name . ' (' . $result->title . ')'; ?>
                                            </a>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php
                                            echo (!empty($result->age)) ? $result->age : 'N/A';
                                            ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->phone; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->email; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->emergency_name; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->emergency_phone; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->special_requirements; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php if (!$result->trashed) { ?>
                                                <?php $url = admin_url('admin-post.php?action=cancel_course_enrolment&course_id=' . $_GET['course_id'] . '&enrolment_id=' . $result->id); ?>
                                                Cancel:
                                                <a href="<?php echo $url . '&refund=full'; ?>" title="Cancel" onclick="if (!confirm('Are you sure you want to cancel and refund this enrolee 100%?')) { return false; }">Full Refund</a> |
                                                <a href="<?php echo $url . '&refund=partial'; ?>" title="Cancel" onclick="if (!confirm('Are you sure you want to cancel and refund this enrolee 80%?')) { return false; }">80% Refund</a> |
                                                <a href="<?php echo $url . '&refund=none'; ?>" title="Cancel" onclick="if (!confirm('Are you sure you want to cancel this enrolee with no refund?')) { return false; }">No Refund</a>
                                            <?php } else { ?>
                                                (Cancelled)
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p>Currently no enrolments for this course.</p>
                <?php } ?>
            </div>

        </form>

    </div>
<?php
}

/**
 * CSV export of selected courses
 *
 */
function course_management_course_enrolments_csv()
{
    global $wpdb;

    if (!current_user_can('course_management_enrolments')) {
        return;
    }

    $get_cancelled = false;
    if (isset($_GET['get_cancelled']) && !empty($_GET['get_cancelled'])) {
        $get_cancelled = true;
    }

    $results = get_admin_course_management_enrolments($get_cancelled);

    $course_title = '';
    if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
        $course_title = get_the_title($_GET['course_id']);
    } elseif (isset($_GET['archived_course_id']) && !empty($_GET['archived_course_id'])) {
        $course_title = $wpdb->get_var("SELECT course_title FROM wp_archived_courses WHERE id = '{$_GET['archived_course_id']}'");
    }

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=course-enrolment-report.csv');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    fputcsv(
        $output,
        array(
            $course_title,
            '',
            '',
            '',
            '',
            ''
        )
    );

    fputcsv(
        $output,
        array(
            'Purchaser / Order',
            'Student Name',
            'Age',
            'Contact Number',
            'Email Address',
            'Special Requirements'
        )
    );

    $capacity_total = 0;
    $sold_total = 0;

    foreach ($results as $result) {

        $purchaser_order = '';
        if (!empty($result->user_id)) {
            $user = get_userdata($result->user_id);
            $purchaser_order .=  $user->user_nicename;
        } else {
            $purchaser_order .=  'Guest';
        }

        $purchaser_order .= ' - ';

        if (!empty($result->order_id)) {
            $purchaser_order .=  'Order #' . $result->order_id;
        } else {
            $purchaser_order .=  'No Order';
        }

        $cancelled = ($result->trashed) ? ' (Cancelled)' : '';

        fputcsv(
            $output,
            array(
                $purchaser_order . $cancelled,
                $result->last_name . ', ' . $result->first_name . ' (' . $result->title . ')',
                $result->age,
                $result->phone,
                $result->email,
                $result->special_requirements
            )
        );
    }
}
add_action('admin_post_print_course_enrolments.csv', 'course_management_course_enrolments_csv');

/**
 * Print course role PDF using FPDF
 *
 */

require_once(get_template_directory() . '/lib/fpdf186/fpdf.php');

function course_management_course_role_pdf()
{
    if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
        $course_id = $_GET['course_id'];
    } else {
        wp_die('Invalid course ID');
    }

    $results = get_admin_course_management_enrolments();

    // Get course details
    $course = get_post($course_id);
    $tutor_id = get_field('tutor', $course_id);
    $tutor = get_the_title($tutor_id);
    $is_kids_course = get_field('is_kids_course', $course_id);

    $studio = '';
    if (have_rows('additional_fields', $course_id)) {
        while (have_rows('additional_fields', $course_id)) {
            the_row();
            if (strtolower(get_sub_field('label')) != 'studio') continue;
            $studio = get_sub_field('value');
            break;
        }
    }

    $start_date = format_acf_date(get_field('start_date', $course_id), 'Y-m-d');
    $end_date = format_acf_date(get_field('end_date', $course_id), 'Y-m-d');

    // Calculate weeks between dates
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $interval = $start->diff($end);
    $weeks = ceil($interval->days / 7);

    // Create PDF instance
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->SetAutoPageBreak(true, 15);

    // Create new page for sign-in sheet
    $pdf->AddPage();

    // Add Logo
    $pdf->Image(get_template_directory() . '/vector-assets/wfac.png', 250, 10, 35);

    // Document Title with more spacing and larger font
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->MultiCell(220, 8, $course->post_title, 0, 'L');
    $pdf->Ln(6);

    // Course Information Block with better spacing and alignment
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetFillColor(245, 245, 245);

    // Course details with consistent spacing
    $labelWidth = 50;
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($labelWidth, 8, 'Tutor:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(80, 8, $tutor, 0, 0, 'L');

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($labelWidth, 8, 'Studio:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(80, 8, $studio, 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($labelWidth, 8, 'Date:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, $start_date, 0, 1, 'L');

    // Add more spacing before the sign-in table
    $pdf->Ln(4);

    if ($weeks <= 1) {
        get_one_week_role($pdf, $results, $is_kids_course);
    } else {
        get_multi_week_role($pdf, $results, $weeks, $start);
    }

    // Important Information Page
    $pdf->AddPage();
    $pdf->AliasNbPages();

    // Document Title
    $pdf->SetFont('Arial', 'B', 18);
    $pdf->Cell(0, 8, 'Important Information', 0, 1, 'L');

    // Add more spacing before the sign-in table
    $pdf->Ln(6);

    // Add Logo
    $pdf->Image(get_template_directory() . '/vector-assets/wfac.png', 250, 10, 35);

    // Information numbers
    $labelWidth = 60;
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($labelWidth, 8, 'Creative Learning:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(80, 8, '0448 280 682', 0, 0, 'L');

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($labelWidth, 8, 'Reception:', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(80, 8, '9432 9555', 0, 1, 'L');

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell($labelWidth, 8, 'Aaron Lyons (Facilities):', 0, 0, 'L');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, '0408 912 328', 0, 1, 'L');

    // Add more spacing before the sign-in table
    $pdf->Ln(4);

    // Table headers with improved styling
    $pdf->SetFillColor(51, 51, 51);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(75, 12, '  Student Name', 1, 0, 'L', true);
    $pdf->Cell(82, 12, '  Additional Needs / Notes', 1, 0, 'L', true);
    $pdf->Cell(60, 12, '  Emergency Contact', 1, 0, 'L', true);
    $pdf->Cell(60, 12, '  Emergency Phone', 1, 1, 'L', true);

    // Reset text color for content
    $pdf->SetTextColor(0, 0, 0);

    // Table content with alternating background
    $pdf->SetFont('Arial', '', 11);
    $rowCount = 0;
    foreach ($results as $result) {
        if ($result->trashed) continue;

        $fill = ($rowCount % 2 == 0) ? true : false;
        $pdf->SetFillColor(245, 245, 245);

        $student_name = sprintf('%s, %s', $result->last_name, $result->first_name);

        if (!empty($result->title)) {
            $student_name .= sprintf(' (%s)', $result->title);
        }

        $special_req_height = max(12, ceil($pdf->GetStringWidth($result->special_requirements) / 82) * 8);

        $pdf->Cell(75, $special_req_height, '  ' . $student_name, 1, 0, 'L', $fill);
        $pdf->Cell(82, $special_req_height, '  ' . $result->special_requirements, 1, 0, 'L', $fill);
        $pdf->Cell(60, $special_req_height, '  ' . $result->emergency_name, 1, 0, 'L', $fill);
        $pdf->Cell(60, $special_req_height, '  ' . $result->emergency_phone, 1, 1, 'L', $fill);

        $rowCount++;
    }

    // Output PDF
    // Format the filename with course name and date
    $course_name = sanitize_title($course->post_title); // Convert to URL-friendly string
    $date_formatted = date('Y-m-d', strtotime($start_date));
    $filename = $course_name . '_' . $date_formatted . '.pdf';
    
    $pdf->Output('D', $filename);
    exit;
}

function get_one_week_role($pdf, $results, $is_kids_course = false)
{
    if ($is_kids_course) {
        // Kids course table structure
        $pdf->SetFillColor(51, 51, 51);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);
        
        // Column widths for kids course (total should be around 277mm for landscape A4)
        $columnWidths = array(55, 55, 55, 55, 55);
        
        // First header row - blank above student name, then Sign In (3 columns), Sign Out (1 column)
        $pdf->Cell($columnWidths[0], 6, '', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[1] + $columnWidths[2] + $columnWidths[3], 6, 'Sign In', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[4], 6, 'Sign Out', 1, 1, 'C', true);
        
        // Second header row with actual column titles
        $pdf->Cell($columnWidths[0], 12, 'Student Name', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[1], 12, 'Guardian Name', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[2], 12, 'Guardian Sig.', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[3], 12, 'Pick Up Name', 1, 0, 'C', true);
        $pdf->Cell($columnWidths[4], 12, 'Guardian Sig.', 1, 1, 'C', true);
        
        // Reset text color for content
        $pdf->SetTextColor(0, 0, 0);
        
        // Generate rows with actual student names
        $pdf->SetFont('Arial', '', 10);
        $rowCount = 0;
        
        // First, populate with actual enrolled students
        foreach ($results as $result) {
            if ($result->trashed) continue;
            
            $fill = ($rowCount % 2 == 0) ? true : false;
            $pdf->SetFillColor(245, 245, 245);
            
            $student_name = sprintf('%s, %s', $result->last_name, $result->first_name);
            if (!empty($result->title)) {
                $student_name .= sprintf(' (%s)', $result->title);
            }
            
            $pdf->Cell($columnWidths[0], 15, '  ' . $student_name, 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[1], 15, '', 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[2], 15, '', 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[3], 15, '', 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[4], 15, '', 1, 1, 'L', $fill);
            $rowCount++;
        }
        
        // Add 3 extra blank rows for additional attendees
        for ($i = 0; $i < 3; $i++) {
            $fill = ($rowCount % 2 == 0) ? true : false;
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell($columnWidths[0], 15, '', 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[1], 15, '', 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[2], 15, '', 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[3], 15, '', 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[4], 15, '', 1, 1, 'L', $fill);
            $rowCount++;
        }
    } else {
        // Original adult course table structure
        $pdf->SetFillColor(51, 51, 51);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 11);
        $columnWidths = array(75, 202);
        $pdf->Cell($columnWidths[0], 12, '  Student Name', 1, 0, 'L', true);
        $pdf->Cell($columnWidths[1], 12, '  Sign in', 1, 1, 'L', true);

        // Reset text color for content
        $pdf->SetTextColor(0, 0, 0);

        // Generate sign-in rows with actual student names
        $pdf->SetFont('Arial', '', 11);
        $rowCount = 0;

        // First, populate with actual enrolled students
        foreach ($results as $result) {
            if ($result->trashed) continue;

            $fill = ($rowCount % 2 == 0) ? true : false;
            $pdf->SetFillColor(245, 245, 245);

            $student_name = sprintf('%s, %s', $result->last_name, $result->first_name);
            if (!empty($result->title)) {
                $student_name .= sprintf(' (%s)', $result->title);
            }

            $pdf->Cell($columnWidths[0], 12, '  ' . $student_name, 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[1], 12, '', 1, 1, 'L', $fill);
            $rowCount++;
        }

        // Add 3 extra blank rows for additional attendees
        for ($i = 0; $i < 3; $i++) {
            $fill = ($rowCount % 2 == 0) ? true : false;
            $pdf->SetFillColor(245, 245, 245);
            $pdf->Cell($columnWidths[0], 12, '', 1, 0, 'L', $fill);
            $pdf->Cell($columnWidths[1], 12, '', 1, 1, 'L', $fill);
            $rowCount++;
        }
    }
}

function get_multi_week_role($pdf, $results, $weeks, $start)
{
    // Include the current week
    $weeks = $weeks + 1;
    // Sign-in Table Header with improved styling
    $pdf->SetFillColor(51, 51, 51);
    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFont('Arial', 'B', 11);
    $columnWidths = array(67, 210 / $weeks);

    $pdf->Cell($columnWidths[0], 2, '', 0, 0, 'C', true);
    for ($w = 1; $w <= $weeks; $w++) {
        $pdf->Cell($columnWidths[1], 2, '', 0, ($w == $weeks ? 1 : 0), 'C', true);
    }

    // First row with student name and week numbers
    $pdf->Cell($columnWidths[0], 6, 'Student Name', '', 0, 'C', true);
    for ($w = 1; $w <= $weeks; $w++) {
        $pdf->Cell($columnWidths[1], 6, 'Week ' . $w, '', ($w == $weeks ? 1 : 0), 'C', true);
    }

    // Second row with empty space and dates
    $pdf->Cell($columnWidths[0], 6, '', '', 0, 'C', true);
    $pdf->SetFont('Arial', 'B', 9);
    for ($w = 1; $w <= $weeks; $w++) {
        $week_date = clone $start;
        $week_date->modify('+' . ($w - 1) . 'week');
        $pdf->Cell($columnWidths[1], 6, $week_date->format('d/m/y'), '', ($w == $weeks ? 1 : 0), 'C', true);
    }

    $pdf->Cell($columnWidths[0], 2, '', 0, 0, 'C', true);
    for ($w = 1; $w <= $weeks; $w++) {
        $pdf->Cell($columnWidths[1], 2, '', 0, ($w == $weeks ? 1 : 0), 'C', true);
    }

    // Reset text color for content
    $pdf->SetTextColor(0, 0, 0);

    // Generate sign-in rows with actual student names
    $pdf->SetFont('Arial', '', 10); // Slightly smaller font for multi-week
    $rowCount = 0;

    // First, populate with actual enrolled students
    foreach ($results as $result) {
        if ($result->trashed) continue;

        $fill = ($rowCount % 2 == 0) ? true : false;
        $pdf->SetFillColor(245, 245, 245);

        // Truncate name if too long for narrow column
        $student_name = sprintf('%s, %s', $result->last_name, $result->first_name);
        if (!empty($result->title)) {
            $student_name .= sprintf(' (%s)', $result->title);
        }

        // Truncate if name is too long for the column
        if (strlen($student_name) > 20) {
            $student_name = substr($student_name, 0, 17) . '...';
        }

        $pdf->Cell($columnWidths[0], 12, '  ' . $student_name, 1, 0, 'L', $fill);

        for ($w = 1; $w <= $weeks; $w++) {
            $pdf->Cell($columnWidths[1], 12, '', 1, ($w == $weeks ? 1 : 0), 'L', $fill);
        }
        $rowCount++;
    }

    // Add 3 extra blank rows for additional attendees
    for ($i = 0; $i < 3; $i++) {
        $fill = ($rowCount % 2 == 0) ? true : false;
        $pdf->SetFillColor(245, 245, 245);
        $pdf->Cell($columnWidths[0], 12, '', 1, 0, 'L', $fill);

        for ($w = 1; $w <= $weeks; $w++) {
            $pdf->Cell($columnWidths[1], 12, '', 1, ($w == $weeks ? 1 : 0), 'L', $fill);
        }
        $rowCount++;
    }
}

add_action('admin_post_print_course_role.pdf', 'course_management_course_role_pdf');

function course_management_cancel_course_enrolment()
{

    // Can user cancel enrolments?
    if (!current_user_can('course_management_enrolments')) {
        wp_redirect(admin_url('/admin.php?page=course-management-course-enrolments'), 301);
        exit;
    }

    $course_id = null;
    $enrolment_id = null;
    $refund = null;

    // Is course ID valid?
    if (
        isset($_GET['course_id']) && !empty($_GET['course_id']) &&
        isset($_GET['enrolment_id']) && !empty($_GET['enrolment_id']) &&
        isset($_GET['refund']) && !empty($_GET['refund'])
    ) {
        $course_id = $_GET['course_id'];
        $enrolment_id = $_GET['enrolment_id'];
        $refund = $_GET['refund'];
    } else {
        wp_redirect(admin_url('/admin.php?page=course-management-course-enrolments'), 301);
        exit;
    }

    freoarts_cancel_enrolment($enrolment_id, $refund);

    wp_redirect(admin_url('/admin.php?page=course-management-course-enrolments&course_id=' . $course_id));
}
add_action('admin_post_cancel_course_enrolment', 'course_management_cancel_course_enrolment');


/**
 * Cancel course enrolments
 *
 */
function course_management_cancel_course_enrolments()
{

    // Can user cancel enrolments?
    if (!current_user_can('course_management_enrolments')) {
        wp_redirect(admin_url('/admin.php?page=course-management-course-enrolments'), 301);
        exit;
    }

    $course_id = null;

    // Is course ID valid?
    if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {
        $course_id = $_GET['course_id'];
    } else {
        wp_redirect(admin_url('/admin.php?page=course-management-course-enrolments'), 301);
        exit;
    }

    // Retrieve enrolments for selected course
    $enrolments = get_admin_course_management_enrolments(false);

    // If enrolments, loop through and cancel
    if (!empty($enrolments) && count($enrolments)) {

        foreach ($enrolments as $enrolment) {

            freoarts_cancel_enrolment($enrolment->id);
        }
    }

    wp_redirect(admin_url('/admin.php?page=course-management-course-enrolments&course_id=' . $course_id));
}
add_action('admin_post_cancel_course_enrolments', 'course_management_cancel_course_enrolments');


/**
 * Cancel the enrolee
 *
 */
function freoarts_cancel_enrolment($enrolment_id = null, $refund_type = 'full')
{

    global $wpdb;
    global $EnrollmentsFactory;

    $refund_amount = 0;

    // Is enrolment ID is submitted via URL query?
    if (isset($_GET['enrolment_id']) && !empty($_GET['enrolment_id'])) {
        $enrolment_id = $_GET['enrolment_id'];
    }

    // Is enrolment ID is submitted via URL query?
    if (isset($_GET['refund']) && !empty($_GET['refund'])) {
        $refund_type = $_GET['refund'];
    }

    // Setup logging
    $log_name = dirname(__FILE__) . '/logs/cancellations-log-' . date('Y-m-d') . '.log';
    $log_file = fopen($log_name, 'a');

    fwrite($log_file, "######################\nFetching enrolment by ID: {$enrolment_id}\n");

    if ($enrolment = $EnrollmentsFactory->fetchEntry($enrolment_id)) {

        // Displayed in email
        $total_paid = 0;

        fwrite($log_file, "Refund type: {$refund_type}\n");

        // If order attached to enrolee and a refund is required?
        if (!empty($enrolment->order_id) && $refund_type != 'none') {

            fwrite($log_file, "Order found #{$enrolment->order_id}, processing refund type: {$refund_type}\n");

            $refund_multiplier = 1;
            if ($refund_type == 'partial') {
                $refund_multiplier = 0.8;
            }

            $sql = "SELECT
                    p.ID,
                    woi.order_item_id,
                    woim1.meta_value AS product_id,
                    woim2.meta_value AS line_total,
                    woim3.meta_value AS line_tax,
                    woim4.meta_value AS qty

                FROM wp_posts AS p

                LEFT JOIN wp_woocommerce_order_items AS woi
                ON woi.order_id = p.ID

                LEFT JOIN wp_woocommerce_order_itemmeta AS woim1
                ON woim1.order_item_id = woi.order_item_id
                AND woim1.meta_key = '_product_id'

                LEFT JOIN wp_woocommerce_order_itemmeta AS woim2
                ON woim2.order_item_id = woi.order_item_id
                AND woim2.meta_key = '_line_total'

                LEFT JOIN wp_woocommerce_order_itemmeta AS woim3
                ON woim3.order_item_id = woi.order_item_id
                AND woim3.meta_key = '_line_tax'

                LEFT JOIN wp_woocommerce_order_itemmeta AS woim4
                ON woim4.order_item_id = woi.order_item_id
                AND woim4.meta_key = '_qty'

                WHERE p.ID = {$enrolment->order_id}
                AND woim1.meta_value = {$enrolment->course_id}";

            $order_item = $wpdb->get_row($sql);

            fwrite($log_file, "Order item data:\n" . print_r($order_item, true));

            /**
             * The following section is copied from the class-wc-ajax.php file
             *
             */

            // Add null check before accessing properties
            if ($order_item && isset($order_item->line_total) && isset($order_item->line_tax)) {
                $total_paid += ($order_item->line_total + $order_item->line_tax);
            }

            // Initialize variables with safe defaults
            $order_id = 0;
            $refund_amount = 0;
            $refund_reason = sanitize_text_field('Cancelled enrolment');
            $line_item_qtys = array();
            $line_item_totals = array();
            $line_item_tax_totals = array();
            
            // Only process if order_item is valid
            if ($order_item && isset($order_item->ID) && isset($order_item->line_total) && 
                isset($order_item->line_tax) && isset($order_item->qty) && isset($order_item->order_item_id)) {
                
                $order_id = absint($order_item->ID);
                $refund_amount = wc_format_decimal(
                    sanitize_text_field(((floatval($order_item->line_total) + floatval($order_item->line_tax)) / $order_item->qty) * $refund_multiplier),
                    wc_get_price_decimals()
                );
                $line_item_qtys = array($order_item->order_item_id => 1);
                $line_item_totals = array(
                    $order_item->order_item_id => ((floatval($order_item->line_total) / $order_item->qty) * $refund_multiplier)
                );
                $line_item_tax_totals = array(
                    $order_item->order_item_id => array(
                        ((floatval($order_item->line_tax) / $order_item->qty) * $refund_multiplier)
                    )
                );
            }
            $api_refund             = true;
            $restock_refunded_items = true;
            $refund                 = false;
            $response_data          = array();

            try {
                // Validate that the refund can occur
                $order       = wc_get_order($order_id);
                $order_items = $order->get_items();
                $max_refund  = wc_format_decimal($order->get_total() - $order->get_total_refunded(), wc_get_price_decimals());

                if (! $refund_amount || $max_refund < $refund_amount || 0 > $refund_amount) {
                    throw new exception(__('Invalid refund amount', 'woocommerce'));
                }

                // Prepare line items which we are refunding
                $line_items = array();
                $item_ids   = array_unique(array_merge(array_keys($line_item_qtys, $line_item_totals)));

                foreach ($item_ids as $item_id) {
                    $line_items[$item_id] = array('qty' => 0, 'refund_total' => 0, 'refund_tax' => array());
                }
                foreach ($line_item_qtys as $item_id => $qty) {
                    $line_items[$item_id]['qty'] = max($qty, 0);
                }
                foreach ($line_item_totals as $item_id => $total) {
                    $line_items[$item_id]['refund_total'] = wc_format_decimal($total);
                }
                foreach ($line_item_tax_totals as $item_id => $tax_totals) {
                    $line_items[$item_id]['refund_tax'] = array_map('wc_format_decimal', $tax_totals);
                }

                // Create the refund object
                $refund = wc_create_refund(array(
                    'amount'     => $refund_amount,
                    'reason'     => $refund_reason,
                    'order_id'   => $order_id,
                    'line_items' => $line_items,
                ));

                if (is_wp_error($refund)) {

                    fwrite($log_file, "Error creating refund object:\n" . print_r($refund->get_error_message(), true) . "\n");
                }

                // Refund via API
                // Disabled: Request 20/10/2020
                /*
                if ( $api_refund ) {
                    if ( WC()->payment_gateways() ) {
                        $payment_gateways = WC()->payment_gateways->payment_gateways();
                    }
                    if ( isset( $payment_gateways[ $order->payment_method ] ) && $payment_gateways[ $order->payment_method ]->supports( 'refunds' ) ) {
                        $result = $payment_gateways[ $order->payment_method ]->process_refund( $order_id, $refund_amount, $refund_reason );

                        do_action( 'woocommerce_refund_processed', $refund, $result );

                        if ( is_wp_error( $result ) ) {
                            fwrite($log_file, "Error refunding:\n" . print_r($refund->get_error_message(), true) . "\n");
                        } elseif ( ! $result ) {
                            fwrite($log_file, "Refund failed\n");
                        }
                    }
                }
                */

                // restock items
                foreach ($line_item_qtys as $item_id => $qty) {
                    if ($restock_refunded_items && $qty && isset($order_items[$item_id])) {
                        $order_item = $order_items[$item_id];
                        // Get product using product ID from order item meta
                        $_product = null;
                        if (is_object($order_item)) {
                            // Try to get product ID from order item meta
                            $product_id = wc_get_order_item_meta($item_id, '_product_id', true);
                            if ($product_id) {
                                $_product = wc_get_product($product_id);
                            }
                        }

                        if ($_product && $_product->exists() && $_product->managing_stock()) {
                            $old_stock    = wc_stock_amount($_product->get_stock_quantity());
                            $new_quantity = $_product->increase_stock($qty);

                            $order->add_order_note(sprintf(__('Item #%s stock increased from %s to %s.', 'woocommerce'), $order_item['product_id'], $old_stock, $new_quantity));

                            do_action('woocommerce_restock_refunded_item', $_product->get_id(), $old_stock, $new_quantity, $order, $_product);
                        }
                    }
                }

                // Trigger notifications and status changes
                if ($order->get_remaining_refund_amount() > 0 || ($order->has_free_item() && $order->get_remaining_refund_items() > 0)) {
                    do_action('woocommerce_order_partially_refunded', $order_id, $refund->get_id(), $refund->get_id());
                } else {
                    do_action('woocommerce_order_fully_refunded', $order_id, $refund->get_id());

                    $order->update_status(apply_filters('woocommerce_order_fully_refunded_status', 'refunded', $order_id, $refund->get_id()));
                    $response_data['status'] = 'fully_refunded';
                }

                do_action('woocommerce_order_refunded', $order_id, $refund->get_id());

                // Clear transients
                wc_delete_shop_order_transients($order_id);
                // wp_send_json_success( $response_data );

                fwrite($log_file, "Successfully refunded.\nUpdating enrolment status.\n");
            } catch (Exception $e) {

                if ($refund && is_a($refund, 'WC_Order_Refund')) {
                    wp_delete_post($refund->get_id(), true);
                }

                fwrite($log_file, "Error refunding:\n" . print_r($e->getMessage(), true) . "\n");
            }
        } else {

            if ($refund_type == 'none') {
                fwrite($log_file, "No refund required. Increasing stock quantity Course ID #{$enrolment->course_id} by 1.\n");
            } else {
                fwrite($log_file, "No order found. Increasing stock quantity Course ID #{$enrolment->course_id} by 1.\n");
            }

            $product = wc_get_product($enrolment->course_id);
            wc_update_product_stock($product, 1, 'increase');
        }

        // Update database record
        $update_result = $wpdb->update(
            'wp_enrolments',
            array(
                'trashed_at' => date('Y-m-d H:i:s'),
                'trashed' => 1
            ),
            array(
                'id' => $enrolment_id
            )
        );

        // Send cancellation email
        $email_args = array(
            'email'         => $enrolment->email,
            'phone'         => $enrolment->phone,
            'course_id'     => $enrolment->course_id,
            'order_id'      => $enrolment->order_id,
            'refund_amount' => $refund_amount,
            'paid_amount'   => $total_paid
        );
        freoarts_send_enrolment_cancellation_email($email_args, $refund_type, $refund_amount);

        if ($update_result) {
            fwrite($log_file, "Successfully updated enrolment status.\n");
        } else {
            fwrite($log_file, "Failed to update enrolment status.\n");
        }
    } else {

        fwrite($log_file, "Failed to fetch enrolment.\n");
    }

    fclose($log_file);
}
