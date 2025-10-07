<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

add_action('rest_api_init', function () {
    register_rest_route('humaan/v1', 'coursesfilters', [
        'methods' => 'GET',
        'callback' => function (WP_REST_Request $request) {
            $type = $request->get_param('type');
            $audience = $request->get_param('audience');
            $adultCategories = $request->get_param('categoriesAdults');
            $kidCategories = $request->get_param('categoriesKids');
            $tutor = $request->get_param('tutor');
            $startDate = $request->get_param('dateStart');
            $endDate = $request->get_param('dateEnd');
            $search = $request->get_param('search');
            $page = $request->get_param('page');
            $response = [
                'status' => 200,
                'html' => hum_courses($type, $startDate, $endDate, $audience, $adultCategories, $kidCategories, $tutor, $search, $page),
            ];
            return new WP_REST_Response($response);
        },
        'permission_callback' => '__return_true',
    ]);
});

function parseDate($date)
{
    $date = trim($date);
    $date = str_replace(' ', '', $date);
    $date = str_replace('-', '', $date);
    return $date;
}

function hum_courses($type = '', $startDate = null, $endDate = null, $audience = null, $adultCategories = null, $kidCategories = null,  $tutor = null, $search = null, $page = null)
{
    $args_query = [
        'post_type' => 'product',
        'ignore_sticky_posts' => 1,
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'tax_query' => [
            [
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'course'
            ]
        ],
        'meta_query' => [
            'relation' => 'AND',
        ],
    ];

    // Get ordering settings
    $order_by = get_option('hum_course_order_by', 'start_date');
    $order = get_option('hum_course_order', 'ASC');

    // Apply ordering
    switch ($order_by) {
        case 'price':
            $args_query['orderby'] = 'meta_value_num';
            $args_query['meta_key'] = '_price';
            break;
        case 'title':
            $args_query['orderby'] = 'title';
            break;
        case 'registrations':
            $args_query['orderby'] = 'meta_value_num';
            $args_query['meta_key'] = '_stock';
            break;
        case 'menu_order':
            $args_query['orderby'] = 'menu_order';
            break;
        case 'start_date':
        default:
            $args_query['orderby'] = 'meta_value';
            $args_query['meta_key'] = 'start_date';
            break;
    }
    $args_query['order'] = $order;

    if ($page !== null) {
        $args_query['paged'] = $page;
    }

    if (!empty($search)) {
        $args_query['s'] = $search;
    }

    if (!empty($startDate)) {
        $startDate = parseDate($startDate);
    }

    if (!empty($endDate)) {
        $endDate = parseDate($endDate);
    }

    // start and end date
    if (!empty($startDate) && !empty($endDate)) {
        $args_query['meta_query'][] = [
            'key' => 'start_date',
            'value' => date('Ymd', strtotime($startDate)),
            'compare' => '>=',
            'type' => 'DATE'
        ];

        $args_query['meta_query'][] = [
            'key' => 'end_date',
            'value' => date('Ymd', strtotime($endDate)),
            'compare' => '<=',
            'type' => 'DATE'
        ];
    } else {
        $args_query['meta_query'][] = [
            'key' => 'start_date',
            'value' => date('Ymd'),
            'compare' => '>=',
            'type' => 'DATE'
        ];
    }

    if (!empty($type)) {
        // if (is_numeric($time_period)) {
        //     $start_date = date('Ymd', strtotime($time_period));
        //     $end_date = date('Ymd', strtotime($time_period));

        //     $args_query['meta_query'] = [
        //         'relation' => 'AND',
        //         [
        //             'relation' => 'OR',
        //             [
        //                 'key' => 'start_date',
        //                 'value' => [$start_date, $end_date],
        //                 'compare' => 'BETWEEN',
        //                 'type' => 'DATE'
        //             ],
        //             [
        //                 'key' => 'end_date',
        //                 'value' => [$start_date, $end_date],
        //                 'compare' => 'BETWEEN',
        //                 'type' => 'DATE'
        //             ],
        //         ]
        //     ];
        // } else {
        // $time_period_value = 'One Day Workshop';
        if ($type === 'one-day') {
            $time_period_value = 'One Day Workshop';
        } else if ($type === 'weekend') {
            $time_period_value = 'Weekend Workshop';
        } else if ($type === 'multi-week') {
            $time_period_value = 'Multi Week Workshop';
        }

        if (!empty($time_period_value)) {
            $args_query['meta_query'][] = [
                'key' => 'time_period',
                'value' => '"' . $time_period_value . '"',
                'compare' => 'LIKE'
            ];
        }

        // }
    }


    if (!empty($audience) && $audience === 'for-kids') {
        $args_query['meta_query'][] = [
            'key' => 'is_kids_course',
            'value' => 1,
            'type' => 'BOOLEAN',
            'compare' => '=='
        ];
    }

    if (!empty($audience) && $audience === 'for-adults') {
        $args_query['meta_query'][] = [
            'key' => 'is_kids_course',
            'value' => 0,
            'type' => 'BOOLEAN',
            'compare' => '=='
        ];
    }

    if ($audience === 'for-adults' && !empty($adultCategories)) {
        $args_query['tax_query'] = [
            [
                'taxonomy' => 'course_adults_category',
                'field' => 'term_id',
                'terms' => explode(",", $adultCategories),
                'include_children' => false,
            ]
        ];
    }

    if ($audience === 'for-kids' && !empty($kidCategories)) {
        $args_query['tax_query'] = [
            [
                'taxonomy' => 'course_kids_category',
                'field' => 'term_id',
                'terms' => explode(",", $kidCategories),
                'include_children' => false,
            ]
        ];
    }

    if (!empty($tutor)) {
        $args_query['meta_query'][] = [
            'key' => 'tutor',
            'value' => $tutor,
            'compare' => '='
        ];
    }



    $query = new WP_Query($args_query);

    $return = render('parts/courses/courses-grid.php', ["pages" => $query->posts], false);

    // set base url to refferer
    // $return .= ajaxPaginationLinks($query, $_SERVER['HTTP_REFERER']);

    $return .= ajaxPaginationLinks($query);

    // print_r($args_query);
    return $return;
}
