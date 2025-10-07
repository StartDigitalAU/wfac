<?php

/****************************************************
 *
 * WOOCOMMERCE - MY ACCOUNT
 *
 ****************************************************/

/**
 * Update the WooCommerce My Account menu
 *
 * @param $items
 * @return mixed
 */
function woocommerce_update_account_menu_items( $items )
{

    if (isset($items['downloads'])) {

        unset($items['downloads']);
    }
    if (isset($items['edit-address'])) {

        $items['edit-address'] = __('Addresses', 'textdomain');
    }

    return $items;
}
add_filter('woocommerce_account_menu_items', 'woocommerce_update_account_menu_items');

function list_my_account_courses()
{
    global $wpdb;

    if ($user_id = get_current_user_id()) {

        $sql = "SELECT
                  e.*,
                  pm.meta_value AS start_date

                FROM wp_enrolments AS e

                LEFT JOIN wp_postmeta AS pm
                ON pm.post_id = e.course_id
                AND pm.meta_key = 'start_date'

                WHERE pm.meta_value >= '" . date('Ymd') . "'
                AND e.user_id = '" . $user_id . "'
                AND e.trashed != 1
                AND e.is_archived != 1
                ";

        $enrolments = $wpdb->get_results($sql);
        ?>

        <?php if (!empty($enrolments)) { ?>
            <h3>Enrolled Courses</h3>

            <table class="woocommerce-MyAccount-orders shop_table shop_table_responsive my_account_orders account-orders-table">
                <thead>
                    <tr>
                        <th><span class="nobr">Course</span></th>
                        <th><span class="nobr">Enrolled</span></th>
                        <th><span class="nobr">Date</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($enrolments as $enrolment) { ?>
                        <?php
                        $_product = wc_get_product($enrolment->course_id);
                        $fields = get_fields($enrolment->course_id);

                        $class = ($fields['start_date'] >= date('Ymd')) ? 'upcoming': 'past';
                        ?>
                        <tr class="<?php echo $class; ?>">
                            <td>
                                <?php echo $_product->get_name(); ?>
                            </td>
                            <td>
                                <?php echo $enrolment->first_name . ' ' . $enrolment->last_name; ?>
                            </td>
                            <td>
                                <?php echo get_formatted_datetime($fields); ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <?php
    }
}
add_action('woocommerce_account_dashboard', 'list_my_account_courses');