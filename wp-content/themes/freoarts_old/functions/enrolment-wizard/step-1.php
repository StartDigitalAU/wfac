<?php
/******************************
 * Customer
 ******************************/

global $submission,
       $errors,
       $wpdb;

enrolment_wizard_get_step_links(1, ifne($submission, 'id'));
?>
<form class="ajax-form postbox-container active" action="#" method="post" autocomplete="off">
    <div class="postbox acf-postbox">

        <input type="hidden" name="action" value="enrolment-wizard-parse-step"/>
        <input type="hidden" name="step" value="1"/>
        <input type="hidden" name="submission_id" value="<?php echo ifne($submission, 'id'); ?>"/>

        <div class="mask">
        </div>

        <h2 class="hndle">Customer</h2>

        <div class="inside acf-fields -left">

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label>New Customer</label>
                </div>
                <div class="acf-input">
                    <ul class="acf-checkbox-list">
                        <li>
                            <label for="is_customer_new">
                                <input type="hidden" name="is_customer_new" value="0">
                                <input type="checkbox" class="acf-checkbox-toggle" id="is_customer_new" name="is_customer_new" value="1"<?php if (ifne($errors, 'is_customer_new')) { echo ' checked'; } ?>>
                                Create New Customer
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="acf-field acf-field-text" data-field="new_customer" style="display: none;">
                <div class="acf-label">
                    <label for="customer_email">Email</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="text" id="customer_email" name="customer_email" value="<?php echo ifne($submission, 'customer_email'); ?>" autocomplete="off">
                        <?php show_ew_error($errors, 'customer_email') ?>
                    </div>
                </div>
            </div>

            <div class="acf-field acf-field-text" data-field="new_customer" style="display: none;">
                <div class="acf-label">
                    <label for="customer_password">Password</label>
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input type="password" id="customer_password" name="customer_password" value="" autocomplete="off">
                        <?php show_ew_error($errors, 'customer_password') ?>
                    </div>
                </div>
            </div>

            <?php

//            $sql = "SELECT
//                      u.ID,
//                      u.user_email
//                    FROM wp_users AS u
//                    ORDER BY u.user_email ASC
//                    ";
//
//            $users = $wpdb->get_results($sql, OBJECT);
            ?>
            <?php // if (!empty($users)) { ?>
                <div class="acf-field acf-field-select" data-field="existing_customer_email">
                    <div class="acf-label">
                        <label for="existing_customer_email">Existing Customer</label>
                        <p class="description">Start typing an email address to locate existing customer.</p>
                    </div>
                    <div class="acf-input">
                        <div class="acf-input-wrap ui-widget">
                            <script>
                                <?php
//                                $user_string = array();
//                                foreach ($users as $user) {
//                                    $user_string[] = "{label: '" . addslashes($user->user_email) . "', value: " . $user->ID . "}";
//                                }
//                                ?>
//                                var ew_users = [<?php //echo implode(',', $user_string); ?>//];
                            </script>
                            <?php
                            $existing_customer_email = '';
                            if (!empty(ifne($submission, 'user_id'))) {
                                $existing_customer_email = ifne($submission, 'customer_email');
                            }
                            ?>
                            <input type="text" id="existing_customer_email" name="existing_customer_email" autocomplete="nope" value="<?php echo $existing_customer_email; ?>">
                            <?php show_ew_error($errors, 'existing_customer_email') ?>
                        </div>
                    </div>
                </div>
            <?php // } ?>

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