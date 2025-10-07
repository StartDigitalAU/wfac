<?php

class EnrolmentsFactory extends HumaanTableFactory
{

    public $name = 'Enrolments';

    public $table_name = 'enrolments';

    public $admin_menu_icon = 'dashicons-groups';

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
        ),
        array(
            'name'      => 'course_id',
            'label'     => 'Course ID',
            'sql'       => "bigint(20) unsigned NOT NULL",
            'form_new' => array(
                'type'          => 'select',
                'html_fn'       => array('this', 'getCourseInputHTML')
            ),
            'form_edit' => array(
                'type'          => 'select',
                'html_fn'       => array('this', 'getCourseOptionHTML')
            )
        ),
        array(
            'name'      => 'course_name',
            'label'     => 'Course Name',
            'wp_col'    => array(
                'type'          => 'string',
                'select_sql'    => "(SELECT post_title FROM wp_posts WHERE ID = course_id) AS course_name",
                'where_sql'     => "(SELECT post_title FROM wp_posts WHERE ID = course_id)"
            )
        ),
        array(
            'name'      => 'order_id',
            'label'     => 'Order ID',
            'sql'       => "bigint(20) unsigned default NULL",
            'form_new'  => 'input_text',
            'form_new' => array(
                'type'          => 'plain',
                'html_fn'       => array('this', 'getOrderNewHTML')
            ),
            'form_edit' => array(
                'type'          => 'plain',
                'html_fn'       => array('this', 'getOrderLinkHTML')
            )
        ),
        array(
            'name'      => 'user_id',
            'label'     => 'Purchaser',
            'sql'       => "bigint(20) unsigned default NULL",
            'form_edit' => array(
                'type'          => 'plain',
                'html_fn'       => array('this', 'getCustomerLinkHTML')
            )
        ),
        array(
            'name'      => 'title',
            'label'     => 'Title',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'preferred_pronoun',
            'label'     => 'Preferred Pronoun',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'full_name',
            'label'     => 'Full Name',
            'wp_col'    => array(
                'type'          => 'string',
                'select_sql'    => "CONCAT(last_name, ', ', first_name) AS full_name",
                'where_sql'     => "CONCAT(last_name, ', ', first_name)"
            )
        ),
        array(
            'name'      => 'first_name',
            'label'     => 'First Name',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'last_name',
            'label'     => 'Last Name',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'phone',
            'label'     => 'Phone',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text',
        ),
        array(
            'name'      => 'email',
            'label'     => 'Email',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text',
            'wp_col'    => array(
                'type'          => 'string'
            )
        ),
        array(
            'name'      => 'special_requirements',
            'label'     => 'Special Requirements',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'emergency_name',
            'label'     => 'Emergency Contact Name',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'emergency_relationship',
            'label'     => 'Emergency Contact Relationship',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'emergency_phone',
            'label'     => 'Emergency Contact Phone',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'child',
            'label'     => 'Child',
            'sql'       => "tinyint(1) default 0",
            'form_new' => array(
                'type'          => 'select',
                'html_fn'       => array('this', 'getChildOptionHTML')
            ),
            'form_edit' => array(
                'type'          => 'select',
                'html_fn'       => array('this', 'getChildOptionHTML')
            )
        ),
        array(
            'name'      => 'age',
            'label'     => 'Age',
            'sql'       => "bigint(20) default NULL",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'method_of_payment',
            'label'     => 'Method of Payment',
            'sql'       => "varchar(128) NOT NULL default ''",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'notes',
            'label'     => 'Notes',
            'sql'       => "text",
            'form_new'  => 'input_text',
            'form_edit' => 'input_text'
        ),
        array(
            'name'      => 'created_at',
            'label'     => 'Created At',
            'sql'       => "datetime NOT NULL default '0000-00-00 00:00:00'",
            'form_edit' => 'datetime'
        ),
        array(
            'name'      => 'updated_at',
            'label'     => 'Updated At',
            'sql'       => "datetime NOT NULL default '0000-00-00 00:00:00'"
        ),
        array(
            'name'      => 'trashed',
            'label'     => 'Trashed',
            'sql'       => "tinyint(1) NOT NULL DEFAULT 0"
        ),
    );

    /**
     * Create a new entry
     *
     * @return bool|int
     */
    public function createEntry()
    {

        $order_id       = null;
        $user_id        = null;

        if (isset($_POST['order_id']) && !empty($_POST['order_id'])) {

            $order_id = $_POST['order_id'];

            $order = new WC_Order($order_id);

            if (!empty($order->get_user_id())) {

                $user_id = $order->get_user_id();
            }
        }

        $enrolment = array(
            'course_id'                 => $_POST['course_id'],
            'order_id'                  => $order_id,
            'user_id'                   => $user_id,
            'title'                     => ifne($_POST, 'title'),
            'preferred_pronoun'         => ifne($_POST, 'preferred_pronoun'),
            'first_name'                => $_POST['first_name'],
            'last_name'                 => $_POST['last_name'],
            'phone'                     => $_POST['phone'],
            'email'                     => $_POST['email'],
            'special_requirements'      => $_POST['special_requirements'],
            'emergency_name'            => $_POST['emergency_name'],
            'emergency_relationship'    => $_POST['emergency_relationship'],
            'emergency_phone'           => $_POST['emergency_phone'],
            'child'                     => $_POST['child'],
            'age'                       => $_POST['age'],
            'method_of_payment'         => $_POST['method_of_payment'],
            'notes'                     => $_POST['notes'],
            'created_at'                => date('Y-m-d H:i:s'),
            'updated_at'                => date('Y-m-d H:i:s')
        );

        $response = $this->wpdb->insert(
            $this->wp_table_name,
            $enrolment
        );

        if ($response) {

            /*
            // If no order was created, assume that the stock quantity of the course was not updated, the subtract 1 from the stock quantity
            if (empty($order_id)) {

                $product = wc_get_product($_POST['course_id']);
                $product->set_stock(1, 'subtract');
            }
            */

            freoarts_send_enrolment_confirmation_email($enrolment);

            return $this->wpdb->insert_id;
        }

        return false;
    }

    /**
     * Update an existing entry
     *
     * @param $entry_id
     * @return false|int
     */
    public function updateEntry( $entry_id )
    {

        $enrolment = array(
            'course_id'                 => $_POST['course_id'],
            'title'                     => ifne($_POST, 'title'),
            'preferred_pronoun'         => ifne($_POST, 'preferred_pronoun'),
            'first_name'                => $_POST['first_name'],
            'last_name'                 => $_POST['last_name'],
            'phone'                     => $_POST['phone'],
            'email'                     => $_POST['email'],
            'special_requirements'      => $_POST['special_requirements'],
            'emergency_name'            => $_POST['emergency_name'],
            'emergency_relationship'    => $_POST['emergency_relationship'],
            'emergency_phone'           => $_POST['emergency_phone'],
            'child'                     => $_POST['child'],
            'age'                       => $_POST['age'],
            'method_of_payment'         => $_POST['method_of_payment'],
            'notes'                     => $_POST['notes'],
            'updated_at'                => date('Y-m-d H:i:s')
        );

        return $this->wpdb->update(
            $this->wp_table_name,
            $enrolment,
            array(
                'id' => $entry_id
            )
        );
    }

    /**
     * Get the HTML for the list filters
     *
     * @return string
     */
    public function getFiltersHTML()
    {

        $course_id = null;

        if (isset($_REQUEST['filters']['course_id']) && !empty($_REQUEST['filters']['course_id'])) {

            $course_id = $_REQUEST['filters']['course_id'];
        }

        ob_start();
        ?>
        <div class="alignleft actions">
            <label class="screen-reader-text" for="course">Filter by Course</label>
            <select id="course" name="filters[course_id]" class="postform">
                <option value="">Please Select</option>
                <?php
                $courses = $this->getCourses();

                foreach ($courses as $course) {

                    $selected = ($course->ID == $course_id) ? ' selected="selected"' : '';
                    echo '<option value="' . $course->ID . '"' . $selected . '>' . $course->post_title . '</option>';
                }
                ?>
            </select>
            <input class="button" type="submit" name="filter_action" value="Filter">
        </div>
        <?php
        $html = ob_get_clean();

        return $html;
    }

    /**
     * Render hidden Input HTML for Course
     *
     * @param null $selected_id
     * @return string
     */
    public function getCourseInputHTML($name, $value = null)
    {

        if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {

            $course = $this->getCourse($_GET['course_id']);

            $html = $course->post_title . ' <input type="hidden" name="' . $name . '" value="' . $_GET['course_id'] . '" />';
        }
        else {

            $html = ' <input type="number" name="' . $name . '" value="" /><br><em>Enter ID of course.</em>';
        }

        return $html;
    }

    /**
     * Render Select Option HTML for Course selection
     *
     * @param null $selected_id
     * @return string
     */
    public function getCourseOptionHTML($name, $value = null)
    {

        /*
         * Decided to not allow user to change enrolee's course (it would break order relationship)
         *
        $courses = $this->getCourses();

        $html = '<select id="' . $name . '" name="' . $name . '">';

        $html .= '<option value="">Please Select</option>';

        foreach ($courses as $course) {

            $selected = ($course->ID == $value) ? ' selected="selected"' : '';
            $html .= '<option value="' . $course->ID . '"' . $selected . '>' . $course->post_title . '</option>';
        }

        $html .= '</select>';

        $html .= '<br><a href="' . get_edit_post_link($course->ID) . '">View Course</a>';
        */

        $course = $this->getCourse($value);

        $html = $course->post_title . ' <input type="hidden" name="' . $name . '" value="' . $value . '" />';

        return $html;
    }

    /**
     * Render Select Option HTML for age selection
     *
     * @param bool|false $is_child
     * @return string
     */
    public function getChildOptionHTML($name, $value = false)
    {

        $html = '<select id="' . $name . '" name="' . $name . '">';

        $selected = (!$value) ? ' selected="selected"' : '';
        $html .= '<option value="1"' . $selected . '>No</option>';

        $selected = ($value) ? ' selected="selected"' : '';
        $html .= '<option value="1"' . $selected . '>Yes</option>';

        $html .= '</select>';

        return $html;
    }

    /**
     * Render link for order
     *
     * @param $name
     * @param $value
     * @return string
     */
    public function getOrderNewHTML($name, $value)
    {

        $where_course_id = '';
        if (isset($_GET['course_id']) && !empty($_GET['course_id'])) {

            $where_course_id = " AND woim.meta_value = {$_GET['course_id']}";
        }

        $sql = "SELECT
                    p.ID

                FROM wp_posts AS p

                INNER JOIN wp_woocommerce_order_items AS woi
                ON woi.order_id = p.ID

                INNER JOIN wp_woocommerce_order_itemmeta AS woim
                ON woim.order_item_id = woi.order_item_id
                AND woim.meta_key = '_product_id'

                WHERE p.post_type = 'shop_order'
                {$where_course_id}

                ORDER BY p.ID DESC";

        $orders = $this->wpdb->get_results($sql);

        $html = '<select id="' . $name . '" name="' . $name . '">';

        $html .= '<option value="">No Associated Order</option>';

        foreach ($orders as $order) {

            $selected = ($order->ID == $value) ? ' selected="selected"' : '';
            $html .= '<option value="' . $order->ID . '"' . $selected . '>#' . $order->ID . '</option>';
        }

        $html .= '</select>';

        $html .= '<em>This is a list of Orders containing the selected course. Leave blank to not associate an order with this enrolment.</em>';

        return $html;
    }

    /**
     * Render field for order
     *
     * @param $name
     * @param $value
     * @return string
     */
    public function getOrderLinkHTML($name, $value)
    {

        if (!empty($value)) {
            $html = '#' . $value . '<br><a href="' . get_edit_post_link($value) . '">View Order</a>';
        }
        else {
            $html = 'No Associated Order';
        }

        return $html;
    }

    /**
     * Render link for customer
     *
     * @param $name
     * @param $value
     * @return string
     */
    public function getCustomerLinkHTML($name, $value)
    {

        if (!empty($value)) {

            $user_data = get_userdata($value);
            $html = $user_data->user_nicename . '<br><a href="' . get_edit_user_link($value) . '">View Account</a>';
        }
        else {

            $html = 'Guest (No Account Created)';
        }

        return $html;
    }

    /**
     * Get course
     *
     * @param null $course_id
     * @return array|bool|null|WP_Post
     */
    private function getCourse($course_id = null)
    {

        if ($course = get_post($course_id)) {
            return $course;
        }

        return false;
    }

    /**
     * Get array of all published courses
     *
     * @return array
     */
    private function getCourses()
    {

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

        return $courses;
    }

    public function afterFormHTML($entry_id) {

        $entry = $this->fetchEntry($entry_id);
        ?>
        <?php if (isset($entry->is_archived) && $entry->is_archived) { ?>
                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        Archived Course
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <a href="<?php echo admin_url( 'admin.php?page=archived_courses_view&entry=' . $entry->archived_course_id ); ?>" title="Printable version" target="_blank">View Archived Course</a>
                        </div>
                    </div>
                </div>
        <?php } ?>
        <?php
    }
}