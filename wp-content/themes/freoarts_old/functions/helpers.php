<?php

/****************************************************
 *
 * HELPERS
 *
 ****************************************************/

use SendGrid\Email;
use SendGrid\Exception as SendGridException;

/**
 * @param  bool $post_id
 * @return string
 */
function get_post_category_links($post_id = false)
{
    $categories = get_the_category($post_id);

    $output = '';

    $separator = ', ';

    $counter = 0;

    foreach ($categories as $category) {
        if (0 < $counter) {
            $output .= $separator;
        }

        $output .= '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="internal-link">' . $category->name . '</a>';

        ++$counter;
    }

    return $output;
}

function the_post_category_links($post_id = false)
{
    echo get_post_category_links($post_id);
}

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
            $popular_posts = $wpdb->get_results(
                "
			SELECT
				postid,
				pageviews
			FROM wp_popularpostssummary
			LEFT JOIN wp_posts
			ON wp_popularpostssummary.postid = wp_posts.ID
			LEFT JOIN wp_term_relationships
			ON wp_popularpostssummary.postid = wp_term_relationships.object_id
			WHERE
				view_date > '" . date('Y-m-d', time() - (7 * 86400)) . "'
			AND
				wp_posts.post_type='post'
			GROUP BY postid
			ORDER BY pageviews, last_viewed DESC
			LIMIT " . $limit
            );
        } else {
            $popular_posts = $wpdb->get_results(
                "
			SELECT
				postid,
				pageviews
			FROM wp_popularpostssummary
			LEFT JOIN wp_posts
			ON wp_popularpostssummary.postid = wp_posts.ID
			LEFT JOIN wp_term_relationships
			ON wp_popularpostssummary.postid = wp_term_relationships.object_id
			WHERE
				view_date > '" . date('Y-m-d', time() - (7 * 86400)) . "'
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

if (!function_exists('random_token')) {
    /**
     * Generates a randomised alphanumeric token of a specified length (defaults to 5).
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @param  int    $length The number of characters your random token should return.  If using for activation tokens etc, a minimum length of 40 is recommended
     * @return string         The random alphanumeric string
     */
    function random_token($length = 5)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);

        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }

        return $result;
    }
}

if (!function_exists('escape_title')) {
    /**
     * Returns an HTML encoded version of the title, that is safe for displaying in the `<title>` tag.
     *
     * At some point, `the_title` stopped auto-escaping HTML inside the title.  This function hooks into the `the_title` filter and escapes the content before it's outputted.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @uses h
     *
     * @see add_filter
     *
     * @link http://codex.wordpress.org/Function_Reference/add_filter Function reference for add_filter
     * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/the_title Filter reference for the_title
     *
     * @param  string $title The title of the page you wish to encode
     * @return string        The encoded string that can safely be printed in HTML
     */
    function escape_title($title)
    {
        return h($title);
    }
}

if (!function_exists('h')) {
    /**
     * Returns text that is properly escaped and can be safely displayed in raw HTML
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @used-by eh
     * @used-by hifne
     * @used-by obfuscate_email
     * @used-by autop
     * @used-by escape_title
     *
     * @param  string $str The string that you wish to encode
     * @return string      The encoded string that can be safely be printed in HTML
     */
    function h($str)
    {
        return htmlspecialchars($str, (ENT_COMPAT | ENT_HTML5), 'UTF-8', false);
    }
}

if (!function_exists('eh')) {
    /**
     * Shortcut for echoing the h method
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @uses h
     *
     * @param  string $str The string that you wish to encode
     * @return void        The encoded string that is echo'd into the output
     */
    function eh($str)
    {
        echo h($str);
    }
}

if (!function_exists('eu')) {
    /**
     * Encodes and echoes a URL.
     *
     * @author Dan Barrett <danb@humaan.com.au>
     *
     * @uses esc_url
     *
     * @param  string $url The URL that you wish to encode
     * @return void
     */
    function eu($url)
    {
        echo esc_url($url);
    }
}

if (!function_exists('ifne')) {
    /**
     * Checks to see if a key of the array exists, and returns it if it does.
     *
     * Returns '' by default.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @used-by hifne
     *
     * @param  array  $var     An array to be searched.  If this is not an array, `$default` is returned instead
     * @param  string $index   The key name to be searched for
     * @param  mixed  $default If the `$index` doesn't exist, this should be returned instead
     * @return mixed           The value of `$var[$index]` if it exists, or `$default` if it doesn't
     */
    function ifne($var, $index, $default = '')
    {
        if (is_array($var)) {
            return (array_key_exists($index, $var) ? $var[$index] : $default);
        } else {
            return $default;
        }
    }
}

