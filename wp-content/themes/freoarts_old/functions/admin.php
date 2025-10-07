<?php

/**
 * Display page template's name column into admin
 */
//Add the custom column to the post type
add_filter('manage_pages_columns', 'humaan_add_custom_column');
function humaan_add_custom_column($columns) {
    $columns['template'] = 'Template';
    return $columns;
}
// Add the data to the custom column
add_action('manage_pages_custom_column', 'humaan_add_custom_column_data', 10, 2);
function humaan_add_custom_column_data($column, $post_id) {
    switch ($column) {
    case 'template':
        $post = get_post($post_id);
        echo get_page_template_slug($post);
        break;
    }
}