<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

function send_SMS($to, $text)
{

    $client = new Client();

    $response['status'] = 'ERROR';

    // Ensure there is content
    if (empty($to) || empty($text)) {

        return $response;
    }

    if (preg_match("/^(?:\+61 ?|0)4[0-9]{2} ?[0-9]{3} ?[0-9]{3}$/", $to) === false) {

        return $response;
    }

    // If no +61, add +61 to $to string
    $to = preg_replace("/^04/", '+614', $to);

    $api_key = get_field('sms_api_key', 'option');
    $api_secret = get_field('sms_api_secret', 'option');

    try {

        $args = array(
            'api_key' => $api_key,
            'api_secret' => $api_secret,
            'to' => $to,
            'from' => 'FreoArtsCentre',
            'type' => 'text',
            'text' => $text
        );

        $query = http_build_query($args);

        $url = 'https://rest.nexmo.com/sms/json?' . $query;

        $sms_response = $client->post($url);

        if ($sms_response->getStatusCode() == 200) {

            $response['status'] = 'OK';
        }
    }
    catch (ClientException $e) {

        if ($e->hasResponse()) {

            error_log(print_r('Error', true));
            error_log(print_r($e->getRequest(), true));
            error_log(print_r($e->getResponse(), true));
        }
    }

    return $response;
}