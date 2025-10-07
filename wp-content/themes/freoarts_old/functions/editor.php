<?php

/****************************************************
 *
 * Editor
 *
 ****************************************************/

/**
 * Removes buttons from the first row of the tiny mce editor
 *
 * @link     http://thestizmedia.com/remove-buttons-items-wordpress-tinymce-editor/
 *
 * @param    array    $buttons    The default array of buttons
 * @return   array                The updated array of buttons that exludes some items
 */

/**
 * Assumptions:
 *
 * - Assumes that production ready builds have a theme/dist/css/editor-style.min.css file
 * - Assumes all builds have a theme/dist/css/editor-style.css file
 */

add_filter('mce_buttons', 'remove_tinymce_editor_buttons');

function remove_tinymce_editor_buttons($buttons)
{
    $remove_buttons = array(
        // 'bold',
        // 'italic',
        // 'strikethrough',
        // 'bullist',
        // 'numlist',
        // 'blockquote',
        // 'hr', // horizontal line
        // 'alignleft',
        // 'aligncenter',
        // 'alignright',
        // 'link',
        // 'unlink',
        'wp_more', // read more link
        // 'spellchecker',
        // 'dfw', // distraction free writing mode
        'wp_adv', // kitchen sink toggle (if removed, kitchen sink will always display)
    );

    foreach ($buttons as $button_key => $button_value) {
        if (in_array($button_value, $remove_buttons)) {
            unset($buttons[$button_key]);
        }
    }
    return $buttons;
}

/**
 * Removes buttons from the second row (kitchen sink) of the tiny mce editor
 *
 * @link     http://thestizmedia.com/remove-buttons-items-wordpress-tinymce-editor/
 *
 * @param    array    $buttons    The default array of buttons in the kitchen sink
 * @return   array                The updated array of buttons that exludes some items
 */

add_filter('mce_buttons_2', 'remove_tinymce_kitchen_sink_buttons');

function remove_tinymce_kitchen_sink_buttons($buttons)
{
    $remove_buttons = array(
        // 'formatselect', // format dropdown menu for <p>, headings, etc
        // 'underline',
        // 'alignjustify',
        'forecolor', // text color
        // 'pastetext', // paste as text
        // 'removeformat', // clear formatting
        'charmap', // special characters
        'outdent',
        'indent',
        // 'undo',
        // 'redo',
        // 'wp_help', // keyboard shortcuts
    );

    foreach ($buttons as $button_key => $button_value) {
        if (in_array($button_value, $remove_buttons)) {
            unset($buttons[$button_key]);
        }
    }

    return $buttons;
}

/**
 * Registers an editor stylesheet for the theme.
 */
function theme_add_editor_styles()
{

    add_editor_style('css/editor-style.css');
}

add_action('admin_init', 'theme_add_editor_styles');

/**
 * Register our callback to the appropriate filter
 *
 * @param $buttons
 * @return mixed
 */

function show_formats($buttons)
{

    array_unshift($buttons, 'styleselect');
    return $buttons;
}

add_filter('mce_buttons_2', 'show_formats');

add_filter('acf/fields/wysiwyg/toolbars', static function ($toolbars) {
    $toolbars['Very Basic'] = [];
    $toolbars['Very Basic'][1] = [
        'bold',
        'italic',
        'underline',
        'undo',
        'redo',
        'link',
        'pastetext',
        'removeformat',
        'fullscreen',
    ];

    return $toolbars;
});


function add_tiny_mce_formats($init_array)
{
    $style_formats = array(
        array(
            'title' => 'Lead Paragraph',
            'selector' => 'p',
            'classes' => 'text--lead',
        ),
    );
    $init_array['style_formats'] = wp_json_encode($style_formats);

    return $init_array;
}
add_filter('tiny_mce_before_init', 'add_tiny_mce_formats', 99);
