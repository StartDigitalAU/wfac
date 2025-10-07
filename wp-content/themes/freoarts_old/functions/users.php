<?php

/****************************************************
 *
 * USERS
 *
 ****************************************************/

/**
 * Add user column header for expiry date
 *
 * @param $column_headers
 * @return mixed
 */
function update_users_columns($column_headers)
{

    unset($column_headers['posts']);

    $column_headers['expiry_date'] = 'Expiry Date';

    return $column_headers;
}
add_action('manage_users_columns', 'update_users_columns');

/**
 * Populate user column header for expiry date
 *
 * @param $value
 * @param $column_name
 * @param $user_id
 * @return bool|mixed|string
 */
function user_expiry_date_column_content($value, $column_name, $user_id)
{

    if ('expiry_date' == $column_name) {

        $value = get_user_meta($user_id, 'expiry', true);
        if (!empty($value)) {
            $value = date('Y/m/d', strtotime($value));
        }
        else {
            $value = '';
        }

    }

    return $value;
}
add_action('manage_users_custom_column', 'user_expiry_date_column_content', 10, 3);

/**
 * Make user column header for expiry date sortable
 *
 * @param $columns
 * @return mixed
 */
function custom_sortable_expiry_column( $columns )
{

    $columns['expiry_date'] = 'expiry';

    return $columns;
}
add_filter('manage_users_sortable_columns', 'custom_sortable_expiry_column');

/**
 * Customise the user query for adding the expiry date
 *
 * @param $query
 */
function custom_pre_user_query($query)
{

    global $wpdb, $current_screen;

    // Only filter in the admin
    if (!is_admin()) {
        return;
    }

    // Only filter on the users screen
    if (!( isset($current_screen) && 'users' == $current_screen->id)) {
        return;
    }

    // Only filter if orderby is set to 'expiry'
    if (isset($query->query_vars) && isset($query->query_vars['orderby']) && ('expiry' == $query->query_vars['orderby'])) {

        // We need the order - default is ASC
        $order = isset($query->query_vars) && isset($query->query_vars['order']) && strcasecmp( $query->query_vars[ 'order' ], 'desc' ) == 0 ? 'DESC' : 'ASC';

        // Order the posts by product count
        $query->query_orderby = "ORDER BY (SELECT meta_value AS expiry FROM wp_usermeta WHERE wp_usermeta.user_id = {$wpdb->users}.ID AND wp_usermeta.meta_key = 'expiry') {$order}";
    }
}
add_action('pre_user_query', 'custom_pre_user_query', 1);

/**
 * Create menu item for exporting member data (i.e. users)
 *
 */
function export_members_csv_menu_item()
{

    $hook = add_submenu_page(
        'users.php',
        'Export Members CSV',
        'Export Members CSV',
        'manage_options',
        'export_members_csv',
        'export_members_csv'
    );
    add_action("load-$hook", 'export_members_csv');
}
add_action('admin_menu', 'export_members_csv_menu_item');

/**
 * Export members in CSV format
 *
 */
function export_members_csv()
{

    global $wpdb;

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=members.csv');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    $sql = "SELECT
                u.user_email,
                um1.meta_value AS first_name,
                um2.meta_value AS last_name,
                CASE
                    WHEN LOCATE('member_individual', um4.meta_value) THEN 'Individual Member'
                    WHEN LOCATE('member_concession', um4.meta_value) THEN 'Concession Member'
                    ELSE 'Subscriber'
                END AS role,
                um3.meta_value AS expiry,
                um5.meta_value AS address_1,
                um6.meta_value AS address_2,
                um7.meta_value AS city,
                um8.meta_value AS state,
                um9.meta_value AS postcode

            FROM wp_users AS u

            LEFT JOIN wp_usermeta AS um1
            ON um1.user_id = u.ID
            AND um1.meta_key = 'first_name'

            LEFT JOIN wp_usermeta AS um2
            ON um2.user_id = u.ID
            AND um2.meta_key = 'last_name'

            LEFT JOIN wp_usermeta AS um3
            ON um3.user_id = u.ID
            AND um3.meta_key = 'expiry'

            LEFT JOIN wp_usermeta AS um4
            ON um4.user_id = u.ID
            AND um4.meta_key = 'wp_capabilities'

            LEFT JOIN wp_usermeta AS um5
            ON um5.user_id = u.ID
            AND um5.meta_key = 'billing_address_1'

            LEFT JOIN wp_usermeta AS um6
            ON um6.user_id = u.ID
            AND um6.meta_key = 'billing_address_2'

            LEFT JOIN wp_usermeta AS um7
            ON um7.user_id = u.ID
            AND um7.meta_key = 'billing_city'

            LEFT JOIN wp_usermeta AS um8
            ON um8.user_id = u.ID
            AND um8.meta_key = 'billing_state'

            LEFT JOIN wp_usermeta AS um9
            ON um9.user_id = u.ID
            AND um9.meta_key = 'billing_postcode'

            WHERE u.user_email != ''
            
            AND um3.meta_value >= '" . date('Y-m-d') . "'

            ORDER BY expiry DESC
            ";

    $results = $wpdb->get_results($sql, ARRAY_N);

    fputcsv(
        $output,
        array(
            'email',
            'first_name',
            'last_name',
            'role',
            'expiry',
            'address_1',
            'address_2',
            'city',
            'state',
            'postcode'
        )
    );

    foreach ($results as $user) {

        fputcsv(
            $output,
            $user
        );
    }

    die();
}

