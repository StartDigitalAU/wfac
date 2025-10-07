<?php

/****************************************************
 *
 * REPORTING - MEMBERSHIP SUBSCRIPTIONS
 *
 ****************************************************/

function get_admin_report_membership_subscriptions()
{

    global $wpdb;

    $filter_dates = '';

    if (!empty($_GET['date_from'])) {
        $filter_dates .= " AND p.post_date >= '" . $_GET['date_from'];

        $hours = !empty($_GET['time_from_hour']) ? str_pad($_GET['time_from_hour'], 2, '0', STR_PAD_LEFT) : '00';
        $minutes = !empty($_GET['time_from_minute']) ? str_pad($_GET['time_from_minute'], 2, '0', STR_PAD_LEFT) : '00';

        $filter_dates .= ' ' . $hours . ':' . $minutes . ":00'";
    }

    if (!empty($_GET['date_to'])) {
        $filter_dates .= " AND p.post_date <= '" . $_GET['date_to'];

        $hours = !empty($_GET['time_to_hour']) ? str_pad($_GET['time_to_hour'], 2, '0', STR_PAD_LEFT) : '00';
        $minutes = !empty($_GET['time_to_minute']) ? str_pad($_GET['time_to_minute'], 2, '0', STR_PAD_LEFT) : '00';

        $filter_dates .= ' ' . $hours . ':' . $minutes . ":00'";
    }

    if (empty($filter_dates)) {
        return array();
    }

    $sql = "SELECT
        p.ID AS order_number,
        p.post_date AS date,
        woi.order_item_name AS type,
        woim1.meta_value AS total,
        woim2.meta_value AS tax,
        um1.meta_value AS first_name,
        um2.meta_value AS last_name,
        um3.meta_value AS address_1,
        um4.meta_value AS address_2,
        um5.meta_value AS city,
        um6.meta_value AS state,
        um7.meta_value AS postcode,
        um8.meta_value AS expiry,
        (
            SELECT
                COUNT(sub_pm.post_id)
            
            FROM wp_postmeta AS sub_pm
        
            INNER JOIN wp_woocommerce_order_items AS sub_woim
            ON sub_pm.post_id = sub_woim.order_id
            AND sub_woim.order_item_name IN ('Individual Membership', 'Concession Membership')
        
            WHERE sub_pm.meta_key = '_customer_user'
            AND sub_pm.meta_value = pm.meta_value
        ) AS subscription_count
        
    FROM wp_woocommerce_order_items AS woi
    
    INNER JOIN wp_posts AS p
    ON woi.order_id = p.ID
    
    INNER JOIN wp_woocommerce_order_itemmeta AS woim1
    ON woi.order_item_id = woim1.order_item_id 
    AND woim1.meta_key = '_line_total'
    
    INNER JOIN wp_woocommerce_order_itemmeta AS woim2
    ON woi.order_item_id = woim2.order_item_id 
    AND woim2.meta_key = '_line_tax'
    
    INNER JOIN wp_postmeta AS pm
    ON p.ID = pm.post_id
    AND pm.meta_key = '_customer_user'
    
    LEFT JOIN wp_usermeta AS um1
    ON pm.meta_value = um1.user_id
    AND um1.meta_key = 'first_name'
    
    LEFT JOIN wp_usermeta AS um2
    ON pm.meta_value = um2.user_id
    AND um2.meta_key = 'last_name'
    
    LEFT JOIN wp_usermeta AS um3
    ON pm.meta_value = um3.user_id
    AND um3.meta_key = 'billing_address_1'
    
    LEFT JOIN wp_usermeta AS um4
    ON pm.meta_value = um4.user_id
    AND um4.meta_key = 'billing_address_2'
    
    LEFT JOIN wp_usermeta AS um5
    ON pm.meta_value = um5.user_id
    AND um5.meta_key = 'billing_city'
    
    LEFT JOIN wp_usermeta AS um6
    ON pm.meta_value = um6.user_id
    AND um6.meta_key = 'billing_state'
    
    LEFT JOIN wp_usermeta AS um7
    ON pm.meta_value = um7.user_id
    AND um7.meta_key = 'billing_postcode'
    
    LEFT JOIN wp_usermeta AS um8
    ON pm.meta_value = um8.user_id
    AND um8.meta_key = 'expiry'
    
    WHERE woi.order_item_name IN ('Individual Membership', 'Concession Membership')
    
	AND p.post_status = 'wc-completed'
    
    {$filter_dates}

    ORDER BY p.post_date DESC;
    ";

    $results = $wpdb->get_results($sql, OBJECT);

    return $results;
}

