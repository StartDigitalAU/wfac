<?php

/****************************************************
 *
 * REPORTING - MEMBERSHIP DISCOUNTS
 *
 ****************************************************/

function get_admin_report_membership_discounts()
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

    $include_refunds = '';
    if (!empty($_GET['include_refunds'])) {
        $include_refunds = " OR p.post_type = 'shop_order_refund'";
    }

    $sql = "SELECT
        p.ID,
        p.post_parent,
        p.post_status,
        p.post_type,
        p.post_date,
        pm1.meta_value AS order_discount,
        woi.order_item_name,
        woim2.meta_value AS line_total,
        woim1.meta_value AS line_subtotal,
        woim4.meta_value AS line_tax,
        woim3.meta_value AS line_subtotal_tax
        
    FROM wp_posts AS p
    
    LEFT JOIN wp_postmeta AS pm1
    ON p.ID = pm1.post_id
    AND pm1.meta_key = '_cart_discount'
    
    LEFT JOIN wp_postmeta AS pm2
    ON p.ID = pm2.post_id
    AND pm2.meta_key = '_paid_date'
    
    LEFT JOIN wp_woocommerce_order_items AS woi
    ON p.ID = woi.order_id
    AND woi.order_item_type = 'line_item'
    
    LEFT JOIN wp_woocommerce_order_itemmeta AS woim1
    ON woi.order_item_id = woim1.order_item_id
    AND woim1.meta_key = '_line_subtotal'
    
    LEFT JOIN wp_woocommerce_order_itemmeta AS woim2
    ON woi.order_item_id = woim2.order_item_id
    AND woim2.meta_key = '_line_total'
    
    LEFT JOIN wp_woocommerce_order_itemmeta AS woim3
    ON woi.order_item_id = woim3.order_item_id
    AND woim3.meta_key = '_line_subtotal_tax'
    
    LEFT JOIN wp_woocommerce_order_itemmeta AS woim4
    ON woi.order_item_id = woim4.order_item_id
    AND woim4.meta_key = '_line_tax'
    
    WHERE (p.post_type = 'shop_order'{$include_refunds})
    
    {$filter_dates}
    
    AND (p.post_status = 'wc-completed' OR p.post_status = 'wc-refunded')
    
    AND (pm1.meta_value > 0)
    
    AND (woim2.meta_value < woim1.meta_value)

    ORDER BY p.post_date DESC;
    ";

    $results = $wpdb->get_results($sql, OBJECT);

    return $results;
}

/**
 * Display the admin Transaction History report
 *
 */
