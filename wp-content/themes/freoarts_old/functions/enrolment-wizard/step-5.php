<?php
/******************************
 * Enrolments
 ******************************/

global $submission,
       $errors,
       $wpdb;

enrolment_wizard_get_step_links(5, ifne($submission, 'id'));
?>
<script type="text/javascript">
    var enrolment_defaults = {
        enrolment_first_name: '<?php echo ifne($submission, 'customer_first_name'); ?>',
        enrolment_last_name: '<?php echo ifne($submission, 'customer_last_name'); ?>',
        enrolment_phone: '<?php echo ifne($submission, 'customer_phone_number'); ?>',
        enrolment_email: '<?php echo ifne($submission, 'customer_email'); ?>',
        enrolment_special_requirements: '<?php echo ifne($submission, 'special_requirements'); ?>',
        enrolment_emergency_name: '<?php echo ifne($submission, 'emergency_contact_name'); ?>',
        enrolment_emergency_relationship: '<?php echo ifne($submission, 'emergency_contact_relationship'); ?>',
        enrolment_emergency_phone: '<?php echo ifne($submission, 'emergency_contact_phone'); ?>'
    };
</script>
<form class="ajax-form postbox-container active" action="#" method="post">
    <div class="postbox acf-postbox">

        <input type="hidden" name="action" value="enrolment-wizard-parse-step"/>
        <input type="hidden" name="step" value="5"/>
        <input type="hidden" name="submission_id" value="<?php echo ifne($submission, 'id'); ?>"/>

        <div class="mask">
        </div>

        <h2 class="hndle">Order</h2>

        <div class="inside acf-fields -left">

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

        <h2 class="hndle">Enrolments</h2>

        <div class="inside acf-fields -left">

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label>Use Customer Information</label>
                </div>
                <div class="acf-input">
                    <ul class="acf-checkbox-list">
                        <li>
                            <label for="use_user_info">
                                <a id="use_user_info" href="#" title="Auto-fill">Click here to auto-fill with information from selected customer</a>
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <?php
            $increment = 0;
            $sql = "SELECT * FROM wp_enrolment_wizard_enrolments WHERE submission_id = " . ifne($submission, 'id');
            $enrolments = $wpdb->get_results($sql, ARRAY_A);
            ?>
            <?php foreach ($enrolments as $enrolment) { ?>
                <?php $product = wc_get_product(ifne($enrolment, 'course_id')); ?>
                <div class="acf-field acf-field-text" style="border-top: 1px solid #bbb;">
                    <div class="acf-label">
                        <label>COURSE</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <strong><?php echo ifne($enrolment, 'course_name'); ?></strong>
                        </div>
                    </div>
                </div>


                <div class="acf-field acfi-field-text">
                    <div class="acf-label">
                        <label>Preferred Pronoun</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_preferred_pronoun" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][preferred_pronoun]" value="<?php echo ifne($enrolment, 'preferred_pronoun'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'preferred_pronoun'); ?>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>First Name</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_first_name" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][first_name]" value="<?php echo ifne($enrolment, 'first_name'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'first_name'); ?>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Last Name</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_last_name" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][last_name]" value="<?php echo ifne($enrolment, 'last_name'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'last_name'); ?>
                        </div>
                    </div>
                </div>

                <?php $kids_course = get_field('is_kids_course', $product->get_id()); ?>
                <?php if ($kids_course) { ?>
                    <div class="acf-field acf-field-text">
                        <div class="acf-label">
                            <label>Age</label>
                        </div>
                        <div class="acf-input">
                            <div class="acf-input-wrap">
                                <select class="enrolment_age" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][age]">
                                    <option value="">Please Select</option>
                                    <?php
                                    for ($j = 18; $j >= 5; $j--) {
                                        $selected = '';
                                        if (ifne($enrolment, 'age') == $j) {
                                            $selected = ' selected';
                                        }
                                        echo '<option value="' . $j . '"' . $selected . '>' . $j . '</option>';
                                    }
                                    ?>
                                </select>
                                <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'age'); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Phone</label>
                        <p class="description">Numbers formatted as follows can receive SMS:<br>"+61 4xx xxx xxx" or "04xx xxx xxx"</p>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_phone" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][phone]" value="<?php echo ifne($enrolment, 'phone'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'phone'); ?>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Email</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_email" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][email]" value="<?php echo ifne($enrolment, 'email'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'email'); ?>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Special Requirements</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_special_requirements" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][special_requirements]" value="<?php echo ifne($enrolment, 'special_requirements'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'special_requirements'); ?>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Emergency Contact Name</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_emergency_name" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][emergency_contact_name]" value="<?php echo ifne($enrolment, 'emergency_contact_name'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'emergency_contact_name'); ?>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Emergency Contact Relationship</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_emergency_relationship" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][emergency_contact_relationship]" value="<?php echo ifne($enrolment, 'emergency_contact_relationship'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'emergency_contact_relationship'); ?>
                        </div>
                    </div>
                </div>

                <div class="acf-field acf-field-text">
                    <div class="acf-label">
                        <label>Emergency Contact Phone</label>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap">
                            <input type="text" class="enrolment_emergency_phone" name="enrolments[<?php echo ifne($enrolment, 'id'); ?>][emergency_contact_phone]" value="<?php echo ifne($enrolment, 'emergency_contact_phone'); ?>">
                            <?php show_ew_error(ifne($errors, ifne($enrolment, 'id')), 'emergency_contact_phone'); ?>
                        </div>
                    </div>
                </div>
            <?php } ?>

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