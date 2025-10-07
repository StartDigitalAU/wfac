
<?php
/**
 * @var boolean $hide_note
 * @var boolean $is_released
 * @var boolean $disabled
 * @var string $product_id
 * @var string $product_title
 */
$hide_note = $hide_note ?? false;
$is_released = $is_released ?? false;
$disabled = $disabled ?? false;
$product_id = $product_id ?? false;
$product_title = $product_title ?? false;
?>
<?php
if ($is_released && !$disabled) {

    if ($_product->is_in_stock()) {

        if ($_product->get_stock_quantity() < 2 && !$hide_note) {

            echo '<p><strong>Note:</strong> There is only one space remaining in this course.</p>';
        }

        echo '<a href="' . $GLOBALS['site_url'] . '/cart/?add-to-cart=' . $product_id . '" class="btn btn--enrol" title="Enrol in ' . $product_title . '">
        Enrol in course
        <span class="icon"><svg aria-hidden="true" style="--icon-width: 1em;" class="stroke" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7 17L17 7M17 7H7M17 7V17" stroke="currentColor" stroke-width="2" stroke-linecap="square"/></svg></span>
        </a>';
    } else {

        echo '<span class="btn btn-outline-grey btn-enroll btn-block">Course Sold Out</span>';
    }
} else {

    echo '<span class="btn btn-black btn-enroll btn-block">Coming Soon</span>';
}
?>