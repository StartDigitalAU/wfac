<?php
/******************************
 * Course
 ******************************/

global $submission,
       $errors,
       $membership;

enrolment_wizard_get_step_links(3, ifne($submission, 'id'));

$user_info = get_userdata(ifne($submission, 'user_id'));

$membership = array(
    'role' => 'None',
    'expiry' => 'N/A'
);

// Is the customer a member already?
if (in_array('member_individual', $user_info->roles)) {
    $membership['role'] = 'Individual Membership';
    $membership['expiry'] = get_user_meta(ifne($submission, 'user_id'), 'expiry', true);
}
if (in_array('member_concession', $user_info->roles)) {
    $membership['role'] = 'Concession Membership';
    $membership['expiry'] = get_user_meta(ifne($submission, 'user_id'), 'expiry', true);
}
?>
<form class="ajax-form postbox-container active" action="#" method="post">
    <div class="postbox acf-postbox">

        <input type="hidden" name="action" value="enrolment-wizard-parse-step"/>
        <input type="hidden" name="step" value="3"/>
        <input type="hidden" name="submission_id" value="<?php echo ifne($submission, 'id'); ?>"/>

        <div class="mask">
        </div>

        <h2 class="hndle">Membership</h2>

        <div class="inside acf-fields -left">

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label>Current Membership Type</label>
                </div>
                <div class="acf-input">
                    <?php echo $membership['role']; ?> (Expires: <?php echo $membership['expiry']; ?>)
                </div>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                    <label>Purchase Membership</label>
                </div>
                <div class="acf-input">
                    <ul class="acf-checkbox-list">
                        <li>
                            <label>
                                <input type="radio" class="acf-checkbox-toggle" name="membership_product_id" value="" checked>
                                Not Required
                            </label>
                        </li>
                        <?php
                        $args = array(
                            'posts_per_page'    => -1,
                            'post_type'         => 'product',
                            'post_status'       => 'publish',
                            'orderby'           => 'title',
                            'tax_query'         => array(
                                array(
                                    'taxonomy'  => 'product_type',
                                    'field'     => 'slug',
                                    'terms'     => 'membership'
                                )
                            )
                        );

                        $memberships = get_posts($args);
                        ?>
                        <?php foreach ($memberships as $membership) { ?>
                            <?php
                            $checked = '';
                            if ($membership->ID == ifne($submission, 'membership_product_id')) {
                                $checked = ' checked';
                            }
                            ?>
                            <li>
                                <label>
                                    <input type="radio" class="acf-checkbox-toggle" name="membership_product_id" value="<?php echo $membership->ID; ?>"<?php echo $checked; ?>>
                                    <?php echo $membership->post_title; ?>
                                </label>
                            </li>
                        <?php } ?>
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