if (!function_exists('hifne')) {
    /**
     * Helper for ifne that returns HTML encoded text safe for outputting.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @uses ifne
     * @uses h
     *
     * @param  array  $var     An array to be searched.  If this is not an array, `$default` is returned instead
     * @param  string $index   The key name to be searched for
     * @param  string $default If the `$index` doesn't exist, this should be returned instead
     * @return string          The value of `$var[$index]` if it exists, or `$default` if it doesn't
     */
    function hifne($var, $index, $default = '')
    {
        return h(ifne($var, $index, $default));
    }
}

if (!function_exists('preload')) {
    /**
     * Adds the asset to the Nucleus preloader $GLOBALS array.
     *
     * @author Dedy Lawidjaja <dedy@humaan.com.au>
     *
     * @param  string $asset Asset URL
     * @return void
     */
    function preload($asset)
    {
        $GLOBALS['preload_assets'][] = $asset;
    }
}

if (!function_exists('pluralise')) {
    /**
     * Quickly pluralises a word if the count is 0 or greater than 1.
     *
     * @param  integer $count
     * @param  string  $word
     * @return string
     */
    function pluralise($count, $word)
    {
        return $count . ' ' . ($count == 1 ? $word : $word . 's');
    }
}

if (!function_exists('get_current_base_url')) {
    /**
     * Returns the current base URL for the current page.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @return string The current base URL for the page
     */
    function get_current_base_url()
    {
        $url = remove_query_arg('ajax', $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $url = remove_query_arg('compact', $url);
        return 'http://' . $url;
    }
}

if (!function_exists('strip_url_protocol')) {
    /**
     * Removes the protocol from a URL string.
     *
     * This should only be used if you need to strip the `http://` or `https://` from a URL, it doesn't remove **any** other protocols from URLs.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @param  string       $url A full URL with the protocol at the start
     * @return mixed|string The full URL without a protocol at the start
     */
    function strip_url_protocol($url)
    {
        $url = rtrim($url, '/');
        $disallowed = array('http://', 'https://');

        foreach ($disallowed as $d) {
            if (strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }

        return $url;
    }
}

if (!function_exists('autop')) {
    /**
     * Returns the string with lines wrapped with the `<p>` tag.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @deprecated This function should no longer be used, try `wpautop` instead
     *
     * @see wpautop
     *
     * @link http://codex.wordpress.org/Function_Reference/wpautop Function reference for wpautop
     *
     * @uses h
     *
     * @param  string $str A string containing a number of lines separated with line breaks
     * @return string      String with lines separated with `<p>` and `</p>` tags
     */
    function autop($str)
    {
        $str = nl2br(h($str));

        // Normalise
        $str = str_replace(array('<br>', '<br/>'), '<br />', $str);

        $lines = explode('<br />', $str);

        $output = '';

        foreach ($lines as $line) {
            $output .= '<p>' . $line . '</p>';
        }

        return $output;
    }
}

if (!function_exists('trimchars')) {
    /**
     * Trims a string and adds ellipsis to the end.
     *
     * Trims a string to a specified length and add an ellipsis to the end of the string.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @param  string $str
     * @param  int    $count Number of characters that should be shown before trimming and placing an ellipsis.  This defaults to 70 if not specified explicitly
     * @return string        String with an ellipsis at the end if it's longer than `$count`, otherwise it returns and untouched string
     */
    function trimchars($str, $count = 70)
    {
        $str = trim($str);

        if (strlen($str) > $count) {

            $str = substr($str, 0, $count);

            $str .= '&hellip;';
        }

        return $str;
    }
}

if (!function_exists('telephone_link')) {
    /**
     * Returns a phone number suitable for an anchor.
     *
     * This function strips all non-numeric characters from a phone number and returns a numeric-only string that can be placed in an anchor href for a `tel:` link.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @param  string $number A phone number containing numerals
     * @return string         A string with nothing but numerals, unless no numerals can be found then it just returns the phone number
     */
    function telephone_link($number)
    {
        $phone_number = '';

        preg_match_all('!\d+!', $number, $matches);

        if (!empty($matches[0]) && is_array($matches[0])) {
            $phone_number = implode('', $matches[0]);
        }

        return $phone_number;
    }
}

if (!function_exists('obfuscate_email')) {
    /**
     * Returns an obfuscated email address, optionally wrapped in an anchor.
     *
     * This function converts a plaintext email address into a ASCII-encoded version.  By default, this function just returns the obfuscated email address, you can optionally set the param `wrap` to `true` to have it wrapped in an anchor tag with a title.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @uses h
     *
     * @used-by obfuscate_email_shortcode
     *
     * @param  string $email_address The email address that requires encoding/obfuscating
     * @param  string $title         Optional title that's used when `$wrap` is true
     * @param  bool   $wrap          If true, function will return the email address wrapped in an `<a>` tag, otherwise it'll just return the obfuscated email address string
     * @return string                Returns either an obfuscated email address string, or a email address wrapped in an `<a>` tag if `$wrap` is true
     */
    function obfuscate_email($email_address, $title = '', $wrap = false)
    {
        $encoded = '';

        for ($i = 0; $i < strlen($email_address); $i++) {
            $encoded .= "&#" . ord($email_address[$i]) . ';';
        }

        if ($wrap) {
            return '<a href="mailto:' . $encoded . '"' . (($title != '') ? ' title="' . h($title) . '"' : '') . '>' . $encoded . '</a>';
        } else {
            return $encoded;
        }
    }
}

if (!function_exists('get_the_excerpt_max_chars')) {
    /**
     * Returns the post excerpt that has been trimmed by `max_chars` to a specified length.
     *
     * This function assumes you're using the default post excerpt.  **Note:** This function must be called while in The Loop!
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @uses max_chars
     * @uses get_the_excerpt
     *
     * @see http://codex.wordpress.org/Function_Reference/get_the_excerpt Function reference for get_the_excerpt
     * @see http://codex.wordpress.org/The_Loop Information about The Loop in WordPress
     *
     * @param  int    $max_length Maximum length of characters of the excerpt to show
     * @return string             The post excerpt, trimmed and with an ellipsis at the end if the character count is longer than `$max_length`
     */
    function get_the_excerpt_max_chars($max_length)
    {
        $excerpt = get_the_excerpt();

        return max_chars($excerpt, $max_length);
    }
}

if (!function_exists('max_chars')) {
    /**
     * Returns a string that's truncated by words and has an ellipsis at the end.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @used-by get_the_excerpt_max_chars
     *
     * @param  string $str        String that requires trimming if longerthan `$max_length`
     * @param  int    $max_length Maximum number of characters to show before trimming `$str` and putting an ellipsis at the end
     * @return string             String that depending on length, will either be the full `$str` string, or trimmed short with an ellipsis at the end
     */
    function max_chars($str, $max_length)
    {
        $max_length++;

        $output = '';

        if (mb_strlen($str) > $max_length) {
            $subex = mb_substr($str, 0, $max_length - 5);

            $exwords = explode(' ', $subex);

            $excut = - (mb_strlen($exwords[count($exwords) - 1]));

            if ($excut < 0) {
                $output .= mb_substr($subex, 0, $excut);
            } else {
                $output .= $subex;
            }

            $output .= '&hellip;';
        } else {
            $output .= $str;
        }

        return $output;
    }
}

if (!function_exists('format_acf_date')) {
    /**
     * Returns a formatted date string from an ACF row.
     *
     * **Note:** this assume you haven't changed the default return value from `Ymd` in ACF PRO.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @see http://www.advancedcustomfields.com/ ACF PRO WordPress plugin
     * @see http://php.net/manual/en/function.date.php date function for the return format
     *
     * @param  string|int  $date   Date from an ACF row
     * @param  string      $format Return the date in the specified format
     * @return bool|string         Returns the date in a string with the specified format, or false if the `$date` wasn't valid
     */
    function format_acf_date($date, $format = 'Ymd')
    {
        $output = '';

        if (!empty($date)) {
            $ts = mktime(0, 0, 0, substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4));

            $output = date($format, $ts);
        }

        return $output;
    }
}

if (!function_exists('timestamp_acf_date')) {
    /**
     * Returns a UNIX timestamp from the supplied date.
     *
     * **Note:** date must be in `Ymd` format, otherwise you will get incorrect results.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @see http://www.advancedcustomfields.com/ ACF PRO WordPress plugin
     *
     * @param  string   $date Date from an ACF row
     * @return int|void       The UNIX timestamp, or void if `$date` wasn't valid
     */
    function timestamp_acf_date($date)
    {
        if (!empty($date)) {
            return mktime(0, 0, 0, substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4));
        }
    }
}

