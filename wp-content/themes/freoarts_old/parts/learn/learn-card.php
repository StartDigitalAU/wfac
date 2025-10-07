<?php
$learn_id = $learn->ID;
$image = get_field('card_image', $learn_id) ?: get_field('hero_image', $learn_id);
$type = get_field('type', $learn);
$start_date = date('D j M', strtotime(get_field('start_date', $learn_id)));
$end_date = date('D j M', strtotime(get_field('end_date', $learn_id)));
$pretitle = get_field('pretitle', $learn_id);
$title = get_the_title($learn_id);
$link = get_the_permalink($learn_id);
$product = wc_get_product($learn_id);
$cost = $product ? wc_price($product->get_price()) : '';
$type = 'Course';
$additional_fields = get_field('additional_fields', $learn_id);
// Find the studio value in additional_fields repeater
$location = '';
if (!empty($additional_fields) && is_array($additional_fields)) {
    foreach ($additional_fields as $field) {
        if (isset($field['label']) && strtolower($field['label']) === 'studio') {
            $location = $field['value'];
            break;
        }
    }
}

$product_categories = get_field('adults_category', $learn_id) ?: get_field('kids_category', $learn_id);

$product_categories = (! empty($product_categories)) ? $product_categories : 'No Category';

if ($product_categories && !is_wp_error($product_categories)) {
    $category = is_array($product_categories) && count($product_categories) > 1
        ? (get_term($product_categories[0])->name . ' / +' . count($product_categories))
        : get_term($product_categories[0])->name;
} else {
    $category = '';
}

// Get the image details
$img_src = get_resized_image($image, 'grid_card_image_2x') ?: ifne($GLOBALS, 'default_img');
$img_alt = $image['alt'] ?? $title;

// Check if the learn is still available
$today = current_time('Ymd');
$in_stock = $end_date >= $today;
$timestamp = date('j M', strtotime($start_date));
?>

<article class="learn__card">
    <a href="<?= $link ?>" class="learn__card-wrapper grid-item">
        <div class="learn__card-image-wrapper grid-item">
            <span class="style--small_button learn__card-date"><?= $start_date . ' - ' . $end_date ?></span>
            <img class="learn__hero-img" src="<?= $img_src ?>" alt="<?= $img_alt ?>" />
        </div>
        <div class="learn__card-content-wrapper">
            <div class="learn__hero-inner-content-wrapper">
                <h3 class="style--h5">
                    <?= $title ?>
                </h3>
                <div class="learn__card-details style--h6">
                    <span class="learn__hero-location">
                        <?= $location ?>
                    </span>
                    <span class="learn__hero-cost">
                        <?= $cost ?>
                    </span>
                </div>
                <h4 class="style--smallest_heading"><?= $type . (! empty($category) ? ' / ' . $category : ''); ?></h4>
            </div>
        </div>
    </a>
</article>