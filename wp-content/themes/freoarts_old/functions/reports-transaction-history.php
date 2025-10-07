<?php

/****************************************************
 *
 * REPORTING - TRANSACTION HISTORY
 *
 ****************************************************/

function get_admin_report_transaction_history()
{

    global $wpdb;

    $filter_dates = '';

    if (!empty($_GET['date_from'])) {
        $filter_dates .= " AND pm6.meta_value >= '" . $_GET['date_from'];

        $hours = !empty($_GET['time_from_hour']) ? str_pad($_GET['time_from_hour'], 2, '0', STR_PAD_LEFT) : '00';
        $minutes = !empty($_GET['time_from_minute']) ? str_pad($_GET['time_from_minute'], 2, '0', STR_PAD_LEFT) : '00';

        $filter_dates .= ' ' . $hours . ':' . $minutes . ":00'";
    }

    if (!empty($_GET['date_to'])) {
        $filter_dates .= " AND pm6.meta_value <= '" . $_GET['date_to'];

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
        p.post_date,
        p.post_type,
        p.post_parent,
        pm1.meta_value AS payment_method_title,
        pm2.meta_value AS transaction_id,
        pm3.meta_value AS customer_id,
        pm4.meta_value AS payment_method,
        pm5.meta_value AS via_admin,
        pm6.meta_value AS paid_date,
        u.display_name,
        i.order_item_name,
        im1.meta_value AS qty,
        im2.meta_value AS line_subtotal,
        im3.meta_value AS line_total,
        im4.meta_value AS line_subtotal_tax,
        im5.meta_value AS line_tax,
        im6.meta_value AS product_id

    FROM wp_posts AS p

    LEFT JOIN wp_postmeta AS pm1
    ON pm1.post_id = p.ID
    AND pm1.meta_key = '_payment_method_title'

    LEFT JOIN wp_postmeta AS pm2
    ON pm2.post_id = p.ID
    AND pm2.meta_key = '_transaction_id'

    LEFT JOIN wp_postmeta AS pm3
    ON pm3.post_id = p.ID
    AND pm3.meta_key = '_customer_user'

    LEFT JOIN wp_postmeta AS pm4
    ON pm4.post_id = p.ID
    AND pm4.meta_key = '_payment_method'

    LEFT JOIN wp_postmeta AS pm5
    ON pm5.post_id = p.ID
    AND pm5.meta_key = '_via_admin'

    LEFT JOIN wp_postmeta AS pm6
    ON pm6.post_id = p.ID
    AND pm6.meta_key = '_paid_date'

    LEFT JOIN wp_users AS u
    ON u.ID = pm3.meta_value

    INNER JOIN wp_woocommerce_order_items AS i
    ON i.order_id = p.ID
    AND i.order_item_type = 'line_item'

    INNER JOIN wp_woocommerce_order_itemmeta AS im1
    ON im1.order_item_id = i.order_item_id
    AND im1.meta_key = '_qty'

    INNER JOIN wp_woocommerce_order_itemmeta AS im2
    ON im2.order_item_id = i.order_item_id
    AND im2.meta_key = '_line_subtotal'

    INNER JOIN wp_woocommerce_order_itemmeta AS im3
    ON im3.order_item_id = i.order_item_id
    AND im3.meta_key = '_line_total'

    INNER JOIN wp_woocommerce_order_itemmeta AS im4
    ON im4.order_item_id = i.order_item_id
    AND im4.meta_key = '_line_subtotal_tax'

    INNER JOIN wp_woocommerce_order_itemmeta AS im5
    ON im5.order_item_id = i.order_item_id
    AND im5.meta_key = '_line_tax'

    INNER JOIN wp_woocommerce_order_itemmeta AS im6
    ON im6.order_item_id = i.order_item_id
    AND im6.meta_key = '_product_id'

    WHERE (p.post_type = 'shop_order'{$include_refunds})

    {$filter_dates}

    AND (p.post_status = 'wc-completed' OR p.post_status = 'wc-refunded')

    ORDER BY p.post_date DESC;
    ";

    $results = $wpdb->get_results($sql, OBJECT);

    return $results;
}

/**
 * Helper - Add to totals array
 *
 */
function add_split_totals($totals, $result)
{

    $totals['qty']           += $result->qty;
    $totals['sale_total']    += round($result->line_total, 2);
    $totals['gst_total']     += round($result->line_tax, 2);
    $totals['line_total']    += (round($result->line_tax, 2) + round($result->line_total, 2));

    return $totals;
}

/**
 * Display the admin Transaction History report
 *
 */
function view_admin_report_transaction_history()
{

    $date_from = !empty($_GET['date_from']) ? $_GET['date_from'] : '';
    $time_from_hour = !empty($_GET['time_from_hour']) ? $_GET['time_from_hour'] : '';
    $time_from_minute = !empty($_GET['time_from_minute']) ? $_GET['time_from_minute'] : '';

    $date_to = !empty($_GET['date_to']) ? $_GET['date_to'] : '';
    $time_to_hour = !empty($_GET['time_to_hour']) ? $_GET['time_to_hour'] : '';
    $time_to_minute = !empty($_GET['time_to_minute']) ? $_GET['time_to_minute'] : '';

    $include_refunds = !empty($_GET['include_refunds']) ? true: false;

    $results = get_admin_report_transaction_history();
    ?>
    <div class="wrap">

        <h1>Transaction History Report</h1>

        <form id="posts-filter" action="" method="get">

            <input type="hidden" name="page" value="reports-transaction-history" />

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
                        $url = admin_url('admin-post.php?action=print_report_transaction_history.csv');

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
                                    <th class="acf-th acf-th-text">Order ID</th>
                                    <th class="acf-th acf-th-text">Created At</th>
                                    <th class="acf-th acf-th-text">Paid At</th>
                                    <th class="acf-th acf-th-text">Type</th>
                                    <th class="acf-th acf-th-text">Payment Method</th>
                                    <th class="acf-th acf-th-text">Transaction ID</th>
                                    <th class="acf-th acf-th-text">Customer</th>
                                    <th class="acf-th acf-th-text">Line Item</th>
                                    <th class="acf-th acf-th-text">Qty</th>
                                    <?php /* <th class="acf-th acf-th-text">Subtotal GST</th> */ ?>
                                    <?php /* <th class="acf-th acf-th-text">Line Subtotal</th> */ ?>
                                    <th class="acf-th acf-th-text">Sale (ex GST)</th>
                                    <th class="acf-th acf-th-text">GST</th>
                                    <th class="acf-th acf-th-text">Total (inc GST)</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $i              = 0;
                            $qty_total      = 0;
                            $sale_total     = 0;
                            $gst_total      = 0;
                            $line_total     = 0;

                            $total_template = array(
                                'qty'           => 0,
                                'sale_total'    => 0,
                                'gst_total'     => 0,
                                'line_total'    => 0
                            );
                            $membership_totals      = $total_template;
                            $enrolment_totals       = $total_template;
                            $printaward_totals      = $total_template;
                            $payment_method_totals  = array();
                            ?>
                            <?php foreach ($results as $result) { ?>
                                <?php
                                $i++;

                                $is_credit_card = ($result->payment_method == 'eway') ? true: false;
                                $is_other_payment_method = ($result->payment_method == 'other_payment_method') ? true: false;

                                if ($is_credit_card) {

                                    $qty_total += $result->qty;

                                    $sale_total += round($result->line_total, 2);
                                    $gst_total += round($result->line_tax, 2);
                                    $line_total += (round($result->line_tax, 2) + round($result->line_total, 2));

                                    if (
                                        $result->order_item_name == 'Concession Membership' ||
                                        $result->order_item_name == 'Individual Membership'
                                    ) {
                                        $membership_totals = add_split_totals($membership_totals, $result);
                                    }
                                    elseif (
                                        strpos( $result->order_item_name, 'Print Award' ) !== false
                                    ) {
                                        $printaward_totals = add_split_totals($printaward_totals, $result);
                                    }
                                    else {
                                        $enrolment_totals = add_split_totals($enrolment_totals, $result);
                                    }
                                }

                                ?>
                                <tr class="acf-row">
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
                                        <?php echo $result->paid_date ?: '' ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo ($result->post_type == 'shop_order_refund') ? 'Refund': 'Sale'; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php

                                        $payment_title = $result->payment_method_title;

                                        if ($is_credit_card) {

                                            if ($result->via_admin) {
                                                $payment_title .= ' (via Reception)';
                                            }
                                            else {
                                                $payment_title .= ' (via Front-end)';
                                            }

                                            if (!isset($payment_method_totals[$payment_title])) {

                                                $payment_method_totals[$payment_title] = $total_template;
                                            }

                                            $payment_method_totals[$payment_title] = add_split_totals($payment_method_totals[$payment_title], $result);
                                        }

                                        echo $payment_title;
                                        ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $result->transaction_id; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php if (!empty($result->customer_id)) { ?>
                                            <a href="<?php echo get_edit_user_link($result->customer_id); ?>" title="View account">
                                                <?php echo $result->display_name . ' (ID: ' . $result->customer_id . ')'; ?>
                                            </a>
                                        <?php } ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php
                                        echo $result->order_item_name;
                                        $term = get_field('term', $result->product_id);
                                        if (
                                                isset($term['term']) && !empty($term['term']) &&
                                                isset($term['year']) && !empty($term['year'])
                                        ) {
                                            echo " - Term {$term['term']}, {$term['year']}";
                                        }
                                        ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $result->qty; ?>
                                    </td>
                                    <?php /* <td class="acf-field acf-field-text">
                                        <?php echo $result->line_subtotal_tax; ?>
                                    </td> */ ?>
                                    <?php /* <td class="acf-field acf-field-text">
                                        <?php echo $result->line_subtotal; ?>
                                    </td> */ ?>
                                    <td class="acf-field acf-field-text align-right">
                                        <?php echo $is_other_payment_method ? '0.00': number_format($result->line_total, 2, '.', ' '); ?>
                                    </td>
                                    <td class="acf-field acf-field-text align-right">
                                        <?php echo $is_other_payment_method ? '0.00': number_format($result->line_tax, 2, '.', ' '); ?>
                                    </td>
                                    <td class="acf-field acf-field-text align-right">
                                        <?php echo $is_other_payment_method ? '0.00': number_format($result->line_tax + $result->line_total, 2, '.', ' '); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php $first = true; ?>
                            <?php foreach ($payment_method_totals as $key => $totals) { ?>
                                <tr class="acf-row<?php echo $first ? ' seperator': ''; ?>">
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
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        Payment Method - <?php echo $key; ?>
                                    </td>
                                    <td class="acf-field acf-field-text">
                                        <?php echo $totals['qty']; ?>
                                    </td>
                                    <td class="acf-field acf-field-text align-right">
                                        <?php echo number_format($totals['sale_total'], 2, '.', ' '); ?>
                                    </td>
                                    <td class="acf-field acf-field-text align-right">
                                        <?php echo number_format($totals['gst_total'], 2, '.', ' '); ?>
                                    </td>
                                    <td class="acf-field acf-field-text align-right">
                                        <?php echo number_format($totals['line_total'], 2, '.', ' '); ?>
                                    </td>
                                </tr>
                                <?php $first = false; ?>
                            <?php } ?>
                            <tr class="acf-row seperator">
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
                                </td>
                                <td class="acf-field acf-field-text">
                                    Membership Totals
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php echo $membership_totals['qty']; ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($membership_totals['sale_total'], 2, '.', ' '); ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($membership_totals['gst_total'], 2, '.', ' '); ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($membership_totals['line_total'], 2, '.', ' '); ?>
                                </td>
                            </tr>
                            <tr class="acf-row">
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
                                </td>
                                <td class="acf-field acf-field-text">
                                    Enrolment Totals
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php echo $enrolment_totals['qty']; ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($enrolment_totals['sale_total'], 2, '.', ' '); ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($enrolment_totals['gst_total'], 2, '.', ' '); ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($enrolment_totals['line_total'], 2, '.', ' '); ?>
                                </td>
                            </tr>
                            <tr class="acf-row">
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
                                </td>
                                <td class="acf-field acf-field-text">
                                    Print Award Totals
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php echo $printaward_totals['qty']; ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($printaward_totals['sale_total'], 2, '.', ' '); ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($printaward_totals['gst_total'], 2, '.', ' '); ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($printaward_totals['line_total'], 2, '.', ' '); ?>
                                </td>
                            </tr>
                            <tr class="acf-row seperator">
                                <td class="acf-field acf-field-text">
                                    <?php echo $i; ?> Orders
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
                                    GRAND TOTALS
                                </td>
                                <td class="acf-field acf-field-text">
                                    <?php echo $qty_total; ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($sale_total, 2, '.', ' '); ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($gst_total, 2, '.', ' '); ?>
                                </td>
                                <td class="acf-field acf-field-text align-right">
                                    <?php echo number_format($line_total, 2, '.', ' '); ?>
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
function print_report_csv_transaction_history()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $results = get_admin_report_transaction_history();

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename=transaction-history.csv');
    header('Pragma: no-cache');

    $output = fopen('php://output', 'w');

    fputcsv(
        $output,
        array(
            'Order ID',
            'Created At',
            'Paid At',
            'Type',
            'Payment Method',
            'Transaction ID',
            'Customer',
            'Line Item',
            'Qty',
            'GST',
            'Sale (ex GST)',
            'Total (inc GST)'
        )
    );

    $i              = 0;
    $qty_total      = 0;
    $sale_total     = 0;
    $gst_total      = 0;
    $line_total     = 0;

    $total_template = array(
        'qty'           => 0,
        'sale_total'    => 0,
        'gst_total'     => 0,
        'line_total'    => 0
    );
    $membership_totals      = $total_template;
    $enrolment_totals       = $total_template;
    $printaward_totals      = $total_template;
    $payment_method_totals  = array();

    foreach ($results as $result) {

        $i++;


        $is_credit_card = ($result->payment_method == 'eway') ? true: false;
        $is_other_payment_method = ($result->payment_method == 'other_payment_method') ? true: false;

        $qty_total += $result->qty;

        if ($is_credit_card) {

            $sale_total += $result->line_total;
            $gst_total += $result->line_tax;
            $line_total += ($result->line_tax + $result->line_total);
        }

        $current_sale_total = ($is_other_payment_method) ? 0: $result->line_total;
        $current_gst_total = ($is_other_payment_method) ? 0: $result->line_tax;
        $current_line_total = ($is_other_payment_method) ? 0: ($result->line_tax + $result->line_total);

        if (
            $result->order_item_name == 'Concession Membership' ||
            $result->order_item_name == 'Individual Membership'
        ) {
            $membership_totals = add_split_totals($membership_totals, $result);
        }
        elseif (
            strpos( $result->order_item_name, 'Print Award' ) !== false
        ) {
            $printaward_totals = add_split_totals($printaward_totals, $result);
        }
        else {
            $enrolment_totals = add_split_totals($enrolment_totals, $result);
        }

        $payment_title = $result->payment_method_title;

        if ($is_credit_card) {

            if ($result->via_admin) {
                $payment_title .= ' (via Reception)';
            }
            else {
                $payment_title .= ' (via Front-end)';
            }

            if (!isset($payment_method_totals[$payment_title])) {

                $payment_method_totals[$payment_title] = $total_template;
            }

            $payment_method_totals[$payment_title] = add_split_totals($payment_method_totals[$payment_title], $result);
        }

        $type = ($result->post_type == 'shop_order_refund') ? 'Refund': 'Sale';

        $customer = '';
        if (!empty($result->customer_id)) {
            $customer = $result->display_name . ' (ID: ' . $result->customer_id . ')';
        }

        fputcsv(
            $output,
            array(
                $result->ID,
                $result->post_date,
                $result->paid_date,
                $type,
                $payment_title,
                $result->transaction_id,
                $customer,
                $result->order_item_name,
                $result->qty,
                number_format($current_sale_total, 2, '.', ' '),
                number_format($current_gst_total, 2, '.', ' '),
                number_format($current_line_total, 2, '.', ' ')
            )
        );
    }

    foreach ($payment_method_totals as $key => $totals) {

        fputcsv(
            $output,
            array(
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Payment Method - ' . $key,
                $totals['qty'],
                number_format($totals['sale_total'], 2, '.', ' '),
                number_format($totals['gst_total'], 2, '.', ' '),
                number_format($totals['line_total'], 2, '.', ' ')
            )
        );
    }

    fputcsv(
        $output,
        array(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Membership Totals',
            $membership_totals['qty'],
            number_format($membership_totals['sale_total'], 2, '.', ' '),
            number_format($membership_totals['gst_total'], 2, '.', ' '),
            number_format($membership_totals['line_total'], 2, '.', ' ')
        )
    );

    fputcsv(
        $output,
        array(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Enrolment Totals',
            $enrolment_totals['qty'],
            number_format($enrolment_totals['sale_total'], 2, '.', ' '),
            number_format($enrolment_totals['gst_total'], 2, '.', ' '),
            number_format($enrolment_totals['line_total'], 2, '.', ' ')
        )
    );

    fputcsv(
        $output,
        array(
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'Print Award Totals',
            $printaward_totals['qty'],
            number_format($printaward_totals['sale_total'], 2, '.', ' '),
            number_format($printaward_totals['gst_total'], 2, '.', ' '),
            number_format($printaward_totals['line_total'], 2, '.', ' ')
        )
    );

    fputcsv(
        $output,
        array(
            $i . ' Orders',
            '',
            '',
            '',
            '',
            '',
            '',
            'GRAND TOTALS',
            $qty_total,
            number_format($sale_total, 2, '.', ' '),
            number_format($gst_total, 2, '.', ' '),
            number_format($line_total, 2, '.', ' ')
        )
    );
}
add_action('admin_post_print_report_transaction_history.csv', 'print_report_csv_transaction_history');