if (!function_exists('get_attachment')) {
    /**
     * Returns a WordPress post in object format from the supplied ID.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @uses get_post
     *
     * @link http://codex.wordpress.org/Function_Reference/get_post Function reference for get_post
     *
     * @param  object|int $attachment_id ID of the attachment you wish to retrieve from the database
     * @return object                    Object with the database records for the specified attachment
     */
    function get_attachment($attachment_id)
    {
        return get_post($attachment_id);
    }
}

if (!function_exists('get_master_parent_page')) {
    /**
     * Returns the master parent page from the supplied page ID.
     *
     * This crawls up the inheritance tree to find the highest level parent of the supplied page ID.
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @uses get_post
     *
     * @link http://codex.wordpress.org/Function_Reference/get_post Function reference for get_post
     *
     * @param  object|int $page ID of the page you wish to find parents for
     * @return object           Object with the database records for the master parent page
     */
    function get_master_parent_page($page)
    {
        $master_parent_page = $page;

        while ($master_parent_page->post_parent) {
            $master_parent_page = get_post($master_parent_page->post_parent);
        }

        return $master_parent_page;
    }
}

if (!function_exists('get_resized_image')) {
    /**
     * Returns the URL of a requested resized image based on the ID
     *
     * @author Lee Karolczak <lee@humaan.com.au>
     *
     * @uses wp_get_attachment_image_src
     *
     * @param   int     $image_id   ID of the image attachment/post
     * @param   string  $size       Key of image size to return
     * @param   string  $default    URL of fallback image if no target image found
     * @return  string              Full URL to the resized image
     */
    function get_resized_image($image_id, $size, $default = '')
    {
        $image = $default;

        $array = wp_get_attachment_image_src($image_id, $size);

        if (!empty($array)) {
            if (isset($array[0]) && !empty($array[0])) {
                $image = $array[0];
            }
        }

        return $image;
    }
}

