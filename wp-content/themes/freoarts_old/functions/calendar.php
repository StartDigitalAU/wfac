<?php

/****************************************************
 *
 * CALENDAR
 *
 ****************************************************/

/**
 * Handler for AJAX request
 *
 */
function get_calendar_events() {
    $selected_post_type = filter_input(INPUT_GET, 'selected-post-type', FILTER_SANITIZE_STRING);
    $selected_year = filter_input(INPUT_GET, 'selected-year', FILTER_VALIDATE_INT);
    $selected_month = filter_input(INPUT_GET, 'selected-month', FILTER_VALIDATE_INT);

    $current_month = mktime(0, 0, 0, $selected_month, 1,   $selected_year);
    $prev_month = mktime(0, 0, 0, $selected_month - 1, 1,   $selected_year);
    $next_month  = mktime(0, 0, 0, $selected_month + 1,   1,   $selected_year);

    $output = [
        'current_html' => date('F Y', $current_month),
        'prev_html' => '<li class="cal-prev"
                                    title="Previous month"
                                    data-month="' . date('n', $prev_month) . '"
                                    data-year="' . date('Y', $prev_month) . '">
                                    <span class="text">' . date('M Y', $prev_month) . '</span>
                                    <span class="icon"></span>
                                </li>',
        'next_html' => '<li class="cal-next"
                                    title="Next month"
                                    data-month="' . date('n', $next_month) . '"
                                    data-year="' . date('Y', $next_month) . '">
                                    <span class="text">' . date('M Y', $next_month) . '</span>
                                    <span class="icon"></span>
                                </li>',
        'calendar_html' => get_calendar_events_html($selected_post_type, $selected_year, $selected_month)
    ];

    wp_send_json($output, 200);
}

/**
 * Return a HTML formatted list of days for use within a calendar
 *
 */
