<?php

$sizes_array = array(
    's680x400' => array(
        'width'  => 688,
        'height' => 400,
        'crop'   => false,
    ),
    's626x300' => array(
        'width'  => 626,
        'height' => 300,
        'crop'   => false,
    ),
    's340x240' => array(
        'width'  => 340,
        'height' => 240,
        'crop'   => false,
    ),
    's1100x540' => array(
        'width'  => 1100,
        'height' => 540,
        'crop'   => false,
    ),
    's1440x540' => array(
        'width'  => 1440,
        'height' => 540,
        'crop'   => false,
    ),
    'c516x240' => array(
        'width'  => 516,
        'height' => 240,
        'crop'   => true,
    ),
);

generate_image_sizes($sizes_array);

function generate_image_sizes($sizes_array) {
    global $_wp_additional_image_sizes;
    $existing_sizes = get_intermediate_image_sizes();
    foreach ($sizes_array as $size_data) {
        $width = $size_data['width'];
        $height = $size_data['height'];
        $crop = $size_data['crop'];
        $prefix = $crop === true ? 'c' : 's';
        $size_name = $prefix . $width . 'x' . $height;
        if (!in_array($size_name, $existing_sizes)) {
            add_image_size($size_name, $width, $height, $crop);
        } else {
            $_wp_additional_image_sizes[$size_name] = array(
                'width'  => $width,
                'height' => $height,
                'crop'   => $crop,
            );
        }
    }
}