if (!function_exists('get_formatted_time')) {

    /**
     * Get a formatted time for event post types
     *
     * @param $fields
     * @return bool|string
     */
    function get_formatted_time($field)
    {

        $time_split = preg_split("/[\s:]+/", $field);
        $time = ltrim($time_split[0], '0');
        //if ($time_split[1] != '00') {
        $time .= ':' . $time_split[1];
        //}
        $time .= $time_split[2];

        return $time;
    }
}

if (!function_exists('get_formatted_datetime')) {

    /**
     * Get a formatted date for event post types
     * Assumes that start_date, end_date, start_time, end_time are available in array
     *
     * @param $fields
     * @return bool|string
     */
    function get_formatted_datetime($fields)
    {

        if (
            !(isset($fields['start_date']) &&
                !empty($fields['start_date']) &&
                isset($fields['end_date']) &&
                !empty($fields['end_date'])
            )
        ) {
            return '';
        }

        $datetime_formatted = date('D d M Y', strtotime($fields['start_date']));

        if ($fields['start_date'] != $fields['end_date']) {
            $datetime_formatted .= ' &#8212; ' . date('D d M Y', strtotime($fields['end_date']));
        }

        if (
            isset($fields['start_time']) &&
            !empty($fields['start_time']) &&
            isset($fields['end_time']) &&
            !empty($fields['end_time'])
        ) {

            $start_time = get_formatted_time($fields['start_time']);
            $end_time = get_formatted_time($fields['end_time']);

            $datetime_formatted .= ' &nbsp;|&nbsp; ' . $start_time . ' &#8212; ' . $end_time;
        }

        return $datetime_formatted;
    }
}
if (!function_exists('get_formatted_datetime_short')) {

    /**
     * Get a formatted date for event post types
     * Assumes that start_date, end_date, start_time, end_time are available in array
     *
     * @param $fields
     * @return bool|string
     */
    function get_formatted_datetime_short($fields)
    {

        if (
            !(isset($fields['start_date']) &&
                !empty($fields['start_date']) &&
                isset($fields['end_date']) &&
                !empty($fields['end_date'])
            )
        ) {
            return '';
        }

        $start_date = $fields['start_date'];
        if ($start_date != '') {
            $date = DateTime::createFromFormat('Ymd', $start_date);
            $start_date = $date->format('D d M');
        }

        $end_date = $fields['end_date'];
        if ($end_date != '') {
            $date = DateTime::createFromFormat('Ymd', $end_date);
            $end_date = $date->format('D d M');
        }



        return $start_date . " - " . $end_date;
    }
}