function view_admin_report_membership_discounts()
{

    $date_from = !empty($_GET['date_from']) ? $_GET['date_from'] : '';
    $time_from_hour = !empty($_GET['time_from_hour']) ? $_GET['time_from_hour'] : '';
    $time_from_minute = !empty($_GET['time_from_minute']) ? $_GET['time_from_minute'] : '';

    $date_to = !empty($_GET['date_to']) ? $_GET['date_to'] : '';
    $time_to_hour = !empty($_GET['time_to_hour']) ? $_GET['time_to_hour'] : '';
    $time_to_minute = !empty($_GET['time_to_minute']) ? $_GET['time_to_minute'] : '';

    $include_refunds = !empty($_GET['include_refunds']) ? true: false;

    $results = get_admin_report_membership_discounts();
    ?>
    <div class="wrap">

        <h1>Membership Discounts Report</h1>

        <form id="posts-filter" action="" method="get">

            <input type="hidden" name="page" value="reports-membership-discounts" />

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

                    <label><input type="checkbox" name="include_refunds" value="1"<?php if ($include_refunds) { echo ' checked="checked"'; } ?>/> Include refunds?</label>

                    <input class="button" type="submit" value="Filter">
                </div>

                <?php if (count($results)) { ?>
                    <div class="alignright actions">
                        <?php
                        $url = admin_url('admin-post.php?action=print_report_membership_discounts.csv');

                        $url .= isset($_GET['date_from']) ? '&date_from=' . $_GET['date_from']: '';
                        $url .= isset($_GET['time_from_hour']) ? '&time_from_hour=' . $_GET['time_from_hour']: '';
                        $url .= isset($_GET['time_from_minute']) ? '&time_from_minute=' . $_GET['time_from_minute']: '';
                        $url .= isset($_GET['date_to']) ? '&date_to=' . $_GET['date_to']: '';
                        $url .= isset($_GET['time_to_hour']) ? '&time_to_hour=' . $_GET['time_to_hour']: '';
                        $url .= isset($_GET['time_to_minute']) ? '&time_to_minute=' . $_GET['time_to_minute']: '';
                        $url .= isset($_GET['include_refunds']) ? '&include_refunds=' . $_GET['include_refunds']: '';
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
                                    <th class="acf-th acf-th-text">Line Item</th>
                                    <th class="acf-th acf-th-text align-right">Discount (excl. GST)</th>
                                    <th class="acf-th acf-th-text align-right">Discount (incl. GST)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 0;
                                $discount_total = 0;
                                $discount_total_tax = 0;
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
                                            <?php
                                            $id = $result->ID;
                                            if (!empty($result->post_parent)) {
                                                $id = $result->post_parent;
                                            }
                                            ?>
                                            <a href="<?php echo get_edit_post_link($id); ?>" title="View Order">
                                                <?php echo $id; ?>
                                            </a>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->post_date; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo ($result->post_type == 'shop_order_refund') ? 'Refund': 'Sale'; ?>
                                        </td>
                                        <td class="acf-field acf-field-text">
                                            <?php echo $result->order_item_name; ?>
                                        </td>
                                        <td class="acf-field acf-field-text align-right">
                                            <?php
                                            $discount = $result->line_subtotal - $result->line_total;
                                            $discount_total += $discount;
                                            echo number_format($discount, 2, '.', ' ');
                                            ?>
                                        </td>
                                        <td class="acf-field acf-field-text align-right">
                                            <?php
                                            $discount_tax = $result->line_subtotal_tax - $result->line_tax;
                                            $discount_total_tax += $discount;
                                            echo number_format($discount + $discount_tax, 2, '.', ' ');
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr class="acf-row seperator">
                                    <td class="acf-field acf-field-text">
                                        TOTALS
                                    </td>
                                    <td class="acf-field acf-field-text">
                                    </td>
                                    <td class="acf-field acf-field-text">
                                    </td>
                                    <td class="acf-field acf-field-text">
                                    </td>
                                    <td class="acf-field acf-field-text">
                                    </td>
                                    <td class="acf-field acf-field-text align-right">
                                        <?php echo number_format($discount_total, 2, '.', ' '); ?>
                                    </td>
                                    <td class="acf-field acf-field-text align-right">
                                        <?php echo number_format($discount_total + $discount_total_tax, 2, '.', ' '); ?>
                                    </td>
                                </tr>
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
function print_report_csv_membership_discounts()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $results = get_admin_report_membership_discounts();

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=transaction-history.csv');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    fputcsv(
        $output,
        array(
            'Order ID',
            'Date',
            'Type',
            'Line Item',
            'Discount (excl. GST)',
            'Discount (incl. GST)'
        )
    );

    $i = 0;
    $discount_total = 0;
    $discount_total_tax = 0;

    foreach ($results as $result) {

        $i++;

        $type = ($result->post_type == 'shop_order_refund') ? 'Refund': 'Sale';

        $discount = $result->line_subtotal - $result->line_total;
        $discount_total += $discount;

        $discount_tax = $result->line_subtotal_tax - $result->line_tax;
        $discount_total_tax += $discount;

        fputcsv(
            $output,
            array(
                $result->ID,
                $result->post_date,
                $type,
                $result->order_item_name,
                number_format($discount, 2, '.', ' '),
                number_format($discount + $discount_tax, 2, '.', ' ')
            )
        );
    }

    fputcsv(
        $output,
        array(
            $i . ' Items',
            '',
            '',
            'GRAND TOTALS',
            number_format($discount_total, 2, '.', ' '),
            number_format($discount_total + $discount_total_tax, 2, '.', ' ')
        )
    );
}
add_action('admin_post_print_report_membership_discounts.csv', 'print_report_csv_membership_discounts');