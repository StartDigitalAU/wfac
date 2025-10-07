<?php

/****************************************************
 *
 * POST LOGGING
 *
 ****************************************************/

add_action('init', 'humaan_process_post');

function humaan_process_post()
{
    // Early exit because there's no $_POST data
    if (empty($_POST) || !is_array($_POST)) {
        return;
    }

    // Ensure we're not hooking into $_POST data for login page
    if (function_exists('is_login_page') && is_login_page()) {
        return;
    }

    // Ensure we're not hooking into $_POST data for admin pages
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    $postData = $_POST;

    // These keys in $_POST data should be stripped out
    $stripKeys = [
        'password',
        'pwd',
    ];

    // Remove sensitive parameters from $_POST data
    foreach ($stripKeys as $stripKey) {
        if (isset($postData[$stripKey])) {
            unset($postData[$stripKey]);
        }
    }

    // Create a string log entry
    $log_entry = sprintf(
        "[%s] %s\n",
        date('Y-m-d H:i:s'),
        json_encode($postData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
    );

    // Get the file system path to the logging directory
    $log_dir = dirname(__FILE__) . '/logs';

    if (!file_exists($log_dir)) {
        if (!mkdir($log_dir, 0755, true) && !is_dir($log_dir)) {
            error_log('Failed to create log directory: ' . $log_dir);
            return;
        }
    }

    $log_file = $log_dir . '/post-log-' . date('Y-m-d') . '.log';

    if (($handle = @fopen($log_file, 'a')) !== false) {
        fwrite($handle, $log_entry);
        fclose($handle);
    } else {
        error_log('Failed to open log file: ' . $log_file);
    }
}