if (!function_exists('get_link_html')) {

    /**
     * Return a basic HTML link with checks for 'active' states
     *
     * @param $path
     * @param $label
     * @param string $title
     * @param bool|true $match_ancestors
     * @return string
     */
    function get_link_html($path, $label, $title = '', $match_ancestors = true, $classes = array())
    {

        global $post;

        if (empty($title)) {
            $title = $label;
        }

        $path           = ltrim($path, '/');
        $global_url     = rtrim($GLOBALS['site_url'], '/');

        // If is page, check if current page or ancestor of current page?
        if (is_singular('page')) {

            $post_ids[] = $post->ID;

            if ($match_ancestors) {
                $ancestor_ids = get_ancestors($post->ID, 'page');
                $post_ids = array_merge($post_ids, $ancestor_ids);
            }

            foreach ($post_ids as $post_id) {

                $check_url = get_the_permalink($post_id);

                $url_parts = parse_url($check_url);

                if ($path == ltrim($url_parts['path'], '/')) {

                    $classes[] = 'active';
                }
            }
        }

        // Add custom classes if required?
        $class = '';

        if (!empty($classes)) {

            $class = ' class="' . implode(' ', $classes) . '"';
        }

        $url = $path;

        // If path already contains the base URL?
        if (strpos($url, $global_url) !== false) {

            // Nothing
        } else {

            $url = $global_url . '/' . $path;
        }

        $html = '<a href="' . $url . '"' . $class . ' title="' . $title . '">' . $label . '</a>';

        return $html;
    }
}

if (!function_exists('format_acf_date')) {

    /**
     * Convert ACF date field Ymd to formatted date
     *
     * @param $date
     * @param string $format
     * @return bool|string
     */
    function format_acf_date($date, $format = 'Ymd')
    {

        $output = '';
        if ($date != '') {

            $ts = mktime(0, 0, 0, substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4));
            $output = date($format, $ts);
        }
        return $output;
    }
}

if (!function_exists('timestamp_acf_date')) {

    /**
     * Convert ACF date field Ymd to timestamp
     *
     * @param $date
     * @return int|null
     */
    function timestamp_acf_date($date)
    {

        $output = null;
        if ($date != '') {
            $output = mktime(0, 0, 0, substr($date, 4, 2), substr($date, 6, 2), substr($date, 0, 4));
        }
        return $output;
    }
}

if (!function_exists('get_search_filter_url')) {

    function get_search_filter_url($filter)
    {

        parse_str($_SERVER['QUERY_STRING'], $output);

        $output['filter'] = $filter;

        $query_string = '';

        foreach ($output as $key => $value) {

            $query_string .= '&' . $key . '=' . $value;
        }

        if (!empty($query_string)) {

            $query_string = ltrim($query_string, '&');
        }

        return add_query_arg($query_string, '', home_url());
    }
}

if (!function_exists('get_first_term')) {

    function get_first_term($post_id, $taxonomy = 'category')
    {

        $terms = get_the_terms($post_id, $taxonomy);

        if (isset($terms[0])) {

            return $terms[0]->name;
        }

        return false;
    }
}

if (!function_exists('_submit_sendgrid_email')) {
    /**
     * Send email via sendgrid
     *
     * @param $message
     * @return null|stdClass
     */
    function _submit_sendgrid_email($message)
    {

        $sendgrid_response = null;

        $sendgrid_api_key = get_field('sendgrid_api_key', 'option');

        $sendgrid = new SendGrid($sendgrid_api_key);

        try {
            $sendgrid_response = $sendgrid->send($message);
        } catch (SendGridException $e) {
            error_log($e->getMessage());
        }

        // error_log('SendGrid Response:');
        // error_log(print_r($sendgrid_response, true));

        return $sendgrid_response;
    }
}

if (!function_exists('validate_release_date')) {
    /**
     * Validate the course release date
     *
     */
    function validate_release_date($fields)
    {
        $current_timestamp = (int) current_time('timestamp', 1);
        $perth_time = $current_timestamp + (8 * 60 * 60);
        // Is release date set?
        if (isset($fields['release_date']) && !empty($fields['release_date'])) {

            // Is user logged in?
            if ($user_id = get_current_user_id() && !$fields['is_kids_course']) {

                $user_data = get_userdata(get_current_user_id());

                // Is the user a member?
                if (
                    in_array('administrator', $user_data->roles) ||
                    in_array('member_concession', $user_data->roles) ||
                    in_array('member_individual', $user_data->roles)
                ) {

                    // Set release date to acf field plus 9am
                    $timestamp = strtotime($fields['release_date'] . " 09:00:00");

                    // If is a member, release date is 1 week prior at 9am
                    if ($perth_time >= strtotime('-1 week', $timestamp)) {
                        return true;
                    }
                }
            }

            // If standard user, check if past release date at 9am.
            if ($perth_time >= strtotime($fields['release_date'] . " 09:00:00")) {

                return true;
            }
        }
        // If release date is not set, assume release date is valid
        else {

            return true;
        }

        return false;
    }
}

