<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Add menu item to WordPress admin
add_action('admin_menu', 'hum_course_ordering_menu');
function hum_course_ordering_menu()
{
    add_submenu_page(
        'edit.php?post_type=product',
        'Course Ordering',
        'Course Ordering',
        'manage_options',
        'course-ordering',
        'hum_course_ordering_page'
    );
}

// Register settings
add_action('admin_init', 'hum_course_ordering_settings');
function hum_course_ordering_settings()
{
    register_setting('hum_course_ordering', 'hum_course_order_by');
    register_setting('hum_course_ordering', 'hum_course_order');

    add_settings_section(
        'hum_course_ordering_section',
        'Course Ordering Settings',
        'hum_course_ordering_section_callback',
        'course-ordering'
    );

    add_settings_field(
        'hum_course_order_by',
        'Order Courses By',
        'hum_course_order_by_callback',
        'course-ordering',
        'hum_course_ordering_section'
    );

    add_settings_field(
        'hum_course_order',
        'Order Direction',
        'hum_course_order_callback',
        'course-ordering',
        'hum_course_ordering_section'
    );
}

// Section callback
function hum_course_ordering_section_callback()
{
    echo '<p>Configure how courses are ordered on the frontend.</p>';
}

// Order by field callback
function hum_course_order_by_callback()
{
    $order_by = get_option('hum_course_order_by', 'start_date');
?>
    <select name="hum_course_order_by">
        <option value="start_date" <?php selected($order_by, 'start_date'); ?>>Start Date</option>
        <option value="title" <?php selected($order_by, 'title'); ?>>Title</option>
        <option value="price" <?php selected($order_by, 'price'); ?>>Price</option>
        <option value="registrations" <?php selected($order_by, 'registrations'); ?>>Registrations left</option>
        <option value="menu_order" <?php selected($order_by, 'menu_order'); ?>>Custom Order</option>
    </select>
<?php
}

// Order direction field callback
function hum_course_order_callback()
{
    $order = get_option('hum_course_order', 'ASC');
?>
    <select name="hum_course_order">
        <option value="ASC" <?php selected($order, 'ASC'); ?>>Ascending</option>
        <option value="DESC" <?php selected($order, 'DESC'); ?>>Descending</option>
    </select>
<?php
}

// Admin page callback
function hum_course_ordering_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }
?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('hum_course_ordering');
            do_settings_sections('course-ordering');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
<?php
}
