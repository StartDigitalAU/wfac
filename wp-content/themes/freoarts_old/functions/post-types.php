<?php

/****************************************************
 *
 * POST TYPES
 *
 ****************************************************/
 
function register_custom_post_types() {

    // Remove core post type support
	remove_post_type_support( 'post', 'thumbnail');
	remove_post_type_support( 'post', 'excerpt');

    register_post_type( 'whatson', array(
        'label'               => 'What\'s On',
        'labels'              => array(
            'name'                => 'What\'s On',
            'singular_name'       => 'What\'s On',
            'menu_name'           => 'What\'s On',
            'name_admin_bar'      => 'What\'s On',
            'parent_item_colon'   => 'Parent What\'s On:',
            'all_items'           => 'All What\'s On',
            'add_new_item'        => 'Add New What\'s On',
            'add_new'             => 'Add New',
            'new_item'            => 'New What\'s On',
            'edit_item'           => 'Edit What\'s On',
            'update_item'         => 'Update What\'s On',
            'view_item'           => 'View What\'s On',
            'search_items'        => 'Search What\'s On',
            'not_found'           => 'Not found',
            'not_found_in_trash'  => 'Not found in Trash',
        ),
        'supports'            => array(
            'title',
            'page-attributes',
            'editor'
        ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => null,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => array(
            'slug'                => 'whats-on/post',
            'with_front'          => false,
            'pages'               => false,
            'feeds'               => false,
        ),
        'capability_type'     => 'post',
    ) );
	remove_post_type_support('whatson', 'comments');

    register_post_type( 'tutor', array(
        'label'               => 'Tutors',
        'labels'              => array(
            'name'                => 'Tutors',
            'singular_name'       => 'Tutor',
            'menu_name'           => 'Tutors',
            'name_admin_bar'      => 'Tutors',
            'parent_item_colon'   => 'Parent Tutor:',
            'all_items'           => 'All Tutors',
            'add_new_item'        => 'Add New Tutor',
            'add_new'             => 'Add New',
            'new_item'            => 'New Tutor',
            'edit_item'           => 'Edit Tutor',
            'update_item'         => 'Update Tutor',
            'view_item'           => 'View Tutor',
            'search_items'        => 'Search Tutors',
            'not_found'           => 'Not found',
            'not_found_in_trash'  => 'Not found in Trash',
        ),
        'supports'            => array(
            'title',
            'page-attributes',
            'editor'
        ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'menu_position'       => null,
        'show_in_admin_bar'   => true,
        'show_in_nav_menus'   => true,
        'can_export'          => true,
        'has_archive'         => false,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'rewrite'             => array(
            'slug'                => 'tutor',
            'with_front'          => false,
            'pages'               => false,
            'feeds'               => false,
        ),
        'capability_type'     => 'post',
    ) );

}
add_action( 'init', 'register_custom_post_types' );

/**
 * Update the Posts label to News
 *
 */
function change_posts_to_news_labels() {

    global $menu;
    global $submenu;

    $menu[5][0] = 'News';

    if (isset($submenu['edit.php'][5])) {
        $submenu['edit.php'][5][0] = 'News';
    }

    if (isset($submenu['edit.php'][10])) {
        $submenu['edit.php'][10][0] = 'Add News';
    }

    if (isset($submenu['edit.php'][16])) {
        $submenu['edit.php'][16][0] = 'News Tags';
    }
}
add_action('admin_menu', 'change_posts_to_news_labels');

/**
 * Update the Posts labels to News
 *
 */
function change_posts_to_news_object() {

    global $wp_post_types;

    $labels = &$wp_post_types['post']->labels;
    $labels->name = 'News';
    $labels->singular_name = 'News';
    $labels->add_new = 'Add News';
    $labels->add_new_item = 'Add News';
    $labels->edit_item = 'Edit News';
    $labels->new_item = 'News';
    $labels->view_item = 'View News';
    $labels->search_items = 'Search News';
    $labels->not_found = 'No News found';
    $labels->not_found_in_trash = 'No News found in Trash';
    $labels->all_items = 'All News';
    $labels->menu_name = 'News';
    $labels->name_admin_bar = 'News';
}
add_action('init', 'change_posts_to_news_object');