/**
 * Display the admin Membership Subscriptions report
 *
 */
function view_admin_report_membership_subscriptions()
{

    $date_from = !empty($_GET['date_from']) ? $_GET['date_from'] : '';
    $time_from_hour = !empty($_GET['time_from_hour']) ? $_GET['time_from_hour'] : '';
    $time_from_minute = !empty($_GET['time_from_minute']) ? $_GET['time_from_minute'] : '';

    $date_to = !empty($_GET['date_to']) ? $_GET['date_to'] : '';
    $time_to_hour = !empty($_GET['time_to_hour']) ? $_GET['time_to_hour'] : '';
    $time_to_minute = !empty($_GET['time_to_minute']) ? $_GET['time_to_minute'] : '';

    $results = get_admin_report_membership_subscriptions();
    ?>
    <div class="wrap">

        <h1>Membership Subscriptions Report</h1>

        <form id="posts-filter" action="" method="get">

            <input type="hidden" name="page" value="reports-membership-subscriptions" />

            <div class="tablenav top">
                <div class="alignleft actions">

                    <input type="text" name="date_from" class="date-picker" style="float: left;" placeholder="From Date" value="<?php echo $date_from; ?>"/>
                    <select name="time_from_hour" class="postform">
                        <?php for ($i = 0; $i <= 23; $i++) { ?>
                            <?php
                            $val = str_pad($i, 2, '0', STR_PAD_LEFT);
                            $selected = ($time_from_hour == $val) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $val; ?>"<?php echo $selected; ?>><?php echo $val; ?></option>
                        <?php } ?>
                    </select>
                    <select name="time_from_minute" class="postform">
                        <?php for ($i = 0; $i <= 59; $i++) { ?>
                            <?php
                            $val = str_pad($i, 2, '0', STR_PAD_LEFT);
                            $selected = ($time_from_minute == $val) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $val; ?>"<?php echo $selected; ?>><?php echo $val; ?></option>
                        <?php } ?>
                    </select>

                    <input type="text" name="date_to" class="date-picker" style="float: left;" placeholder="To Date" value="<?php echo $date_to; ?>"/>
                    <select name="time_to_hour" class="postform">
                        <?php for ($i = 0; $i <= 23; $i++) { ?>
                            <?php
                            $val = str_pad($i, 2, '0', STR_PAD_LEFT);
                            $selected = ($time_to_hour == $val) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $val; ?>"<?php echo $selected; ?>><?php echo $val; ?></option>
                        <?php } ?>
                    </select>
                    <select name="time_to_minute" class="postform">
                        <?php for ($i = 0; $i <= 59; $i++) { ?>
                            <?php
                            $val = str_pad($i, 2, '0', STR_PAD_LEFT);
                            $selected = ($time_to_minute == $val) ? ' selected="selected"' : '';
                            ?>
                            <option value="<?php echo $val; ?>"<?php echo $selected; ?>><?php echo $val; ?></option>
                        <?php } ?>
                    </select>

                    <input class="button" type="submit" value="Filter">
                </div>

                <?php if (count($results)) { ?>
                    <div class="alignright actions">
                        <?php
                        $url = admin_url('admin-post.php?action=print_report_membership_subscriptions.csv');

                        $url .= isset($_GET['date_from']) ? '&date_from=' . $_GET['date_from']: '';
                        $url .= isset($_GET['time_from_hour']) ? '&time_from_hour=' . $_GET['time_from_hour']: '';
                        $url .= isset($_GET['time_from_minute']) ? '&time_from_minute=' . $_GET['time_from_minute']: '';
                        $url .= isset($_GET['date_to']) ? '&date_to=' . $_GET['date_to']: '';
                        $url .= isset($_GET['time_to_hour']) ? '&time_to_hour=' . $_GET['time_to_hour']: '';
                        $url .= isset($_GET['time_to_minute']) ? '&time_to_minute=' . $_GET['time_to_minute']: '';
                        ?>
                        <a href="<?php echo $url; ?>" class="button">Export CSV</a>
                    </div>
                <?php } ?>
            </div>

            <div class="acf-field acf-field-repeater">

                <?php if (count($results)) { ?>
                    <div class="acf-input">
                        <table class="acf-table">
                            <thead>
                                <tr>
                                    <th class="acf-th acf-th-text"></th>
                                    <th class="acf-th acf-th-text">Order ID</th>
                                    <th class="acf-th acf-th-text">Date</th>
                                    <th class="acf-th acf-th-text">Type</th>
                                    <th class="acf-th acf-th-text">Renewal</th>
                                    <th class="acf-th acf-th-text align-right">Amount Paid</th>
                                    <th class="acf-th acf-th-text">Name</th>
                                    <th class="acf-th acf-th-text">Address</th>
                                    <th class="acf-th acf-th-text">Expiry</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                ?>
                                <?php foreach ($results as $result) { ?>
                                    <?php
                                    $i++;
                                    ?>
                                    <tr class="acf-row">
                                        <td class="acf-field acf-field-text">
                                            <?php echo $i; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <a href="<?php echo get_edit_post_link($result->order_number); ?>" title="View Order">
                                                <?php echo $result->order_number; ?>
                                            </a>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->date; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->type; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo ($result->subscription_count > 1) ? 'Renewal' : 'New'; ?>
                                        </td>
                                        <td class="acf-field acf-field-text align-right">
                                            <?php
                                            echo number_format($result->total + $result->tax, 2, '.', ' ');
                                            ?>
                                            (inc.GST)
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->first_name . ' ' . $result->last_name; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php

                                            $address = !empty($result->address_1) ? $result->address_1 : '';
                                            $address .= !empty($result->address_2) ? ', ' . $result->address_2 : '';
                                            $address .= !empty($result->city) ? ', ' . $result->city : '';
                                            $address .= !empty($result->state) ? ', ' . $result->state : '';
                                            $address .= !empty($result->postcode) ? ', ' . $result->postcode : '';

                                            echo $address;
                                            ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo date('Y-m-d', strtotime($result->expiry)); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <p>No results for current filter.</p>
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
function print_report_csv_membership_subscriptions()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $results = get_admin_report_membership_subscriptions();

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=membership-subscriptions.csv');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    fputcsv(
        $output,
        array(
            'Order ID',
            'Date',
            'Type',
            'Renewal',
            'Amount Paid',
            'Name',
            'Address',
            'Expiry'
        )
    );

    $i = 0;

    foreach ($results as $result) {

        $i++;

        $renewal = ($result->subscription_count > 1) ? 'Renewal' : 'New';
        $name = $result->first_name . ' ' . $result->last_name;

        $address = !empty($result->address_1) ? $result->address_1 : '';
        $address .= !empty($result->address_2) ? ', ' . $result->address_2 : '';
        $address .= !empty($result->city) ? ', ' . $result->city : '';
        $address .= !empty($result->state) ? ', ' . $result->state : '';
        $address .= !empty($result->postcode) ? ', ' . $result->postcode : '';

        fputcsv(
            $output,
            array(
                $result->order_number,
                $result->date,
                $result->type,
                $renewal,
                number_format($result->total + $result->tax, 2, '.', ' '),
                $name,
                $address,
                date('Y-m-d', strtotime($result->expiry))
            )
        );
    }
}
add_action('admin_post_print_report_membership_subscriptions.csv', 'print_report_csv_membership_subscriptions');