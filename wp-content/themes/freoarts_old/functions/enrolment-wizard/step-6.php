<?php
/******************************
 * Payment Method
 ******************************/

global $submission,
       $errors,
       $wpdb;

enrolment_wizard_get_step_links(6, ifne($submission, 'id'));
?>
<form class="ajax-form postbox-container active" action="#" method="post">
    <div class="postbox acf-postbox">

        <input type="hidden" name="action" value="enrolment-wizard-parse-step"/>
        <input type="hidden" name="step" value="6"/>
        <input type="hidden" name="submission_id" value="<?php echo ifne($submission, 'id'); ?>"/>

        <div class="mask">
        </div>

        <h2 class="hndle">Order</h2>

        <div class="inside acf-fields -left">

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

        </div>

    </div>

    <div class="postbox acf-postbox">

        <h2 class="hndle">Payment Methods</h2>

        <div class="inside acf-fields -left">

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label>Credit Card</label>
                </div>
                <div class="acf-input">
                    <ul class="acf-checkbox-list">
                        <li>
                            <label for="is_cc_payment">
                                <input type="checkbox" class="acf-checkbox-toggle" id="is_cc_payment" name="is_cc_payment" value="true"<?php if (ifne($submission, 'is_cc_payment')) { ?> checked<?php } ?>>
                                Pay via Credit Card (eWAY Payment Gateway)
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="acf-field acf-field-text" data-field="other_payment_methods"<?php if (ifne($submission, 'is_cc_payment')) { ?> style="display: none;"<?php } ?>>
                <div class="acf-label">
                    <label>Other Payment Methods</label>
                </div>
                <div class="acf-input">
                    <?php
                    $options = array();
                    if (ifne($submission, 'other_payment_methods')) {
                        $options = explode(', ', ifne($submission, 'other_payment_methods'));
                    }
                    ?>
                    <ul class="acf-checkbox-list">
                        <li>
                            <label>
                                <input
                                    type="checkbox"
                                    class="acf-checkbox-toggle"
                                    name="other_payment_methods[]"
                                    value="Cash / Cheque"
                                    <?php if (in_array('Cash / Cheque', $options)) { echo ' checked'; } ?>>
                                Cash/Cheque
                            </label>
                        </li>
                        <li>
                            <label>
                                <input
                                    type="checkbox"
                                    class="acf-checkbox-toggle"
                                    name="other_payment_methods[]"
                                    value="EFTPOS"
                                    <?php if (in_array('EFTPOS', $options)) { echo ' checked'; } ?>>
                                Eftpos
                            </label>
                        </li>
                        <li>
                            <label>
                                <input
                                    type="checkbox"
                                    class="acf-checkbox-toggle"
                                    name="other_payment_methods[]"
                                    value="Gift Voucher"
                                    <?php if (in_array('Gift Voucher', $options)) { echo ' checked'; } ?>>
                                Gift Voucher
                            </label>
                        </li>
                        <?php if (current_user_can('administrator')) { ?>
                        <li>
                            <label>
                                <input
                                    type="checkbox"
                                    class="acf-checkbox-toggle"
                                    name="other_payment_methods[]"
                                    value="Admin"
                                    <?php if (in_array('Admin', $options)) { echo ' checked'; } ?>>
                                Admin
                            </label>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php show_ew_error($errors, 'other_payment_methods'); ?>
                </div>
            </div>

            <div class="acf-field acf-field-text" data-field="other_payment_methods"<?php if (ifne($submission, 'is_cc_payment')) { ?> style="display: none;"<?php } ?>>
                <div class="acf-label">
                    <label for="payment_notes">Notes</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <textarea id="payment_notes" name="payment_notes"><?php echo ifne($submission, 'payment_notes'); ?></textarea>
                        <?php show_ew_error($errors, 'payment_notes'); ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="has_read_terms_conditions">Terms &amp; Conditions</label>
                </div>
                <div class="acf-input">
                    <ul class="acf-checkbox-list">
                        <li>
                            <label for="has_read_terms_conditions">
                                <input type="checkbox" class="acf-checkbox-toggle" id="has_read_terms_conditions" name="has_read_terms_conditions" value="true"<?php if (ifne($submission, 'has_read_terms_conditions')) { ?> checked<?php } ?>>
                                The customer understands and accepts the terms &amp; conditions?
                            </label>
                            <?php show_ew_error($errors, 'has_read_terms_conditions'); ?>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input class="button button-primary" type="submit" name="proceed" value="Proceed">
                    </div>
                </div>
            </div>

        </div>

    </div><!-- .postbox.acf-postbox -->
</form><!-- .postbox-container -->