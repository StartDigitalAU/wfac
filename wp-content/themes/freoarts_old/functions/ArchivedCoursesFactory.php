<?php

class ArchivedCoursesFactory extends HumaanTableFactory
{

    public $name = 'Archived Courses';

    public $table_name = 'archived_courses';

    public $admin_menu_icon = 'dashicons-backup';

    public $admin_menu_position = '401';

    public $columns = array(
        /*  array(
            'name'      => 'course_id', // column key
            'label'     => 'Course ID', // column name
            'sql'       => "bigint(20) unsigned NOT NULL", // insert SQL
            'form_type' => 'select', // HTML form type (plain|input_text|select)
            'options'   => 'getCourseOptionHTML', // options for 'select' form type
            'wp_col'    => array(
                'type'          => 'string',
                'select_sql'    => "(SELECT post_title FROM wp_posts WHERE ID = course_id) AS course_name",
                'where_sql'     => "(SELECT post_title FROM wp_posts WHERE ID = course_id)"
            )
        ) */
        array(
            'name'  => 'id',
            'label' => 'ID',
            'sql'   => "bigint(20) unsigned NOT NULL auto_increment",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'course_id',
            'label'     => 'Course ID',
            'sql'       => "bigint(20) unsigned default NULL",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'course_title',
            'label'     => 'Course Title',
            'sql'       => "varchar(128) NOT NULL default ''",
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'post_content',
            'label'     => 'Content',
            'sql'       => "text NOT NULL default ''",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'qty_available',
            'label'     => 'Qty Available',
            'sql'       => "bigint(20) unsigned default NULL",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'qty_sold',
            'label'     => 'Qty Sold',
            'sql'       => "bigint(20) unsigned default NULL",
            'wp_col'    => array(
                'type'          => 'string'
            ),
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'is_kids_course',
            'label'     => 'Is Kids Course',
            'sql'       => "tinyint(1) default 0",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'categories',
            'label'     => 'Categories',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'release_date',
            'label'     => 'Release Date',
            'sql'       => "datetime NOT NULL default '0000-00-00 00:00:00'",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'start_date',
            'label'     => 'Start Date',
            'sql'       => "datetime NOT NULL default '0000-00-00 00:00:00'",
            'form_edit' => 'plain',
            'wp_col'    => array(
                'type'          => 'string'
            )
        ),
        array(
            'name'      => 'end_date',
            'label'     => 'End Date',
            'sql'       => "datetime NOT NULL default '0000-00-00 00:00:00'",
            'form_edit' => 'plain',
            'wp_col'    => array(
                'type'          => 'string'
            )
        ),
        array(
            'name'      => 'summary',
            'label'     => 'Summary',
            'sql'       => "text NOT NULL default ''",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'difficulty',
            'label'     => 'Difficulty',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'tutor_id',
            'label'     => 'Tutor ID',
            'sql'       => "bigint(20) unsigned default NULL",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'tutor_name',
            'label'     => 'Tutor Name',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'duration',
            'label'     => 'Duration',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'hero_image',
            'label'     => 'Hero Image',
            'sql'       => "bigint(20) unsigned default NULL",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'info_image',
            'label'     => 'Info Image',
            'sql'       => "bigint(20) unsigned default NULL",
            'form_edit' => 'plain'
        ),
        array(
            'name'      => 'created_at',
            'label'     => 'Archived At',
            'sql'       => "datetime NOT NULL default '0000-00-00 00:00:00'",
            'form_edit' => 'plain'
        )
    );

    /**
     * Get the HTML for the list filters
     *
     * @return string
     */
    public function getFiltersHTML()
    {

        $html = '';

        return $html;
    }

    public function initCustom()
    {

        add_action('admin_post_' . $this->table_name . '_archive_course', array($this, 'archiveCourse'));
    }

    public function afterFormHTML($entry_id) {

        global $wpdb;
        ?>

        <div class="acf-field acf-field-text">
            <div class="acf-label">
                <label for="images">Enrolments</label>
            </div>
            <div class="acf-input">
                <div class="acf-input-wrap">
                    <?php
                    $sql = "SELECT * FROM wp_enrolments WHERE archived_course_id = " . $entry_id;
                    $results = $wpdb->get_results($sql, OBJECT);
                    ?>
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
                                </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; ?>
                                <?php foreach ($results as $result) { ?>
                                    <?php
                                    $user = get_userdata($result->user_id);
                                    ?>
                                    <tr class="acf-row"<?php if ($result->trashed) { ?> style="opacity: 0.5;"<?php } ?>>
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
                                            echo (!empty($result->age)) ? $result->age: 'N/A';
                                            ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->phone; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->email; ?>
                                        </td>
                                    </tr>
                                    <?php $i++; ?>
                                <?php } ?>
                                </tbody>
                            </table>

                        </div>

                        <p>
                            <?php $url = admin_url('admin.php?page=course-management-bulk-email&archived_course=1&course_id=' . $entry_id); ?>
                            <a class="button" href="<?php echo $url; ?>" title="Bulk Email Enrolees">Bulk Email Active Enrolees</a>
                        </p>

                    <?php } else { ?>
                        <p>Currently no enrolments for this course.</p>
                    <?php } ?>
                </div>
            </div>
        </div>

        <?php
    }

    public function archiveCourse()
    {

        if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {

            $archived_course_id = archive_course($_GET['course_id']);

            header('Location: ' . admin_url( 'admin.php?page=archived_courses_view&entry=' . $archived_course_id));
        }
        else {

            header('Location: ' . admin_url( 'admin.php?page=archived_courses_list'));
        }
    }
}