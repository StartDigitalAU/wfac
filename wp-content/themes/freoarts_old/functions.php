<?php

/****************************************************
 *
 * INCLUDES
 *
 ****************************************************/

// Bring in Composer
include_once(TEMPLATEPATH . '/library/vendor/autoload.php');

// Classes
include_once(TEMPLATEPATH . '/functions/HumaanEmail.php');
include_once(TEMPLATEPATH . '/functions/library/HumaanListTable.php');
include_once(TEMPLATEPATH . '/functions/library/HumaanTableFactory.php');
include_once(TEMPLATEPATH . '/functions/library/HumaanEmailScheduler.php');
include_once(TEMPLATEPATH . '/functions/EnrolmentsFactory.php');
include_once(TEMPLATEPATH . '/functions/ContactMessagesFactory.php');
include_once(TEMPLATEPATH . '/functions/ArchivedCoursesFactory.php');

//Print awards submissions
include_once(TEMPLATEPATH . '/functions/print-awards.php');
include_once(TEMPLATEPATH . '/functions/PrintAwards.php');
include_once(TEMPLATEPATH . '/functions/PrintAwardsFactory.php');

// Overrides
include_once(TEMPLATEPATH . '/functions/overrides.php');

// Logging $_POST data
include_once(TEMPLATEPATH . '/functions/post-logging.php');

// General config
include_once(TEMPLATEPATH . '/functions/general.php');

// Helpers
include_once(TEMPLATEPATH . '/functions/helpers.php');

// Users
include_once(TEMPLATEPATH . '/functions/users.php');

// Scripts
include_once(TEMPLATEPATH . '/functions/scripts.php');

// ACF
include_once(TEMPLATEPATH . '/functions/acf.php');

// Navigation
include_once(TEMPLATEPATH . '/functions/navigation.php');

// Shortcodes
include_once(TEMPLATEPATH . '/functions/shortcodes.php');

// Post types
include_once(TEMPLATEPATH . '/functions/post-types.php');

// Taxonomies
include_once(TEMPLATEPATH . '/functions/taxonomies.php');

// Post thumbnails
include_once(TEMPLATEPATH . '/functions/post-thumbnails.php');

// User roles
include_once(TEMPLATEPATH . '/functions/roles.php');

// Social
// include_once( TEMPLATEPATH . '/functions/social.php' );

// Calendar
include_once(TEMPLATEPATH . '/functions/calendar.php');

// Popular posts
include_once(TEMPLATEPATH . '/functions/popular-posts.php');

// Contact messages & form
include_once(TEMPLATEPATH . '/functions/contact-messages.php');

// Course Management
include_once(TEMPLATEPATH . '/functions/course-management.php');

// Reports
include_once(TEMPLATEPATH . '/functions/reports.php');

// Enrolment Wizard
include_once(TEMPLATEPATH . '/functions/enrolment-wizard.php');

// Archived courses
include_once(TEMPLATEPATH . '/functions/archived-courses.php');

// SMS
include_once(TEMPLATEPATH . '/functions/sms.php');

// Search
include_once(TEMPLATEPATH . '/functions/search.php');

include_once TEMPLATEPATH . '/functions/editor.php'; // Custom TinyMCE Formats

// Woocommerce
include_once(TEMPLATEPATH . '/functions/woocommerce.php');
include_once(TEMPLATEPATH . '/functions/woocommerce-my-account.php');
include_once(TEMPLATEPATH . '/functions/woocommerce-product-types.php');
include_once(TEMPLATEPATH . '/functions/woocommerce-membership.php');
include_once(TEMPLATEPATH . '/functions/woocommerce-courses.php');
include_once(TEMPLATEPATH . '/functions/woocommerce-payment-gateway.php');
include_once(TEMPLATEPATH . '/functions/covid.php');
include_once(TEMPLATEPATH . '/functions/caching.php');


include_once(TEMPLATEPATH . '/functions/components/feeds/feeds.php');
$Feed = new Humaan\Feed(['Instagram']);

include_once(TEMPLATEPATH . '/functions/library/HumaanVonageAPI.php');
include_once(TEMPLATEPATH . '/functions/library/HumaanSMSClient.php');
include_once(TEMPLATEPATH . '/functions/library/HumaanVonageSMSClient.php');
include_once(TEMPLATEPATH . '/functions/library/HumaanMockedSMSClient.php');
include_once(TEMPLATEPATH . '/functions/library/HumaanSMSScheduler.php');

include_once(TEMPLATEPATH . '/vendor/google/recaptcha/src/autoload.php'); // reCapthca

include_once(TEMPLATEPATH . '/functions/image-sizes.php'); // 2023 image sizes

include_once(TEMPLATEPATH . '/functions/apis/courses.php');
include_once(TEMPLATEPATH . '/functions/apis/whats-on.php');
include_once(TEMPLATEPATH . '/functions/apis/news.php');

include_once(TEMPLATEPATH . '/functions/confirmation-tutor-email.php');
include_once(TEMPLATEPATH . '/functions/course-low-registeration-email.php');
include_once(TEMPLATEPATH . '/functions/admin/course-ordering.php');
include_once(TEMPLATEPATH . '/functions/admin/preview-courses.php');
include_once(TEMPLATEPATH . '/functions/EmailSchedulerCron.php');


// write a function to display the page template for pages in admin columns
function humaan_page_template_column($columns)
{
    $columns['page_template'] = 'Page Template';
    // populate column with page template name
    add_action('manage_pages_custom_column', function ($column, $post_id) {
        if ('page_template' === $column) {
            $page_template = get_page_template_slug($post_id);
            if ($page_template) {
                $page_template = str_replace('.php', '', $page_template);
                $page_template = str_replace('page-', '', $page_template);
                $page_template = ucwords(str_replace('-', ' ', $page_template));
                echo $page_template;
            } else {
                echo 'Default';
            }
        }
    }, 10, 2);
    return $columns;
}
add_filter('manage_pages_columns', 'humaan_page_template_column');

add_filter('upload_dir', function ($dirs) {
    if ($_SERVER['HTTP_HOST'] === 'wfac.test') {
        // point both the base URL and the per‐size URLs
        $dirs['baseurl'] = 'https://wfac.org.au/wp-content/uploads';
        $dirs['url']     = $dirs['baseurl'] . $dirs['subdir'];
    }
    return $dirs;
});