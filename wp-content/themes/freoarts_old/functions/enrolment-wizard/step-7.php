<?php
/******************************
 * Review & Pay
 ******************************/

global $submission,
       $errors,
       $wpdb;

enrolment_wizard_get_step_links(7, ifne($submission, 'id'));

?>
<?php if (ifne($submission, 'status') == 'complete') { ?>

    <p>This order has already been processed. Please create a new enrolment wizard submission.</p>

<?php } else { ?>
    <div class="postbox-container active">
        <div class="postbox acf-postbox">

            <h2 class="hndle">Review</h2>

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
        </div><!-- .postbox.acf-postbox -->

        <div class="postbox acf-postbox">

            <h2 class="hndle">Payment</h2>

            <div class="inside acf-fields -left">

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Payment Details</label>
                    </div>
                    <div class="acf-input">
                        <?php if (ifne($submission, 'is_cc_payment')) { ?>
                            <p>Credit Card Payment via eWAY</p>
<!--                            <p><em style="color: red;">Proceeding with this order will generate an order with status set to "Pending Payment".<br>The next page will display a credit card form to complete the payment, and if successful it will update the order status to "Complete."</em></p>-->
                        <?php } else { ?>
                            <p>
                                <?php echo ifne($submission, 'other_payment_methods'); ?><br />
                                <?php echo ifne($submission, 'payment_notes'); ?>
                            </p>
                            <p><em style="color: red;">Proceeding with this order will generate the order, create the enrolees and reduce the course stock quantity.<br>
                                    You will not be able to edit this enrolment wizard beyond this point. All edits must be made manually.</em></p>
                        <?php } ?>
                    </div>
                </div>
                <?php if(ifne($submission, 'is_cc_payment')) : ?>
                    <?php
                    $encryptKey = 'rkh0rlimOHqJ9ewe/y9TeFGbYWjN1OJN1B9Z/JwW2p3YFX9l9nPReGQff7Z/Qasy1eF6zqftcIsE1cVAXfeoeRVdGhqWZit9LuGI+XAJ/Md0yFHaEK/l6IOw0wSG7jRp66od9YQYCCEdGCyQVzswuc3jPVhcFzo+iCdTcO3c1ZPpNkY3hvMNecUwUBxnA/C8jgheoOSsvFx1TmYmMwA2ThuQiBo2xPRsbJoRwHSuZAWFOQWV0MRL5OjbDKmU+YLkh0t9aIUD5D71IFx8qRKxyVNOUJUwdRaQy6NakTPsx781/JT7vcXL19r7NWtY6wSs3K8l9Vnpp5e29+z9kErhvQ==';

                    if ($_POST['submit_cc_form'] ?? '' === 2) {

                        //Create the order, we will set status to Complete or Failed depending on credit card processing result
                        $order = enrolment_wizard_create_order($submission);

                        $apiKey = 'F9802AAeIVlEZzx9Q4cdOSOIdMm7vIBZnyQhvgw22r2ItBYM6apsm3qDh4gR5kUoVh743A';
                        $apiPassword = '!#LeTiTbE4#!';
                        $apiEndpoint = \Eway\Rapid\Client::MODE_PRODUCTION;
//                      $apiKey = 'F9802Cko2CjU3ON/J6qW48kHgxOT2Mp9FUvg4J7Uo2EnupsVQubJ3mUk/MJPvte2J+Frax';
//                      $apiPassword = 'OjY3oRiq';
//                      $apiEndpoint = \Eway\Rapid\Client::MODE_SANDBOX;

                        $client      = \Eway\Rapid::createClient($apiKey, $apiPassword, $apiEndpoint);

                        $transaction = [
                            'Customer'        => [
                                'Reference' => $order->get_id(),
                                'FirstName' => $order->get_billing_first_name(),
                                'LastName' => $order->get_billing_last_name(),
                                'Street1' => $order->get_billing_address_1(),
                                'Street2' => $order->get_billing_address_2(),
                                'City' => $order->get_billing_city(),
                                'State' => $order->get_billing_state(),
                                'PostalCode' => $order->get_billing_postcode(),
                                'Country' => 'au',
                                'Email' => $order->get_billing_email(),
                                'CardDetails' => [
                                    'Name'        => $_POST['EWAY_CARDNAME'] ?? '', // John Smith
                                    'Number'      => $_POST['EWAY_CARDNUMBER'] ?? '', // 4444333322221111
                                    'ExpiryMonth' => $_POST['EWAY_CARDEXPIRYMONTH'] ?? '',
                                    'ExpiryYear'  => $_POST['EWAY_CARDEXPIRYYEAR'] ?? '',
                                    'CVN'         => $_POST['EWAY_CARDCVN'] ?? '', // 123
                                ]
                            ],
                            'Payment'         => [
                                'TotalAmount' => (int) $cart_review['total'],
                                'InvoiceDescription' => '(Online Payment)',
                                'InvoiceNumber' => $order->get_id(),
                                'InvoiceReference' => $order->get_id(),
                                'CurrencyCode' => 'AUD',
                            ],
                            'TransactionType' => \Eway\Rapid\Enum\TransactionType::PURCHASE,
                        ];

                        $response = $client->createTransaction(\Eway\Rapid\Enum\ApiMethod::DIRECT, $transaction);

                        theme_log(
                            'enrolment-wizard',
                            'Enrolment Wizard eWay Transaction',
                            'Submission ID: '.$submission['id'],
                            'Created Order ID: '.$order->get_id(),
                            'Transaction: ',
                            $transaction,
                            'Transaction ID: '.$response->TransactionID,
                            'Errors: '.$response->Errors,
                            'Response code: '.$response->ResponseCode
                        );

                        if ($response->TransactionStatus) :

                            $submission = enrolment_wizard_save_submission(
                                $submission['id'],
                                array(
                                    'order_id' => $order->get_id(),
                                    'status' => 'complete',
                                )
                            );

                            $enrolments = enrolment_wizard_create_enrolments($submission);

                            theme_log(
                                'enrolment-wizard',
                                'Enrolment Wizard eWay Transaction Success',
                                'Submission ID: '.$submission['id'],
                                'Transaction ID: '.$response->TransactionID,
                                'Order ID: '.$submission['order_id'],
                                'Enrolment: ',
                                $enrolments
                            );

                            update_post_meta($order->get_id(), '_payment_method', 'eway');
                            update_post_meta($order->get_id(), '_payment_method_title', 'Credit Card');
                            update_post_meta($order->get_id(), '_via_admin', true);

                            // Mark as on-hold (we're awaiting the payment)
                            $order->update_status('completed', 'eWAY API payment completed.');
                            // Reduce stock levels
                            $order->reduce_order_stock();

                            // Payment has been successful
                            $order->add_order_note('eWAY API payment completed - Transaction ID: ' . $response->TransactionID);

                            // Mark order as Paid
                            $order->payment_complete();

                            // Send invoice
                            $email = new WC_Email_Customer_Invoice();
                            $email->trigger($order->get_id());
                        ?>
                            <div class="acf-field acf-field-text success">
                                <div class="acf-label">
                                    <label for="EWAY_CARDNAME">Payment Success</label>
                                </div>
                                <div class="acf-input">
                                    <div class="acf-input-wrap">
                                        <p style="color: green;">Payment successful!</p>
                                        <p>eWay transaction ID: <?= $response->TransactionID ?></p>
                                        <p><a href="<?= site_url() ?>/wp-admin/post.php?post=<?= $submission['order_id'] ?>&action=edit">
                                            Order #: <?= $submission['order_id'] ?>
                                        </a></p>
                                    </div>
                                </div>
                            </div>
                        <?php else :

                            $order->update_status( 'failed', 'eWAY API payment failed - '.$response->Errors);

                            echo '<div class="acf-field acf-field-text errors">
                            <div class="acf-label">
                                <label for="EWAY_CARDNAME">Payment Errors</label>
                            </div>
                            <div class="acf-input">
                                <div class="acf-input-wrap">';
                            $errors = explode(',', $response->Errors);
                            foreach($errors as $error) :
                                switch($error) {
                                    case "V6021" :
                                        echo '<p class="error" style="color: red;">Error: Missing cardholder name</p>';
                                        break;
                                    case "V6023" :
                                        echo '<p class="error" style="color: red;">Error: Missing CVN security code</p>';
                                        break;
                                    case "V6101" :
                                        echo '<p class="error" style="color: red;">Error: Invalid expiry month</p>';
                                        break;
                                    case "V6102" :
                                        echo '<p class="error" style="color: red;">Error: Invalid expiry year</p>';
                                        break;
                                    case "S9993" :
                                        echo '<p class="error" style="color: red;">Error: Authentication error</p>';
                                        break;
                                }
                            endforeach;
                            if ($response->ResponseCode) {
                                echo '<p class="error" style="color: red;">Response Code: <a href="https://go.eway.io/s/article/Bank-Response-Codes-Responses-00-to-38" target="_blank">' . $response->ResponseCode . '</a></p>';
                            }
                            echo '</div></div></div>';
                        endif;
                    }

                    /*
                    V6111 = Unauthorised API Access, Account Not PCI Certified
                    V6010 = Invalid TransactionType, account not certified for eCome only MOTO or Recurring available
                    */

                    ?>
                    <form method="post"
                          action="?page=enrolment-wizard-main&submission_id=<?= $submission['id'] ?>&step=7"
                          class="acf-fields -left" id="eway_credit_card_form"
                          data-eway-encrypt-key="<?= $encryptKey; ?>">

<!--                        <input type="hidden" name="EWAY_ACCESSCODE" value="--><?php //echo $access_code; ?><!--">-->
                        <input type="hidden" name="submit_cc_form" value="2">

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
                                    <input type="text" data-eway-encrypt-name="EWAY_CARDNUMBER" value="" autocomplete="off">
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
                                            <option value="<?= $j < 10 ? '0' : ''?><?= $j; ?>"><?= $j; ?></option>
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
                                    <input type="text" data-eway-encrypt-name="EWAY_CARDCVN" value="" autocomplete="off">
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
<!--                                            Please note, upon success, this form will redirect you to the eWAY payment completion page.<br>-->
<!--                                            If there is a payment issue you will be redirected to the previous page.-->
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
                    <script src="https://secure.ewaypayments.com/scripts/eCrypt.min.js"></script>
                <?php else : ?>
                    <div class="acf-field acf-field-text">
                        <div class="acf-label">
                        </div>
                        <div class="acf-input">
                            <div class="acf-input-wrap">
                                <form class="ajax-form postbox-container active" action="#" method="post">
                                    <input type="hidden" name="action" value="enrolment-wizard-parse-step">
                                    <input type="hidden" name="step" value="7">
                                    <input type="hidden" name="submission_id" value="<?= ifne($submission, 'id'); ?>">
                                    <input class="button button-primary" type="submit" name="proceed" value="Proceed" style="
                                        background: red;
                                        border-color: red #A90000 #A90000;
                                        box-shadow: 0 1px 0 #A90000;
                                        color: #fff;
                                        text-decoration: none;
                                        text-shadow: 0 -1px 1px #A90000, 1px 0 1px #A90000, 0 1px 1px #A90000, -1px 0 1px #A90000;">
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- .postbox.acf-postbox -->
    </div>
<?php } ?>