if (!function_exists('recursive_array_search')) {
    /**
     * Recursive array search that returns key
     *
     */
    function recursive_array_search($needle, $haystack)
    {

        foreach ($haystack as $key => $value) {

            $current_key = $key;

            if (
                $needle === $value
                || (is_array($value)
                    && recursive_array_search($needle, $value) !== false
                )
            ) {

                return $current_key;
            }
        }

        return false;
    }
}

if (!function_exists('theme_log')) {
    /**
     * Log the data to a specified file within the theme.
     *
     * @param string $log_filename
     * @param mixed ...$data
     */
    function theme_log($log_filename = 'log', ...$data)
    {

        if (empty($data)) {

            return;
        }

        $file = get_template_directory() . '/logs/' . $log_filename . '.' . date('Ymd') . '.log';

        $log_file = fopen($file, 'a');

        $content = '[' . date('Y-m-d H:i:s e') . '] ';

        $current_user = wp_get_current_user();

        if ($current_user->exists()) {

            $content .= '[' . $current_user->user_login . '] ';
        }

        foreach ($data as $data_item) {

            if (is_array($data) || is_object($data_item)) {

                $content .= var_export($data_item, true) . "\n";
            } else {

                $content .= $data_item . "\n";
            }
        }

        fwrite($log_file, $content);

        fclose($log_file);
    }
}

if (!function_exists('render')) {
    /**
     *
     * @param string $path
     * @param array $arguments
     * @param bool $echo
     */
    function render(string $path, array $vars = array(), bool $echo = true)
    {
        if (!empty($vars)) {
            extract($vars);
        }

        if ($echo) {
            if (file_exists(locate_template($path))) {
                include locate_template($path);
            }
        } else {
            ob_start();
            include locate_template($path);
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
    }
}

function get_discount_price($price)
{
    $membership_discount = floatval($GLOBALS['theme_options']['membership_discount']);
    $discount_rate = $membership_discount / 100;

    return $price - ($price * $discount_rate);
}



if (!function_exists('tag_attributes')) {
    /**
     * Takes an array of parameters and returns a string of HTML tag attributes.
     * @param array $arguments
     * @return string
     */
    function tag_attributes(array $arguments)
    {
        $attributes = [];

        foreach ($arguments as $key => $value) {
            if (!is_null($value)) {
                $attributes[] = sprintf(
                    '%s="%s"',
                    $key,
                    htmlspecialchars($value, (ENT_COMPAT | ENT_HTML5), 'UTF-8', false)
                );
            }
        }

        return implode(' ', $attributes);
    }
}

if (!function_exists('getDatePickerDates')) {
    function getDatePickerDates()
    {

        $dates = [];

        if (is_home()) {
            $custom_query = new WP_Query([
                'posts_per_page'   => -1,
                'post_type' => 'post',
            ]);


            while ($custom_query->have_posts()) {
                $custom_query->the_post();
                $dates[] = get_the_date('Y-m-d');
            }
            // Restore original Post Data.
            wp_reset_postdata();
        } elseif (is_page_template('template-whats-on.php')) {

            $custom_query = new WP_Query([
                'posts_per_page'   => -1,
                'post_type' => 'whatson',
            ]);

            while ($custom_query->have_posts()) {
                $custom_query->the_post();
                $dates[] = date("Y-m-d", strtotime(get_field('start_date')));
            }
            // Restore original Post Data.
            wp_reset_postdata();
        } elseif (is_page_template('template-courses.php')) {

            $custom_query = new WP_Query([
                'posts_per_page'   => -1,
                'post_type' => 'product',
                'post_status' => 'publish',
                'tax_query' => [
                    [
                        'taxonomy' => 'product_type',
                        'field'    => 'slug',
                        'terms'    => 'course'
                    ]
                ],
            ]);

            while ($custom_query->have_posts()) {
                $custom_query->the_post();
                $dates[] = date("Y-m-d", strtotime(get_field('start_date')));
            }
            // Restore original Post Data.
            wp_reset_postdata();
        }

        return $dates;
    }
}
