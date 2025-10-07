<?php
$disabled = $GLOBALS['theme_options']['disable_course_enrolments'];
?>
<div class="sub-header-wrapper">
    <div class="sub-header container container--gutters">
        <div class="relative" style="position: relative;">
            <nav>
                <ul class="clearfix">
                    <li><a href="<?php echo $GLOBALS['site_url'] . '/learn/'; ?>" class="icon link-back" title="Go Back to back to Courses">Back to Courses</a></li>
                </ul>
            </nav>


            <div class="btn-enroll-wrap">
                <?php
                if (validate_release_date($fields) && !$disabled) {

                    if ($_product->is_in_stock()) {

                        echo '<a href="' . $GLOBALS['site_url'] . '/cart/?add-to-cart=' . get_the_ID() . '" class="btn btn-enroll" title="Enrol in' . get_the_title() . '">Enrol in this course</a>';
                    } else {

                        echo '<span class="btn btn-enroll">Sold Out</span>';
                    }
                } else {

                    echo '<span class="btn btn-enroll">Coming Soon</span>';
                }
                ?>
            </div>
        </div>
    </div>
</div>