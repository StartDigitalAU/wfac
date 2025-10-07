<?php

/**
 * @var string $title
 */

$duration = $duration ?? false;
$date = $date ?? false;
$price = $price ?? false;
$is_adult_type = $is_adult_type ?? false;
$discount_price = $discount_price ?? false;
$additional_fields = $additional_fields ?? [];
$cta = $cta ?? [];
?>

<div class="item-meta-inner item-meta">
    <h2 class="title title--h3">Course information</h2>

    <div class="course-meta clearfix">
        <?php if (!empty($tutor)) : ?>
            <span class="instructor"><?= $tutor->post_title; ?></span>
        <?php endif; ?>
        <?php if (!empty($difficulties)) : ?>
            <div class="pills">
                <?php foreach ($difficulties as $difficulty) : ?>
                    <span class="pill"><?= $difficulty; ?></span>
                <?php endforeach;  ?>
            </div>
        <?php endif; ?>
    </div>

    <ul class="item-details">
        <?php if (!empty($duration)) { ?>
            <li>
                <h3 class="title title--h4">Duration</h3>
                <?php echo $duration; ?>
            </li>
        <?php } ?>
        <li>
            <h3 class="title title--h4">When</h3>
            <?php echo $date; ?>
        </li>
        <li>
            <h3 class="title title--h4">Course Cost</h3>
            <?php
            if ($is_adult_type) {

                echo '$' . $price . ' Non Members<br>';

                echo '$' . number_format($discount_price, 2, '.', '') . ' Members';
            } else {

                echo '$' . $price;
            }
            ?>
        </li>
        <?php if (!empty($additional_fields)) { ?>
            <?php foreach ($additional_fields as $additional_field) { ?>
                <li>
                    <h3 class="title title--h4"><?php echo $additional_field['label']; ?></h3>
                    <?php echo $additional_field['value']; ?>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>

    <?php
        render("parts/courses/cta.php", $cta);
    ?>

    <a class="item-terms" href="/terms/">Enrolment Terms &amp; Conditions</a>
</div>