<?php

/****************************************************
 *
 * REPORTING
 *
 ****************************************************/

include_once(TEMPLATEPATH . '/functions/reports-courses.php');
include_once(TEMPLATEPATH . '/functions/reports-all-enrolments.php');
include_once(TEMPLATEPATH . '/functions/reports-transaction-history.php');
include_once(TEMPLATEPATH . '/functions/reports-membership-discounts.php');
include_once(TEMPLATEPATH . '/functions/reports-membership-subscriptions.php');

/**
 * Add Reports admin menu items
 *
 */
function add_admin_report_pages() {

    add_menu_page(
        'Reports',
        'Reports',
        'reports_main', // Capability
        'reports-main',
        'view_admin_report_courses',
        'dashicons-analytics',
        200
    );

    add_submenu_page(
        'reports-main',
        'Courses',
        'Courses',
        'reports_courses', // Capability
        'reports-courses',
        'view_admin_report_courses'
    );

    add_submenu_page(
        'reports-main',
        'Enrollees',
        'Enrollees',
        'reports_enrollees', // Capability
        'reports-all-enrolments',
        'view_admin_report_all_enrolments'
    );

    add_submenu_page(
        'reports-main',
        'Transaction History',
        'Transaction History',
        'reports_transaction_history', // Capability
        'reports-transaction-history',
        'view_admin_report_transaction_history'
    );

    add_submenu_page(
        'reports-main',
        'Membership Discounts',
        'Membership Discounts',
        'reports_membership_discounts', // Capability
        'reports-membership-discounts',
        'view_admin_report_membership_discounts'
    );

    add_submenu_page(
        'reports-main',
        'Membership Subscriptions',
        'Membership Subscriptions',
        'reports_membership_discounts', // Capability
        'reports-membership-subscriptions',
        'view_admin_report_membership_subscriptions'
    );
}
add_action('admin_menu', 'add_admin_report_pages');





