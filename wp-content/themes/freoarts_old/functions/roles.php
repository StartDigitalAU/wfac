<?php

/****************************************************
 *
 * ROLES
 *
 ****************************************************/

/**
 * Create Member user role
 *
 */
function freoarts_create_member_role()
{

    // remove_role('member'); // Useful for updating role

    $role = get_role('member_individual');

    // If Member role doesn't exist, create it
    if (is_null($role)) {

        add_role(
            'member_individual',
            __('Member (Individual)'),
            array(
                'read' => true,
                'level_0' => true
            )
        );
    }
    else {

        // Role already exists
    }

    $role = get_role('member_concession');

    // If Member role doesn't exist, create it
    if (is_null($role)) {

        add_role(
            'member_concession',
            __('Member (Concession)'),
            array(
                'read' => true,
                'level_0' => true
            )
        );
    }
    else {

        // Role already exists
    }
}
add_action('init', 'freoarts_create_member_role');

/**
 * Check if current user has a member role
 *
 * @return bool
 */
function has_user_member_role()
{

    if (is_user_logged_in()) {

        $current_user = wp_get_current_user();

        $roles = $current_user->roles;

        foreach ($roles as $role) {

            if (
                $role == 'member_individual' ||
                $role == 'member_concession'
            ) {

                return $role;
            }
        }
    }

    return false;
}

/**
 * Create and delete roles
 *
 */
function freoarts_user_roles()
{

    // Remove unnecessary roles
    /*
    remove_role('member');
    remove_role('reception');
    remove_role('wholesale_tax_free');
    remove_role('wholesale_buyer');
    remove_role('shop_manager');
    remove_role('staff_reception');
    */
}
add_action('init', 'freoarts_user_roles');