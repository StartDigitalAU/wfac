<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('rest_api_init', function () {
    register_rest_route('humaan/v1', 'newsfilters', [
        'methods' => 'GET',
        'callback' => function (WP_REST_Request $request) {
            $type = $request->get_param('type');
            $startDate = $request->get_param('dateStart');
            $endDate = $request->get_param('dateEnd');
            $search = $request->get_param('search');
            $page = $request->get_param('page');
            $response = [
                'status' => 200,
                'html' => hum_news($type, $startDate, $endDate, $search, $page),
            ];
            return new WP_REST_Response($response);
        },
        'permission_callback' => '__return_true',
    ]);
});

function hum_news($type = '', $startDate = null, $endDate = null, $search = null, $page = null)
{
    $start_date = $startDate !== null ? date('Y-m-d', strtotime($startDate)) : null;
    $end_date = $endDate !== null ? date('Y-m-d', strtotime($endDate)) : null;

    $args_query = array(
        'post_type' => array('post'),
        'post_status' => array('publish'),
        'posts_per_page' => 12,
        'order' => 'DESC',
        'orderby' => 'date',
        'ignore_sticky_posts' => 1,
    );

    if ($page !== null) {
        $args_query['paged'] = $page;
    }

    if (!empty($search)) {
        $args_query['s'] = $search;
    }

    if ($start_date !== null && $end_date !== null) {
        $args_query['date_query'] = [
            [
                'after' => $start_date,
                'before' => $end_date,
                'inclusive' => true,
            ]
        ];
    }

    if (!empty($type)) {
        $args_query['meta_query'][] = array(
            array(
                'key' => 'type',
                'compare' => '=',
                'value' => $type,
            ),
        );
    }

    $query = new WP_Query($args_query);

    $return = render('parts/news/news-grid.php', ["pages" => $query->posts], false);
    $return .= ajaxPaginationLinks($query);
    return $return;
}