<?php

class HumaanSMSScheduler
{

    public $from_name = 'FreoArts';

    private $wpdb;

    private $wp_table_name_templates;

    private $wp_table_name_recipients;

    private $table_name_templates = 'sms_scheduler_templates';

    private $table_name_recipients = 'sms_scheduler_recipients';

    private $send_limit = 10;

    /**
     * @var HumaanSMSClient
     */
    private $sms_client;

    public function __construct($sms_client)
    {

        global $wpdb;

        $this->wpdb                         = $wpdb;
        $this->wp_table_name_templates      = $wpdb->prefix . $this->table_name_templates;
        $this->wp_table_name_recipients     = $wpdb->prefix . $this->table_name_recipients;

        $this->sms_client = $sms_client;
        $this->init();
    }

    public function init()
    {

        // $this->initDB();
    }

    /**
     * Install database tables if required
     *
     */
    private function initDB()
    {

        if (
            $this->wpdb->get_var("SHOW TABLES LIKE '{$this->wp_table_name_templates}'") != $this->wp_table_name_templates &&
            $this->wpdb->get_var("SHOW TABLES LIKE '{$this->wp_table_name_recipients}'") != $this->wp_table_name_recipients
        ) {

            $sql = "CREATE TABLE {$this->wp_table_name_templates} (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  course_id bigint(20) unsigned default NULL,
                  content text NOT NULL default '',
                  created_at datetime NOT NULL default '0000-00-00 00:00:00',
                  PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            $this->wpdb->query($sql);

            $sql = "CREATE TABLE {$this->wp_table_name_recipients} (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  template_id bigint(20) unsigned NOT NULL,
                  mobile_number varchar(128) NOT NULL default '',
                  full_name varchar(128) NOT NULL default '',
                  status varchar(128) NOT NULL default 'pending',
                  created_at datetime NOT NULL default '0000-00-00 00:00:00',
                  scheduled_for datetime NOT NULL default '0000-00-00 00:00:00',
                  sent_at datetime default NULL,
                  PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            $this->wpdb->query($sql);
        }
    }

    /**
     * Add SMS template
     *
     * @param array $args
     * @return mixed
     */
    public function addTemplate($args = array())
    {

        $args['created_at'] = date('Y-m-d H:i:s');

        $result = $this->wpdb->insert(
            $this->wp_table_name_templates,
            $args
        );

        if (!empty($result)) {

            return $this->wpdb->insert_id;
        }

        return false;
    }

    /**
     * Add SMS recipient
     *
     * @param array $args
     * @return bool
     */
    public function addRecipient($args = array())
    {

        $args['created_at'] = date('Y-m-d H:i:s');

        if (!(isset($args['scheduled_for']) && !empty($args['scheduled_for']))) {
            $args['scheduled_for'] = date('Y-m-d H:i:s');
        }

        $result = $this->wpdb->insert(
            $this->wp_table_name_recipients,
            $args
        );

        if (!empty($result)) {

            return $this->wpdb->insert_id;
        }

        return false;
    }

    /**
     * Loop through scheduled SMSs
     *
     */
    // TODO: Setup cron task to run the sendEmails method
    public function sendAllScheduledSMS()
    {

        $sql = "SELECT
            r.id,
            r.mobile_number,
            t.content

            FROM {$this->wp_table_name_recipients} AS r

            LEFT JOIN {$this->wp_table_name_templates} AS t
            ON t.id = r.template_id

            WHERE r.status = 'pending'
            AND DATE(r.scheduled_for) <= DATE(NOW())

            ORDER BY r.created_at ASC
            LIMIT {$this->send_limit}";

        $recipients = $this->wpdb->get_results($sql, 'ARRAY_A');

        if (!empty($recipients)) {

            foreach ($recipients as $recipient) {

                $this->sendSMS($recipient);
            }
        }
    }

    /**
     * Send SMS
     *
     * @param $recipient
     */
    public function sendSMS($recipient)
    {
        $status = 'error';

        $sms_response = $this->sms_client->sendSMS($recipient['mobile_number'], $this->from_name, $recipient['content']);

        if ($sms_response['status'] === 'OK') {
            $status = 'sent';
        } else {
            error_log("Error sending sms to " . $recipient['mobile_number']);
            error_log(print_r($sms_response, true));
        }

        // Update database entry to 'sent'
        $this->wpdb->update(
            $this->wp_table_name_recipients,
            array(
                'status' => $status,
                'sent_at' => date('Y-m-d H:i:s')
            ),
            array(
                'id' => $recipient['id']
            )
        );
    }
}