<?php

/****************************************************
 *
 * PRINT AWARDS
 *
 ****************************************************/

$PrintAwardsFactory = null;

function freoarts_init_print_awards_factory()
{

    global $PrintAwardsFactory;

    $PrintAwardsFactory = new PrintAwardsFactory();
    $PrintAwardsFactory->init();
}
add_action('init', 'freoarts_init_print_awards_factory', 1);

/**
 * Enable/disable the Print Award Submissions
 *
 * @return void
 */
function action_print_awards_submission_is_active() {
    if (is_page_template('template-print-awards-submission.php')) {
        global $post;
        $is_active = get_field('print_awards_submission_is_active', $post->ID);
        if (!$is_active) {
            wp_redirect(home_url());
            exit();
        }
    }
}
add_action('template_redirect', 'action_print_awards_submission_is_active');

/**
 * If the order was successful, update the print award submission entry
 *
 * @param $result
 * @param $order_id
 * @return mixed
 */
function print_awards_payment_successful_result($order_id)
{
    global $wpdb;

    $order = wc_get_order($order_id);
    $order_items = $order->get_items();

    foreach ($order_items as $order_item) {

        $_product = wc_get_product($order_item['product_id']);

        if ($_product->get_type() == 'print_award') {

            $submission = PrintAwardsSubmission::draftSubmission($order->user_id);

            $submission->update(array(
                'submitted' => date('Y-m-d H:i:s'),
                'paid' => 'yes'
            ));

            break;
        }
    }
}
add_action('woocommerce_pre_payment_complete', 'print_awards_payment_successful_result');

/**
 * Add printable Print Award page
 *
 */
function add_admin_print_awards_pages()
{

    add_submenu_page(
        null,
        'Printable',
        'Printable',
        'manage_options',
        'print_awards_submissions_printable',
        'view_admin_print_awards_printable'
    );
}
add_action('admin_menu', 'add_admin_print_awards_pages');

function view_admin_print_awards_printable()
{

    if (!(isset($_GET['entry']) && !empty($_GET['entry']))) {
        return;
    }
}

/**
 *
 *
 */
/*
function print_awards_login_redirect()
{

    if (isset($_REQUEST['printawards_redirect'])) {

        wp_redirect( get_option('home') . esc_url('/print-awards-submission/') );
    }
}
add_action('wp', 'print_awards_login_redirect', 1);
*/

function print_awards_override_login_redirect()
{

    if (isset($_GET['print_award_redirect']) && $_GET['print_award_redirect'] == true) {

        echo '<input type="hidden" name="_wp_http_referer" value="'. esc_attr( wp_unslash( '/print-awards-submission/' ) ) . '" />';
    }
}

add_action('woocommerce_login_form_end', 'print_awards_override_login_redirect');