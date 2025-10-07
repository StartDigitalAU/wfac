<?php

/**
 * Class HumaanVonageSMSClient
 *
 * @package \\${NAMESPACE}
 */
class HumaanMockedSMSClient implements HumaanSMSClient
{
    /**
     * @var HumaanVonageSMSClient
     */
    private static $instance;

    /**
     * Only allow singleton
     *
     * HumaanVonageSMSClient constructor.
     */
    protected function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new HumaanMockedSMSClient;
        }

        return static::$instance;
    }

    public function sendSMS($to, $from_name, $content)
    {
        sleep(1);
        return [
            'status' => 'OK',
            'status_code' => '200',
            'data' => [
                // doesn't really matter
            ],
        ];
    }
}