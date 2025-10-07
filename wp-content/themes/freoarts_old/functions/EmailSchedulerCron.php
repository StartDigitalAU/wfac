<?php

class HumaanEmailCron
{
    private static $instance = null;
    private $scheduler = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init()
    {
        add_filter('cron_schedules', [$this, 'addCronIntervals']);
        add_action('init', [$this, 'createEmailScheduler'], 1);
        add_action('humaan_send_emails', [$this, 'executeScheduledEmails']);
    }

    public function addCronIntervals($schedules)
    {
        $schedules['every_five_minutes'] = array(
            'interval' => 300,
            'display'  => 'Every 5 Minutes'
        );
        return $schedules;
    }

    public function createEmailScheduler()
    {
        $this->scheduler = new HumaanEmailScheduler();
        $this->scheduler->init();

        if (!wp_next_scheduled('humaan_send_emails')) {
            wp_schedule_event(time(), 'every_five_minutes', 'humaan_send_emails');
        }
    }

    public function executeScheduledEmails()
    {
        error_log('Humaan Email Cron: executeScheduledEmails() called at ' . date('Y-m-d H:i:s'));

        // Email Scheduler Send
        $scheduler = new HumaanEmailScheduler();
        $scheduler->sendEmails();

        // SMS Scheduler Send
        $smsClient = HumaanVonageSMSClient::getInstance();
        $sms_scheduler = new HumaanSMSScheduler($smsClient);
        $sms_scheduler->sendAllScheduledSMS();

        // Additional logging
        error_log('Humaan Email Cron: executeScheduledEmails() completed');
        error_log('Humaan SMS Sent through');
    }
}

HumaanEmailCron::getInstance()->init();
