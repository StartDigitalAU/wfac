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

    enrolment_wizard_get_step_links('payment', ifne($submission, 'id'));

    $order = enrolment_wizard_create_order($submission);

    $submission = enrolment_wizard_save_submission(ifne($submission, 'id'), array(
        'order_id' => $order->get_id()
    ));

    $access_code = enrolment_wizard_process_cc_payment($submission, $order);

//    $enrolments = enrolment_wizard_create_enrolments($submission);
//
//    $submission = enrolment_wizard_save_submission(array(
//        'status' => 'complete'
//    ));

    $FA_WC_Gateway_EWAY = new FA_WC_Gateway_EWAY();
    ?>
    <div class="postbox-container active">
        <div class="postbox acf-postbox">

            <input type="hidden" name="step" value="8"/>
            <input type="hidden" name="submission_id" value="<?php echo ifne($submission, 'id'); ?>"/>

            <div class="mask">
            </div>

            <h2>Order Details</h2>

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

            <h2 class="hndle">eWay Payment</h2>
            <?php

            require('vendor/autoload.php');

            $apiKey = 'F9802Cko2CjU3ON/J6qW48kHgxOT2Mp9FUvg4J7Uo2EnupsVQubJ3mUk/MJPvte2J+Frax';
            $apiPassword = 'uMgfPF5r';
            $encryptKey = 'leVBUntJIjgaQyGG3Fdv2UL2G94Y6dL+9CdQMLef2RKBbhlu3e/7IB89SmiJPBiw9wWM/ZGVRIug7AURLvsGsmRlgjO4fwS4Od8Kv6gbrwzyb9hO0sZc+sjQ7yNLQdlJx52xuhynpaTmz0zsHvpQx8TDgzUrgv3YqUIAA4kuSkUdbdAphQ22XQ2V72HKITOVPJWrx8Aci1t3xgMXeVu+C6CIpCCp4VGPKrmkS4wWKH2AfanFQWNoW98RKu8X4Bo/TYRM0V7BPdnfeqolCmtHvB0DLAMBf1AkAzaOgEk4EdYXvJdftLD6for5fF4yqmoYR6MPtM5SWd9TAipjVp2AIQ==';

            if ($_POST['test'] ?? '' === 2) {
                $apiEndpoint = \Eway\Rapid\Client::MODE_SANDBOX;
                $client      = \Eway\Rapid::createClient($apiKey, $apiPassword, $apiEndpoint);

                $transaction = [
                    'Customer'        => [
                        'CardDetails' => [
                            'Name'        => $_POST['EWAY_CARDNAME'] ?? '', // John Smith
                            'Number'      => $_POST['EWAY_CARDNUMBER'] ?? '', // 4444333322221111
                            'ExpiryMonth' => $_POST['EWAY_CARDEXPIRYMONTH'] ?? '12',
                            'ExpiryYear'  => $_POST['EWAY_CARDEXPIRYYEAR'] ?? '25',
                            'CVN'         => $_POST['EWAY_CARDCVN'] ?? '', // 123
                        ]
                    ],
                    'Payment'         => [
                        'TotalAmount' => 1000,
                    ],
                    'TransactionType' => \Eway\Rapid\Enum\TransactionType::PURCHASE,
                ];

                $response = $client->createTransaction(\Eway\Rapid\Enum\ApiMethod::DIRECT, $transaction);

                if ($response->TransactionStatus) {
                    echo 'Payment successful! ID: ' . $response->TransactionID;
                } else {
                    echo $response->Errors;
                }
            }

            /*
            V6111 = Unauthorised API Access, Account Not PCI Certified
            V6010 = Invalid TransactionType, account not certified for eCome only MOTO or Recurring available
            */

            ?>
            <form method="post" class="inside acf-fields -left" action="<?php echo $gateway_url; ?>" id="eway_credit_card_form">

                <input type="hidden" name="EWAY_ACCESSCODE" value="<?php echo $access_code; ?>">

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label for="EWAY_CARDNAME">Card Name</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" name="EWAY_CARDNAME" value="" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label for="EWAY_CARDNUMBER">Card Number</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" name="EWAY_CARDNUMBER" value="" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label for="EWAY_CARDEXPIRYMONTH">Expiry Month</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <select name="EWAY_CARDEXPIRYMONTH">
                                <?php for ($j = 1; $j <= 12; $j++) { ?>
                                    <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label for="EWAY_CARDEXPIRYYEAR">Expiry Year</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <select name="EWAY_CARDEXPIRYYEAR">
                                <?php for ($j = date('Y'); $j <= (date('Y') + 10); $j++) { ?>
                                    <option value="<?php echo $j; ?>"><?php echo $j; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label for="EWAY_CARDCVN">Security Code (CVN)</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" name="EWAY_CARDCVN" value="" autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <p>
                                <em style="color: red;">
                                    If the payment is successful, enrolees will be created and the course stock quantities will be reduced.<br>
                                    Please note, upon success, this form will redirect you to the eWAY payment completion page.<br>
                                    If there is a payment issue you will be redirected to the previous page.
                                </em>
                            </p>
                            <input class="button button-primary" type="submit" name="pay_with_eway" value="Pay" style="
                                background: red;
                                border-color: red #A90000 #A90000;
                                box-shadow: 0 1px 0 #A90000;
                                color: #fff;
                                text-decoration: none;
                                text-shadow: 0 -1px 1px #A90000, 1px 0 1px #A90000, 0 1px 1px #A90000, -1px 0 1px #A90000;">
                        </div>
                    </div>
                </div>

            </form>

        </div><!-- .postbox.acf-postbox -->
    </div><!-- .postbox-container -->

<?php } ?>