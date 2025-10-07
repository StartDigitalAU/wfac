<?php

/**
 * Interface HumaanSMSClient
 *
 */
interface HumaanSMSClient
{
    public function sendSMS($to, $from_name, $content);
}