<?php

/****************************************************
 *
 * POST THUMBNAILS
 *
 ****************************************************/

/**
 * Set the default thumbnail size
 *
 */
set_post_thumbnail_size(128, 128, false);

/**
 * Add support for post thumbnails to post types
 *
 */
add_theme_support(
    'post-thumbnails',
    array(
        'product'
    )
);
remove_theme_support('post-formats');

/**
 * Specify the custom post thumbnail sizes
 *
 */
// add_image_size('flexible-thumbnail', 128, 128, true);
add_image_size('front_page_hero', 1920, 1080, true);
add_image_size('front_page_hero_mobile', 420, 900, true);

add_image_size('hero_image', 1180, 700, true);
add_image_size('subscribe_bg', 1400, 933, true);
add_image_size('whats_on_hero', 1680, 997, true);
add_image_size('whats_on_hero_2x', 3360, 1994, true);
add_image_size('whats_on_card', 560, 415, true);
add_image_size('whats_on_content', 600, 0, true);
add_image_size('related_item', 600, 450, true);
add_image_size('timeline_item', 600, 450, true);
add_image_size('child_page_tile', 375, 280, true);
add_image_size('grid_card_image_2x', 1080, 760, true);
add_image_size('grid_card_image_690', 690, 486, true);
add_image_size('grid_card_image', 540, 380, true);
add_image_size('staff_photo', 375, 280, true);
add_image_size('shop_card', 355, 266, true);
add_image_size('news_card', 355, 266, true);
add_image_size('course', 570, 430, true);
add_image_size('contact_image', 590, 654, true);
add_image_size('tutor_card', 240, 180, true);
add_image_size('cart_thumbnail', 50, 50, true);
add_image_size('c1100x520@2x', 2200, 1040, true);
add_image_size('c1100x520', 1100, 520, true);
add_image_size('c480x227', 480, 227, true);
add_image_size('c480x227@2x', 960, 454, true);
// TODO: New banner
add_image_size('hero_banner_2x', 2192, 1040, true);
add_image_size('hero_banner', 1096, 520, true);

/**
 * Custom post content image HTML
 *
 * @param $html
 * @param $id
 * @param $caption
 * @param $title
 * @param $align
 * @param $url
 * @param $size
 * @param $alt
 * @return string
 */
function custom_image_send_to_editor($html, $id, $caption, $title, $align, $url, $size, $alt)
{

    $image_thumb = get_resized_image($id, 'whats_on_content');
    $photographer = get_field('photographer', $id);

    ob_start();
?>
    <div class="inline-img-wrapper clearfix">
        <img src="<?php echo $image_thumb; ?>" alt="">
        <div class="caption-wrapper">
            <div class="inner">
                <p><?php echo $caption; ?></p>
                <span><em>Image:</em> <?php echo $title; ?></span>
                <span><em>Photographer:</em> <?php echo $photographer; ?></span>
            </div>
        </div>
    </div>
<?php
    $output = ob_get_contents();
    ob_end_clean();

    return $output;
}
// add_filter('image_send_to_editor', 'custom_image_send_to_editor', 1, 8);

/**
 * Experimental on-demand image resizing
 *
 * @param $width
 * @param $height
 * @param $max_w
 * @param $max_h
 * @param $crop
 * @return array (width/height)
 */
function get_projected_wh_after_resize($width, $height, $max_w, $max_h, $crop)
{

    $dims = image_resize_dimensions($width, $height, $max_w, $max_h, $crop);
    list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;

    return array(
        'width'  => $dst_w,
        'height' => $dst_h
    );
}

function humaan_media_downsize($out, $id, $size)
{

    if (is_array($size)) {

        return false;
    }

    // Check that the requested size exists, or abort
    global $_wp_additional_image_sizes;
    if (!isset($_wp_additional_image_sizes[$size])) {
        return false;
    }

    // If image size exists & size let WP serve it like normally
    $imagedata = wp_get_attachment_metadata($id);

    if (is_array($imagedata) && isset($imagedata['sizes'][$size])) {
        $config_size = ($_wp_additional_image_sizes[$size]);
        $actual_size = $imagedata['sizes'][$size];

        //Projected size after resize
        $projected_size = get_projected_wh_after_resize($imagedata['width'], $imagedata['height'], $config_size['width'], $config_size['height'], $config_size['crop']);

        $size_changed = false;

        $size_changed = (
            ($projected_size['width']  != $actual_size['width']) ||
            ($projected_size['height'] != $actual_size['height'])
        );

        if (!$size_changed) {
            //Size unchanged
            return false;
        } else {
            //Size config changed : will regen
        }
    }

    // Make the new thumb
    if (!$resized = image_make_intermediate_size(
        get_attached_file($id),
        $_wp_additional_image_sizes[$size]['width'],
        $_wp_additional_image_sizes[$size]['height'],
        $_wp_additional_image_sizes[$size]['crop']
    )) {
        return false;
    }

    // Save image meta, or WP can't see that the thumb exists now
    $imagedata['sizes'][$size] = $resized;
    wp_update_attachment_metadata($id, $imagedata);

    // Return the array for displaying the resized image
    $att_url = wp_get_attachment_url($id);
    return array(dirname($att_url) . '/' . $resized['file'], $resized['width'], $resized['height'], true);
}
add_filter('image_downsize', 'humaan_media_downsize', 10, 3);

function humaan_media_prevent_resize_on_upload($sizes)
{

    // Removing these defaults might cause problems, so we don't
    return array(
        'thumbnail' => ifne($sizes, 'thumbnail'),
        'medium' => ifne($sizes, 'medium'),
        'large' => ifne($sizes, 'large'),
        'flexible-thumbnail' => ifne($sizes, 'flexible-thumbnail')
    );
}
add_filter('intermediate_image_sizes_advanced', 'humaan_media_prevent_resize_on_upload');
