<?php

/****************************************************
 *
 * POPULAR POSTS
 *
 ****************************************************/

if (!function_exists('get_popular_posts')) {
    /**
     * Returns an array of the most recent popular posts.
     *
     * @uses $wpdb
     *
     * @link https://codex.wordpress.org/Class_Reference/wpdb Class reference for $wpdb
     * @link https://wordpress.org/plugins/wordpress-popular-posts/ The WordPress plugin, WordPress Popular Posts, that generates the lists of popular posts
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @param  int          $limit   The number of posts you want returned
     * @param  array|null   $exclude Provide an array of Post IDs to be excluded, or null if you don't want to exclude any Post IDs
     * @return string|array          Returns an array with 0 or more results, or a string if WpPP doesn't exist
     */
    function get_popular_posts($limit, $exclude = null)
    {
        global $wpdb;

        if (!class_exists('WordpressPopularPosts')) {
            return 'WordPress Popular Posts plugin is not installed!';
        }

        if (is_null($exclude)) {
            $popular_posts = $wpdb->get_results("
			SELECT
				postid,
				pageviews
			FROM wp_popularpostssummary
			LEFT JOIN wp_posts
			ON wp_popularpostssummary.postid = wp_posts.ID
			LEFT JOIN wp_term_relationships
			ON wp_popularpostssummary.postid = wp_term_relationships.object_id
			WHERE
				view_date > '" . date('Y-m-d', time() - (7 * 86400) ) . "'
			AND
				wp_posts.post_type='post'
			GROUP BY postid
			ORDER BY pageviews, last_viewed DESC
			LIMIT " . $limit
            );
        } else {
            $popular_posts = $wpdb->get_results("
			SELECT
				postid,
				pageviews
			FROM wp_popularpostssummary
			LEFT JOIN wp_posts
			ON wp_popularpostssummary.postid = wp_posts.ID
			LEFT JOIN wp_term_relationships
			ON wp_popularpostssummary.postid = wp_term_relationships.object_id
			WHERE
				view_date > '" . date('Y-m-d', time() - (7 * 86400) ) . "'
			AND
				wp_posts.post_type='post'
			AND postid NOT IN (" . implode(',',  $exclude) . ")
			GROUP BY postid
			ORDER BY pageviews, last_viewed DESC
			LIMIT " . $limit
            );
        }

        $post_ids = array();

        foreach ($popular_posts as $popular_post) {
            $post_ids[] = $popular_post->postid;
        }

        return $post_ids;
    }
}