/**
 * Add expiry date to user edit screen
 *
 * @param $user
 */
function show_extra_user_fields($user)
{
    ?>

    <h3>Member Information</h3>

    <table class="form-table">
        <tr>
            <th>
                <label for="expiry">Membership Expiry Date</label>
            </th>
            <td>
                <input class="date-picker" type="text" name="expiry" id="expiry" value="<?php echo esc_attr( get_the_author_meta( 'expiry', $user->ID ) ); ?>" class="regular-text" />
                <p class="description">The date at which the user's current membership period will lapse.</p>
            </td>
        </tr>
        <tr>
            <th>
                <label for="expiry">Special Requirements</label>
            </th>
            <td>
                <input type="text" name="special_requirements" id="special_requirements" value="<?php echo esc_attr( get_the_author_meta( 'special_requirements', $user->ID ) ); ?>" class="regular-text" />
                <p class="description">Special requirements for anyone enrolled into a course by this user. Can be override during the checkout process.</p>
            </td>
        </tr>
        <tr>
            <th>
                <label for="expiry">Emergency Contact Name</label>
            </th>
            <td>
                <input type="text" name="emergency_name" id="emergency_name" value="<?php echo esc_attr( get_the_author_meta( 'emergency_name', $user->ID ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="expiry">Emergency Contact Relationship</label>
            </th>
            <td>
                <input type="text" name="emergency_relationship" id="emergency_relationship" value="<?php echo esc_attr( get_the_author_meta( 'emergency_relationship', $user->ID ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="expiry">Emergency Contact Phone</label>
            </th>
            <td>
                <input type="text" name="emergency_phone" id="emergency_phone" value="<?php echo esc_attr( get_the_author_meta( 'emergency_phone', $user->ID ) ); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
<?php
}
add_action('show_user_profile', 'show_extra_user_fields');
add_action('edit_user_profile', 'show_extra_user_fields');


function save_extra_user_fields($user_id)
{

    if (!current_user_can('edit_user', $user_id)) {

        return false;
    }

    update_user_meta($user_id, 'expiry', $_POST['expiry']);
    update_user_meta($user_id, 'special_requirements', $_POST['special_requirements']);
    update_user_meta($user_id, 'emergency_name', $_POST['emergency_name']);
    update_user_meta($user_id, 'emergency_relationship', $_POST['emergency_relationship']);
    update_user_meta($user_id, 'emergency_phone', $_POST['emergency_phone']);
}
add_action('personal_options_update', 'save_extra_user_fields');
add_action('edit_user_profile_update', 'save_extra_user_fields');

/**
 * Render list of courses and associated enrollees on User edit screen
 *
 * @param $profileuser
 */
function list_user_courses_and_enrollees($profileuser) {

    global $wpdb;

    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $profileuser->ID,
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_order_statuses() ),
    ));

    if (!empty($customer_orders)) {
        ob_start();
        ?>
        <h2>Customer's Course Enrolment Orders</h2>
        <table class="acf-table">
            <thead>
                <tr>
                    <th class="acf-th acf-th-text" style="width: 30%;">Order</th>
                    <th class="acf-th acf-th-text" style="width: 70%;">Course / Enrollee</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($customer_orders as $order) { ?>
                <?php
                $sql = "SELECT * FROM wp_enrolments WHERE order_id = '" . $order->ID . "'";
                $enrolments = $wpdb->get_results($sql);
                ?>
                <?php if (!empty($enrolments)) { ?>
                <tr class="acf-row">
                    <td class="acf-field acf-field-text">
                        <a href="<?php echo get_edit_post_link($order->ID); ?>">Order #<?php echo $order->ID; ?></a>
                    </td>
                    <td class="acf-field acf-field-text">
                        <?php foreach ($enrolments as $enrollee) { ?>
                            <span style="display: block;">
                                <a href="<?php echo get_edit_post_link($enrollee->course_id); ?>"><?php echo get_the_title($enrollee->course_id); ?></a>
                                /
                                <?php $url = admin_url('admin.php?page=enrolments_view&entry=' . $enrollee->id); ?>
                                <a href="<?php echo $url; ?>"><?php echo $enrollee->last_name; ?>, <?php echo $enrollee->first_name; ?></a>
                            </span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
            </table>
        <?php
        $output .= ob_get_contents();
        ob_end_clean();

        echo $output;
    }
}
add_action('edit_user_profile', 'list_user_courses_and_enrollees');