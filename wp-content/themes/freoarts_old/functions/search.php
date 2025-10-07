<?php

function custom_search_where($where)
{

    if (is_search() && !is_admin()) {

        $filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_STRING);

        if (empty($filter) || $filter == 'courses') {

            // Add search tutor title
            $where = preg_replace(
                "/\(\s*wp_posts.post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
                "(wp_posts.post_title LIKE $1) OR (tutor_post.post_title LIKE $1)",
                $where
            );
        }

        if ($filter == 'courses') {

            // Add filter for start date if a 'product/course'
            $where .= " AND (
                wp_posts.post_type = 'product'
                AND
                CAST(start_date_meta.meta_value AS UNSIGNED) > CAST( DATE_FORMAT(NOW(), '%Y%m%d') AS UNSIGNED)
            ) ";
        }

        if (empty($filter)) {

            // Add filter for start date if a 'product/course'
            $where .= " AND (
                wp_posts.post_type IN ('post', 'page', 'whatson')
                OR
                (
                wp_posts.post_type = 'product'
                AND
                CAST(start_date_meta.meta_value AS UNSIGNED) > CAST( DATE_FORMAT(NOW(), '%Y%m%d') AS UNSIGNED)
                )
            ) ";
        }
    }

    return $where;
}
add_filter('posts_where' , 'custom_search_where');

function custom_search_join($join)
{

    global $wpdb;

    if (is_search() && !is_admin()) {

        $filter = filter_input(INPUT_GET, 'filter', FILTER_SANITIZE_STRING);

        if (empty($filter) || $filter == 'courses') {

            // Include tutor post ID (if exists)
            $join .= " LEFT JOIN wp_postmeta AS tutor_meta ON wp_posts.ID = tutor_meta.post_id AND tutor_meta.meta_key = 'tutor' ";

            // Include tutor post data (if exists)
            $join .= " LEFT JOIN wp_posts AS tutor_post ON tutor_meta.meta_value = tutor_post.ID ";

            // Include start date of 'product' post (if exists)
            $join .= " LEFT JOIN wp_postmeta AS start_date_meta ON wp_posts.ID = start_date_meta.post_id AND start_date_meta.meta_key = 'start_date' ";
        }
    }

    return $join;
}
add_filter('posts_join', 'custom_search_join');
