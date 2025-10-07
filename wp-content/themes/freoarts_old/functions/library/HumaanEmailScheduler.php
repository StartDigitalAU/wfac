<?php

use Postmark\PostmarkClient;

class HumaanEmailScheduler
{

    public $from_name = 'Fremantle Arts Centre';

    private $wpdb;

    private $wp_table_name_templates;

    private $wp_table_name_recipients;

    private $table_name_templates = 'email_scheduler_templates';

    private $table_name_recipients = 'email_scheduler_recipients';

    private $send_limit = 10;

    private $postmark_client;

    public function __construct()
    {

        global $wpdb;

        $this->wpdb                         = $wpdb;
        $this->wp_table_name_templates      = $wpdb->prefix . $this->table_name_templates;
        $this->wp_table_name_recipients     = $wpdb->prefix . $this->table_name_recipients;

        $postmark_api_key = get_field('postmark_api_key', 'option');
        $this->postmark_client = new PostmarkClient($postmark_api_key);
    }

    public function init()
    {

        $this->initDB();
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
                  subject varchar(128) NOT NULL default '',
                  email_from varchar(128) NOT NULL default '',
                  content text NOT NULL default '',
                  created_at datetime NOT NULL default '0000-00-00 00:00:00',
                  PRIMARY KEY (id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

            $this->wpdb->query($sql);

            $sql = "CREATE TABLE {$this->wp_table_name_recipients} (
                  id bigint(20) unsigned NOT NULL auto_increment,
                  template_id bigint(20) unsigned NOT NULL,
                  email varchar(128) NOT NULL default '',
                  full_name varchar(128) NOT NULL default '',
                  content text NOT NULL default '',
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
     * Add email template
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
     * Add email recipient
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
     * Loop through scheduled emails
     *
     */
    // TODO: Setup cron task to run the sendEmails method
    public function sendEmails()
    {

        $sql = "SELECT
            r.id,
            r.email,
            t.subject,
            t.email_from,
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

                $this->sendEmail($recipient);
            }
        }
    }

    /**
     * Send email via SendGrid
     *
     * @param $recipient
     */
    public function sendEmail($recipient)
    {

        try {
            $send_result = $this->postmark_client->sendEmail(
                $recipient['email_from'],
                $recipient['email'], // to
                $this->from_name . ' - ' . $recipient['subject'],
                $recipient['content']
            );

            $status = 'sent';
        } catch (\Postmark\Models\PostmarkException $ex) {
            $status = 'error';
            error_log("Error sending contact enquiry email: " . $ex->message);
        } catch (Exception $ex) {
            $status = 'error';
            error_log($ex->getMessage());
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
