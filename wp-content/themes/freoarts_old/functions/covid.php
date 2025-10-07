<?php

require_once(TEMPLATEPATH . '/library/vendor/campaignmonitor/createsend-php/csrest_subscribers.php');

class CovidRegister
{
    private $table_name = 'covid_register';

    private $campaign_monitor_api_key = '612219a1f7ffe03e1dc1837d7499f522';

    private $campaign_monitor_list_id = '45111ca2d7b8324c5298970774645d26';

    public function __construct()
    {
        add_action('wp_ajax_nopriv_covid-parse-form-submission', [$this, 'ajaxParseFormSubmission']);
        add_action('wp_ajax_covid-parse-form-submission', [$this, 'ajaxParseFormSubmission']);

        add_action('admin_menu', [$this, 'addMenuPages']);

        add_action('admin_post_export_covid_register.csv', [$this, 'exportCovidRegisterSubmissionsAsCSV']);
    }

    public function addMenuPages()
    {
        add_menu_page(
            'Covid Register',
            'Covid Register',
            'manage_options',
            'covid-register-main',
            [$this, 'viewCovidRegisterSubmissions'],
            'dashicons-heart'
        );
    }

    public function ajaxParseFormSubmission()
    {
        global $wpdb;

        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email_address = filter_input(INPUT_POST, 'email_address', FILTER_SANITIZE_EMAIL);
        $contact_number = filter_input(INPUT_POST, 'contact_number', FILTER_SANITIZE_STRING);
        $number_in_group = isset($_POST['number_in_group']) ? (int) $_POST['number_in_group'] : 1;
        $join_mailing_list = isset($_POST['join_mailing_list']) ? (int) $_POST['join_mailing_list'] : 0;

        if (empty($name)
            || empty($contact_number)
            || empty($number_in_group)
        ) {
            wp_send_json([
                'status' => 'ERROR',
                'message' => 'Please ensure all fields are submitted.'
            ]);
            wp_die();
        }

        $wpdb->insert(
            $wpdb->prefix . $this->table_name,
            array(
                'name' => $name,
                'email_address' => $email_address,
                'contact_number' => $contact_number,
                'number_in_group' => $number_in_group,
                'join_mailing_list' => $join_mailing_list,
                'created_at' => current_time('Y-m-d H:i:s')
            )
        );

        if ($join_mailing_list) {
            $CS_REST_Subscribers = new CS_REST_Subscribers(
                $this->campaign_monitor_list_id,
                [
                    'api_key' => $this->campaign_monitor_api_key
                ]
            );

            $result = $CS_REST_Subscribers->add(
                [
                    'Name'         => $name,
                    'EmailAddress' => $email_address,
                    'CustomFields' => [
                        [
                            'Key'   => 'Contact Number',
                            'Value' => $contact_number
                        ],
                        [
                            'Key'   => 'Number in Group',
                            'Value' => $number_in_group
                        ]
                    ]
                ]
            );
        }

        wp_send_json([
            'status' => 'SUCCESS',
            'message' => 'Submission successful.'
        ]);
        wp_die();
    }

    public function viewCovidRegisterSubmissions()
    {
        global $wpdb;

        $results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . $this->table_name);
        ?>
        <div class="wrap">
            <h1>Covid Register</h1>
            <?php if (!empty($results)) : ?>
                <form id="posts-filter" action="" method="get">
                    <input type="hidden" name="page" value="stock"/>
                    <div class="tablenav top">
                        <div class="alignright actions">
                            <a href="<?php echo admin_url('admin-post.php?action=export_covid_register.csv'); ?>" class="button btn-csv">Export CSV</a>
                        </div>
                    </div>
                    <div class="acf-field acf-field-repeater">
                        <div class="acf-input">
                            <table class="acf-table stock">
                                <thead>
                                    <tr>
                                        <th class="acf-th acf-th-text">Name</th>
                                        <th class="acf-th acf-th-text">Email Address</th>
                                        <th class="acf-th acf-th-text">Contact Number</th>
                                        <th class="acf-th acf-th-text">Number in Group</th>
                                        <th class="acf-th acf-th-text">Created At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($results as $result) {
                                        ?>
                                        <tr>
                                            <td><?php echo $result->name; ?></td>
                                            <td><?php echo $result->email_address; ?></td>
                                            <td><?php echo $result->contact_number; ?></td>
                                            <td><?php echo $result->number_in_group; ?></td>
                                            <td><?php echo $result->created_at; ?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            <?php endif; ?>
        </div>
        <?php
    }

    public function exportCovidRegisterSubmissionsAsCSV()
    {
        global $wpdb;

        $filename = 'covid-register-submissions.csv';
        header('Content-Type: application/csv');
        header("Content-Disposition: attachment; filename=$filename");
        header('Pragma: no-cache');
        $output = fopen('php://output', 'w');
        fputcsv(
            $output,
            [
                'Name',
                'Email Address',
                'Contact Number',
                'Number in Group',
                'Created At'
            ]
        );

        $results = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . $this->table_name);

        if (!empty($results)) {
            foreach ($results as $result) {
                fputcsv(
                    $output,
                    [
                        $result->name,
                        $result->email_address,
                        $result->contact_number,
                        $result->number_in_group,
                        $result->created_at
                    ]
                );
            }
        }
    }
}

$CovidRegister = new CovidRegister();
