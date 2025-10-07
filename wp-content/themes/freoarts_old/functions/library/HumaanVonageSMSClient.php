<?php

/**
 * Class HumaanVonageSMSClient
 *
 * @package \\${NAMESPACE}
 */
class HumaanVonageSMSClient implements HumaanSMSClient
{
    /**
     * @var HumaanVonageSMSClient
     */
    private static $instance;


    /**
     * @var \HumaanVonageAPI
     */
    private $api;

    /**
     * Only allow singleton
     *
     * HumaanVonageSMSClient constructor.
     */
    protected function __construct()
    {
        $this->api = new HumaanVonageAPI();
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new HumaanVonageSMSClient;
        }

        return static::$instance;
    }

    public function e164FormatNumber($number)
    {
        // If no +61, add +61 to $to string
        return preg_replace("/^04/", '+614', $number);
    }

    public function sendSMS($to, $from_name, $content)
    {
        $to_formatted = $this->e164FormatNumber($to);
        return $this->api->sendSMS($to_formatted, $from_name, $content);
    }
}