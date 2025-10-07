<?php
/******************************
 * Course
 ******************************/

global $submission,
       $errors,
       $wpdb;

enrolment_wizard_get_step_links(4, ifne($submission, 'id'));
?>
<form class="ajax-form postbox-container active" action="#" method="post">
    <div class="postbox acf-postbox">

        <input type="hidden" name="action" value="enrolment-wizard-parse-step"/>
        <input type="hidden" name="step" value="4"/>
        <input type="hidden" name="submission_id" value="<?php echo ifne($submission, 'id'); ?>"/>

        <div class="mask">
        </div>

        <h2 class="hndle">Courses</h2>

        <div class="inside acf-fields -left">

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
                        'terms'     => 'course'
                    )
                ),
                'meta_query' => array(
                    array(
                        'key' => '_stock_status',
                        'value' => 'instock'
                    ),
                    array(
                        'key' => '_backorders',
                        'value' => 'no'
                    ),
                )
            );

            $courses = get_posts($args);
            ?>
            <script>
                <?php
                $course_string = array();
                foreach ($courses as $course) {
                    $course_string[] = "{label: '" . addslashes($course->post_title) . "', value: " . $course->ID . "}";
                }
                ?>
                var ew_courses = [<?php echo implode(',', $course_string); ?>];
            </script>

            <?php
            $increment = 0;
            $sql = "SELECT * FROM wp_enrolment_wizard_enrolments WHERE submission_id = " . ifne($submission, 'id');
            $enrolments = $wpdb->get_results($sql, ARRAY_A);
            ?>
            <div class="product-list acf-fields -left" data-increment="<?php echo count($enrolments); ?>" style="border-top: 1px solid #eee;">
                <?php foreach ($enrolments as $enrolment) { ?>

                    <div class="acf-field acf-field-select" data-increment="<?php echo $increment; ?>">
                        <input type="hidden" name="course[<?php echo $increment; ?>][id]" value="<?php echo ifne($enrolment, 'id'); ?>"/>
                        <input type="hidden" name="course[<?php echo $increment; ?>][delete]" value=""/>
                        <div class="acf-label">
                            <label>Course #<span class="course-increment"><?php echo $increment + 1; ?></span> Enrolment</label>
                            <p class="description">Start typing course name locate existing course.</p>
                        </div>
                        <div class="acf-input">
                            <div class="acf-input-wrap">
                                <input type="text" class="course-name" data-increment="<?php echo $increment; ?>" name="course[<?php echo $increment; ?>][name]" value="<?php echo ifne($enrolment, 'course_name'); ?>">
                                <input type="hidden" class="course-id" data-increment="<?php echo $increment; ?>" name="course[<?php echo $increment; ?>][course_id]" value="<?php echo ifne($enrolment, 'course_id'); ?>">
                                <br><a class="remove-course" href="#" title="Remove course" data-increment="<?php echo $increment; ?>">Remove Course</a>
                            </div>
                        </div>
                    </div>

                    <?php $increment++; ?>
                <?php } ?>
            </div>

            <div class="acf-field acf-field-text">
                <div class="acf-label">
                </div>
                <div class="acf-input">
                    <div class="acf-input-wrap">
                        <input class="button" type="button" name="add_course" value="Add Course">
                        <input class="button button-primary" type="submit" name="proceed" value="Proceed">
                    </div>
                </div>
            </div>

        </div>

    </div><!-- .postbox.acf-postbox -->
</form><!-- .postbox-container -->