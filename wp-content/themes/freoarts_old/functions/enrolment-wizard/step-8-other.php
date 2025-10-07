<?php
/******************************
 * Review
 ******************************/

global $submission,
       $errors,
       $wpdb;
?>
<?php if (ifne($submission, 'status') == 'complete') { ?>

    <p>This order has already been processed. Please create a new enrolment wizard submission.</p>

<?php } else { ?>

    <?php

    enrolment_wizard_get_step_links(7, ifne($submission, 'id'));

    $order = enrolment_wizard_create_order($submission);

    $submission = enrolment_wizard_save_submission(ifne($submission, 'id'), array(
        'order_id' => $order->get_id()
    ));

    $enrolments = enrolment_wizard_create_enrolments($submission);

    $success_url = enrolment_wizard_process_other_payment($submission, $order);

    $submission = enrolment_wizard_save_submission(ifne($submission, 'id'), array(
        'status' => 'complete'
    ));
    ?>
    <div class="postbox-container active">
        <div class="postbox acf-postbox">

            <div class="mask">
            </div>

            <h2 class="hndle">Order Complete</h2>

            <div class="inside acf-fields -left">

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Order</label>
                    </div>
                    <div class="acf-input">
                        <a target="_blank" href="<?php echo get_edit_post_link(ifne($submission, 'order_id')); ?>" title="">Order #<?php echo ifne($submission, 'order_id'); ?></a><br>
                        <a target="_blank" href="<?php echo get_edit_user_link(ifne($submission, 'user_id')); ?>" title="">User: <?php echo ifne($submission, 'customer_email'); ?></a>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Customer Details</label>
                    </div>
                    <div class="acf-input">
                        <?php enrolment_wizard_get_customer_details($submission); ?>
                        <a href="<?php echo get_admin_url(null, 'admin.php?page=enrolment-wizard-main&submission_id=' . ifne($submission, 'id') . '&step=2'); ?>" title="">Edit Details</a>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Order Details</label>
                    </div>
                    <div class="acf-input">
                        <?php
                        $cart_review = enrolment_wizard_get_cart_review($submission);
                        echo $cart_review['html'];
                        ?>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Optional</label>
                    </div>
                    <div class="acf-input">
                        <?php if ($success_url) { ?>
                            <a target="_blank" href="<?php echo $success_url; ?>">View 'Order Received' Page</a>
                        <?php } else { ?>
                            <span style="color: red;">Payment Incomplete</span>
                        <?php } ?>
                    </div>
                </div>

            </div>

        </div><!-- .postbox.acf-postbox -->
    </div><!-- .postbox-container -->

<?php } ?>