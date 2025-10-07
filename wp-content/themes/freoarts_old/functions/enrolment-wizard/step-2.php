<?php
/******************************
 * Customer
 ******************************/

global $submission,
       $errors;

enrolment_wizard_get_step_links(2, ifne($submission, 'id'));
?>
<form class="ajax-form postbox-container active" action="#" method="post">
    <div class="postbox acf-postbox">

        <input type="hidden" name="action" value="enrolment-wizard-parse-step"/>
        <input type="hidden" name="step" value="2"/>
        <input type="hidden" name="submission_id" value="<?php echo ifne($submission, 'id'); ?>"/>

        <div class="mask">
        </div>

        <h2 class="hndle">Customer Details</h2>

        <div class="inside acf-fields -left">

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="customer_first_name">First Name</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="customer_first_name" name="customer_first_name" value="<?php echo ifne($submission, 'customer_first_name'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'customer_first_name') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="customer_last_name">Last Name</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="customer_last_name" name="customer_last_name" value="<?php echo ifne($submission, 'customer_last_name'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'customer_last_name') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="customer_phone_number">Phone Number</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="customer_phone_number" name="customer_phone_number" value="<?php echo ifne($submission, 'customer_phone_number'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'customer_phone_number') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="customer_billing_address">Billing Address</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="customer_billing_address" name="customer_billing_address" value="<?php echo ifne($submission, 'customer_billing_address'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'customer_billing_address') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="customer_billing_suburb">Billing Suburb</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="customer_billing_suburb" name="customer_billing_suburb" value="<?php echo ifne($submission, 'customer_billing_suburb'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'customer_billing_suburb') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="customer_billing_state">Billing State</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <select id="customer_billing_state" name="customer_billing_state">
                            <?php
                            $states = array(
                                'WA' => 'Western Australia',
                                'ACT' => 'Australian Capital Territory',
                                'NSW' => 'New South Wales',
                                'NT' => 'Northern Territory',
                                'QLD' => 'Queensland',
                                'SA' => 'South Australia',
                                'TAS' => 'Tasmania',
                                'VIC' => 'Victoria'
                            );

                            foreach ($states as $key => $state) {
                                $selected = '';
                                if (ifne($submission, 'customer_billing_state') == $key) {
                                    $selected = ' selected';
                                }
                                echo '<option value="' . $key . '"' . $selected . '>' . $state . '</option>';
                            }
                            ?>
                        </select>
                        <?php show_ew_error($errors, 'customer_billing_state') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="customer_billing_postcode">Billing Postcode</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="customer_billing_postcode" name="customer_billing_postcode" value="<?php echo ifne($submission, 'customer_billing_postcode'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'customer_billing_postcode') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="special_requirements">Special Requirements</label>
                    <p class="description">Used as default for enrolee data.</p>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <textarea id="special_requirements" name="special_requirements" autocomplete="off"><?php echo ifne($submission, 'special_requirements'); ?></textarea>
                        <?php show_ew_error($errors, 'special_requirements') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="emergency_contact_name">Emergency Contact Name</label>
                    <p class="description">Used as default for enrolee data.</p>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="emergency_contact_name" name="emergency_contact_name" value="<?php echo ifne($submission, 'emergency_contact_name'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'emergency_contact_name') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="emergency_contact_relationship">Emergency Contact Relationship</label>
                    <p class="description">Used as default for enrolee data.</p>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="emergency_contact_relationship" name="emergency_contact_relationship" value="<?php echo ifne($submission, 'emergency_contact_relationship'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'emergency_contact_relationship') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label for="emergency_contact_phone">Emergency Contact Phone</label>
                    <p class="description">Used as default for enrolee data.</p>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="emergency_contact_phone" name="emergency_contact_phone" value="<?php echo ifne($submission, 'emergency_contact_phone'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'emergency_contact_phone') ?>
                    </div>
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