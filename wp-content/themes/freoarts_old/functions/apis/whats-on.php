<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('rest_api_init', function () {
    register_rest_route('humaan/v1', 'whatsonfilters', [
        'methods' => 'GET',
        'callback' => function (WP_REST_Request $request) {
            $type = $request->get_param('type');
            $startDate = $request->get_param('dateStart');
            $endDate = $request->get_param('dateEnd');
            $search = $request->get_param('search');
            $page = $request->get_param('page');
            $response = [
                'status' => 200,
                'html' => hum_whatson_events($type, $startDate, $endDate, $search, $page),
            ];
            return new WP_REST_Response($response);
        },
        'permission_callback' => '__return_true',
    ]);
});

function hum_whatson_events($type = '', $startDate = null, $endDate = null, $search = null, $page = null)
{

    $startDate = $startDate == null ? null : new DateTime($startDate);
    $endDate = $endDate == null ? new DateTime('+ 1 year') : new DateTime($endDate);
    $endDate = $endDate->setTime(0,0,1);
   
    // print_r($dates);

    $args_query = [
        'post_type' => 'whatson',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'order' => 'ASC',
        'orderby' => 'meta_value_num',
        'meta_key' => 'start_date',
    ];

    if ($page !== null) {
        $args_query['paged'] = $page;
    }

    if (!empty($search)) {
        $args_query['s'] = $search;
    }

    $args_query['meta_query'] = [
        'relation' => 'AND',
    ];


    if (!empty($type)) {
        $args_query['meta_query'][] = [
            [
                'key' => 'type',
                'compare' => '=',
                'value' => $type,
            ],
        ];
    }

    // start and end date
    $date_query = [];
    if(!empty($startDate) && !empty($endDate)) {
        $date_query['relation'] = 'AND';
        $date_query[] = [
            'key' => 'end_date',
            'value' => $startDate->format('Ymd'),
            'compare' => '>=',
            'type' => 'DATE'
        ];
        $date_query[] = [
            'key' => 'start_date',
            'value' => $endDate->format('Ymd'),
            'compare' => '<=',
            'type' => 'DATE'
        ];
        $args_query['meta_query'][] = $date_query;
    } else {
        // no date so get anything not ended
        $args_query['meta_query'][] = [
            'key' => 'end_date',
            'value' => date('Ymd'),
            'compare' => '>=',
            'type' => 'DATE'
        ];
    }

    $query = new WP_Query($args_query);

    $return = render('parts/whats-on/whats-on-grid.php', ["pages" => $query->posts], false);
    $return .= ajaxPaginationLinks($query);
    return $return;
}