function get_calendar_events_html($post_type = 'post', $selected_year = null, $selected_month = null, $today_is_active = false)
{
    $selected_year = $selected_year ?: date('Y');
    $selected_month = $selected_month ?: date('n');

    // Stores the days to be rendered
    $printable_dates = [];

    // Get first of the month
    $first_day_ts = mktime( 0, 0, 0, $selected_month, 1, $selected_year );
    // Numeric representation of the day of the week
    $first_day_dow = date( 'w', $first_day_ts );
    // Number of days in the month
    $number_of_days = date( 't', $first_day_ts );

    // Add days "before" the currently selected month i.e. padding days
    $pad_front = $first_day_dow;
    for ($i = 0; $i < $pad_front; $i++) {
        $printable_ts = mktime( 0, 0, 0, $selected_month, ( 1 - ( $pad_front - $i ) ), $selected_year );
        $printable_dates[] = [
            'ts' => $printable_ts, // Unix timestamp
            'date' => date( 'Ymd', $printable_ts ),
            'formatted' => date( 'j', $printable_ts ),
            'day' => date( 'D', $printable_ts ),
            'pad' => true,
            'post_types' => []
        ];
    }

    // Add the days "within" the selected month
    for ( $i = 1; $i <= $number_of_days; $i++ ){
        $printable_ts = mktime( 0, 0, 0, $selected_month, $i, $selected_year );
        $printable_dates[] = [
            'ts' => $printable_ts, // Unix timestamp
            'date' => date( 'Ymd', $printable_ts ),
            'formatted' => date( 'j', $printable_ts ),
            'day' => date( 'D', $printable_ts ),
            'pad' => false,
            'post_types' => []
        ];
    }

    // Add days "after" the currently selected month i.e. padding days
    $last_day_ts = mktime( 0, 0, 0, $selected_month, $number_of_days, $selected_year );
    $last_day_dow = date( 'w', $last_day_ts );
    $pad_back = ( 6 - $last_day_dow );
    for( $i = 1; $i <= $pad_back; $i++ ) {
        $printable_ts = mktime( 0, 0, 0, $selected_month, $number_of_days + $i, $selected_year );
        $printable_dates[] = [
            'ts' => $printable_ts, // Unix timestamp
            'date' => date( 'Ymd', $printable_ts ),
            'formatted' => date( 'j', $printable_ts ),
            'day' => date( 'D', $printable_ts ),
            'pad' => true,
            'post_types' => []
        ];
    }

    // Get the timestamps for the first and last day in the calendar (including padding days)
    $first_query_date = $printable_dates[0]['date'];
    $last_query_date = $printable_dates[ count( $printable_dates ) - 1 ]['date'];
    $first_query_timestamp = $printable_dates[0]['ts'];
    $last_query_timestamp = $printable_dates[ count( $printable_dates ) - 1 ]['ts'];

    // Build the get_posts arguments for retrieving the posts to display within the calendar
    $query_args = [
        'post_type' => $post_type,
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'orderby' => 'ID',
        'order' => 'ASC'
    ];

    if ($post_type === 'post') {
        $query_args['date_query'] = [
            [
                'after' => [
                    'year' => date('Y', $first_query_timestamp),
                    'month' => date('n', $first_query_timestamp),
                    'day' => date('j', $first_query_timestamp),
                ],
                'before' => [
                    'year' => date('Y', $last_query_timestamp),
                    'month' => date('n', $last_query_timestamp),
                    'day' => date('j', $last_query_timestamp),
                ],
                'inclusive' => true
            ]
        ];
    }

    if ($post_type === 'product') {
        $query_args['tax_query'] = [
            [
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'course'
            ]
        ];
    }

    if ($post_type === 'whatson' || $post_type === 'product') {
        $query_args['meta_query'] = [
            'relation' => 'OR',
            [
                'relation' => 'OR',
                [
                    'key' => 'start_date',
                    'value' => [$first_query_date, $last_query_date],
                    'type' => 'numeric',
                    'compare' => 'BETWEEN'
                ],
                [
                    'key' => 'end_date',
                    'value' => [$first_query_date, $last_query_date],
                    'type' => 'numeric',
                    'compare' => 'BETWEEN'
                ]
            ],
            [
                'relation' => 'AND',
                [
                    'key' => 'start_date',
                    'value' => $first_query_date,
                    'type' => 'numeric',
                    'compare' => '<='
                ],
                [
                    'key' => 'end_date',
                    'value' => $last_query_date,
                    'type' => 'numeric',
                    'compare' => '>='
                ]
            ]
        ];
    }

    // Get the posts
    $posts = get_posts($query_args);

    foreach ($posts as $post) {
        // Default assumes news post
        $post_type = 'news';
        $post_dates = [date('Ymd', strtotime($post->post_date))];

        if ($post->post_type === 'whatson') {
            $post_type = get_field('type', $post->ID);
            $post_start_date = get_field('start_date', $post->ID);
            $post_end_date = get_field('end_date', $post->ID);

            // TODO: Add for all days inbetween
            // if ($post_start_date === $post_end_date) {
                $post_dates = [$post_start_date];
            // }
        }

        if ($post->post_type === 'product') {
            $post_type = 'course';
            $post_start_date = get_field('start_date', $post->ID);
            $post_end_date = get_field('end_date', $post->ID);

            // TODO: Add for all days inbetween
            // if ($post_start_date === $post_end_date) {
            $post_dates = [$post_start_date];
            // }
        }

        // Add the post to the printable days
        foreach ($printable_dates as $key => $printable_date) {
            if (in_array($printable_date['date'], $post_dates)) {
                $printable_dates[$key]['post_types'][] = $post_type;
            }
        }
    }

    $first_row = false;
    $output = '';
    foreach ($printable_dates as $printable_date) {

        if ($printable_date['day'] == 'Sun' ) {
            $output .= '<ul>';
        }

        $classes = [];
        $event_types = [];

        if ($printable_date['pad']) {
            $classes[] = 'pad';
        }

        if (count($printable_date['post_types'])){
            $classes[] = 'has-post';
        }

        if (time() > $printable_date['ts']) {
            $classes[] = 'past';
        }

        if (date('Ymd', $printable_date['ts']) == (date( 'Ymd', time()))){
            $classes[] = 'today';
            if ($today_is_active) {
                $classes[] = 'is-active';
            }
        }

        foreach ($printable_date['post_types'] as $post_type) {
            $event_types[$post_type] = 'type-' . $post_type;
        }

        $classes_html = count($classes) ? ' class="' . implode(' ', $classes) . '"' : '';

        $output .= '<li' . $classes_html . ' data-date="' . $printable_date['date'] . '">';

        if ($event_types) {
            $output .= '<a href="#" title="Events for ' . date('d-m-Y', $printable_date['ts'] ) . '">';
        }

        $output .= '<span>' . $printable_date[ 'formatted' ] . '</span>';

        if ($first_row) {
            $output .= '<span class="abbr">' . date('D', $printable_date['ts'] ) . '</span>';
        }

        if ($event_types) {
            $output .= '</a>';
            $output .= '<span class="event-types ' . implode(' ', $event_types) . '"></span>';
        }

        $output .= '</li>';

        if ($printable_date['day'] === 'Sat' ){
            $output .= '</ul>';
            $first_row = false;
        }
    }

    return '<div class="output">' . $output . '</div>';
}
add_action('wp_ajax_nopriv_get-calendar-events', 'get_calendar_events');
add_action('wp_ajax_get-calendar-events', 'get_calendar_events');