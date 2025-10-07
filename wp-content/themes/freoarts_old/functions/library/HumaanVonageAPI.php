<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;
use GuzzleHttp\Exception\RequestException as GuzzleRequestException;

/**
 * Class HumaanVonageAPI
 *
 * @package \\${NAMESPACE}
 */
class HumaanVonageAPI
{
    private $api_key;
    private $api_secret;

    /**
     * @var GuzzleClient
     */
    private $client;
    public $base_url = 'https://rest.nexmo.com';
    public $sms_endpoint = 'sms/json';
    public $account_balance_endpoint = 'account/get-balance';

    public function __construct()
    {
        $this->api_key = get_field('sms_api_key', 'option');
        $this->api_secret = get_field('sms_api_secret', 'option');
        $this->client = new GuzzleClient();
    }

    public function post($url)
    {
        $response = [
            'status' => 'ERROR'
        ];

        try {
            $post_response = $this->client->post($url);
            $response['status'] = 'OK';
            $response['status_code'] = $post_response->getStatusCode();
            $data = json_decode($post_response->getBody()->getContents(), true);
            if (isset($data['messages']) && count($data['messages'])) {
                foreach ($data['messages'] as $message) {
                    if (!isset($message['status']) || intval($message['status']) !== 0) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $message['error-text'];
                        break;
                    }
                }
            }
        } catch (GuzzleClientException $e) {
            if ($e->hasResponse()) {
                error_log(print_r('Error', true));
                error_log(print_r($e->getRequest(), true));
                error_log(print_r($e->getResponse(), true));
            }
            $response['message'] = $e->getMessage();
            error_log($e->getMessage());
        }

        return $response;
    }

    public function get($url)
    {
        $response = [
            'status' => 'ERROR'
        ];

        try {
            $post_response = $this->client->get($url);
            $response['status'] = 'OK';
            $response['status_code'] = $post_response->getStatusCode();
            $data = json_decode($post_response->getBody()->getContents(), true);
            if (isset($data['messages']) && count($data['messages'])) {
                foreach ($data['messages'] as $message) {
                    if (!isset($message['status']) || intval($message['status']) !== 0) {
                        $response['status'] = 'ERROR';
                        $response['message'] = $message['error-text'];
                        break;
                    }
                }
            }
        } catch (GuzzleClientException $e) {
            if ($e->hasResponse()) {
                error_log(print_r('Error', true));
                error_log(print_r($e->getRequest(), true));
                error_log(print_r($e->getResponse(), true));
            }
            $response['message'] = $e->getMessage();
            error_log($e->getMessage());
        }

        return $response;
    }

    public function buildURL($endpoint, $args = [])
    {
        $defaults = [
            'api_key' => $this->api_key,
            'api_secret' => $this->api_secret,
        ];

        $query = http_build_query(array_merge($defaults, $args));
        $url = join('/', [$this->base_url, $endpoint]);
        $url = "$url?$query";

        return $url;
    }

    public function getAccountBalance()
    {
        $url = $this->buildURL($this->account_balance_endpoint);
        $response = $this->get($url);
        return $response['data'];
    }

    public function sendSMS($to, $from, $content)
    {
        $args = [
            'to' => $to,
            'from' => $from,
            'type' => 'text',
            'text' => $content
        ];

        $url = $this->buildURL($this->sms_endpoint, $args);
        $response = $this->post($url);
        return $response;
    }
}