<?php

namespace Humaan;

if (!class_exists('InstagramBasicDisplay')) {
    class InstagramBasicDisplay
    {
        const API_URL = 'https://graph.instagram.com/';

        const API_OAUTH_URL = 'https://api.instagram.com/oauth/authorize';

        const API_OAUTH_TOKEN_URL = 'https://api.instagram.com/oauth/access_token';
    
        const API_TOKEN_EXCHANGE_URL = 'https://graph.instagram.com/access_token';
    
        const API_TOKEN_REFRESH_URL = 'https://graph.instagram.com/refresh_access_token';

        private $appId;

        private $appSecret;

        private $redirectUri;

        private $accesstoken;

        private $scopes = ['user_profile', 'user_media'];

        private $userFields = 'account_type, id, media_count, username';

        private $mediaFields = 'caption, id, media_type, media_url, permalink, thumbnail_url, timestamp, username, children{id, media_type, media_url, permalink, thumbnail_url, timestamp, username}';

        private $mediaChildrenFields = 'id, media_type, media_url, permalink, thumbnail_url, timestamp, username';

        private $timeout = 90000;

        private $connectTimeout = 20000;

        public function __construct($config = null)
        {
            if (is_array($config)) {
                $this->setAppId($config['appId']);
                $this->setAppSecret($config['appSecret']);
                $this->setRedirectUri($config['redirectUri']);
            
                if (isset($config['timeout'])) {
                    $this->setTimeout($config['timeout']);
                }
            
                if (isset($config['connectTimeout'])) {
                    $this->setConnectTimeout($config['connectTimeout']);
                }
            } elseif (is_string($config)) {
                // For read-only
                $this->setAccessToken($config);
            } else {
                throw new \Exception('Error: __construct() - Configuration data is missing.');
            }
        }

        public function getLoginUrl($scopes = ['user_profile', 'user_media'], $state = '')
        {
            if (is_array($scopes) && count(array_intersect($scopes, $this->scopes)) === count($scopes)) {
                return self::API_OAUTH_URL . '?client_id=' . $this->getAppId() . '&redirect_uri=' . urlencode($this->getRedirectUri()) . '&scope=' . implode(
                ',',
                $scopes
            ) . '&response_type=code' . ($state != '' ? '&state=' . $state : '');
            }

            throw new \Exception("Error: getLoginUrl() - The parameter isn't an array or invalid scope permissions used.");
        }

        public function getUserProfile($id = 0)
        {
            if ($id === 0) {
                $id = 'me';
            }

            return $this->makeCall($id, ['fields' => $this->userFields]);
        }

        public function getUserMedia($id = 'me', $limit = 0, $before = null, $after = null)
        {
            $params = [
            'fields' => $this->mediaFields
        ];

            if ($limit > 0) {
                $params['limit'] = $limit;
            }
            if (isset($before)) {
                $params['before'] = $before;
            }
            if (isset($after)) {
                $params['after'] = $after;
            }

            return $this->makeCall($id . '/media', $params);
        }

        public function getMedia($id)
        {
            return $this->makeCall($id, ['fields' => $this->mediaFields]);
        }

        public function getMediaChildren($id)
        {
            return $this->makeCall($id . '/children', ['fields' => $this->mediaChildrenFields]);
        }

        public function getOAuthToken($code, $tokenOnly = false)
        {
            $apiData = array(
            'app_id' => $this->getAppId(),
            'app_secret' => $this->getAppSecret(),
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->getRedirectUri(),
            'code' => $code
        );

            $result = $this->makeOAuthCall(self::API_OAUTH_TOKEN_URL, $apiData);

            return !$tokenOnly ? $result : $result->access_token;
        }

        public function getLongLivedToken($token, $tokenOnly = false)
        {
            $apiData = array(
            'client_secret' => $this->getAppSecret(),
            'grant_type' => 'ig_exchange_token',
            'access_token' => $token
        );

            $result = $this->makeOAuthCall(self::API_TOKEN_EXCHANGE_URL, $apiData, 'GET');

            return !$tokenOnly ? $result : $result->access_token;
        }

        public function refreshToken($token, $tokenOnly = false)
        {
            $apiData = array(
            'grant_type' => 'ig_refresh_token',
            'access_token' => $token
        );

            $result = $this->makeOAuthCall(self::API_TOKEN_REFRESH_URL, $apiData, 'GET');

            return !$tokenOnly ? $result : $result->access_token;
        }

        protected function makeCall($function, $params = null, $method = 'GET')
        {
            if (!isset($this->accesstoken)) {
                throw new \Exception("Error: makeCall() | $function - This method requires an authenticated users access token.");
            }

            $authMethod = '?access_token=' . $this->getAccessToken();

            $paramString = null;

            if (isset($params) && is_array($params)) {
                $paramString = '&' . http_build_query($params);
            }

            $apiCall = self::API_URL . $function . $authMethod . (('GET' === $method) ? $paramString : null);

            $headerData = array('Accept: application/json');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiCall);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headerData);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->connectTimeout);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, true);

            $jsonData = curl_exec($ch);

            if (!$jsonData) {
                throw new \Exception('Error: makeCall() - cURL error: ' . curl_error($ch), curl_errno($ch));
            }

            list($headerContent, $jsonData) = explode("\r\n\r\n", $jsonData, 2);

            curl_close($ch);

            return json_decode($jsonData);
        }

        private function makeOAuthCall($apiHost, $params, $method = 'POST')
        {
            $paramString = null;

            if (isset($params) && is_array($params)) {
                $paramString = '?' . http_build_query($params);
            }

            $apiCall = $apiHost . (('GET' === $method) ? $paramString : null);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiCall);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, $this->timeout);
        
            if ($method === 'POST') {
                curl_setopt($ch, CURLOPT_POST, count($params));
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            }

            $jsonData = curl_exec($ch);

            if (!$jsonData) {
                throw new \Exception('Error: makeOAuthCall() - cURL error: ' . curl_error($ch));
            }

            curl_close($ch);

            return json_decode($jsonData);
        }

        public function setAccessToken($token)
        {
            $this->accesstoken = $token;
        }

        public function getAccessToken()
        {
            return $this->accesstoken;
        }

        public function setAppId($appId)
        {
            $this->appId = $appId;
        }

        public function getAppId()
        {
            return $this->appId;
        }

        public function setAppSecret($appSecret)
        {
            $this->appSecret = $appSecret;
        }

        public function getAppSecret()
        {
            return $this->appSecret;
        }

        public function setRedirectUri($redirectUri)
        {
            $this->redirectUri = $redirectUri;
        }

        public function getRedirectUri()
        {
            return $this->redirectUri;
        }

        public function setUserFields($fields)
        {
            $this->userFields = $fields;
        }

        public function setMediaFields($fields)
        {
            $this->mediaFields = $fields;
        }

        public function setMediaChildrenFields($fields)
        {
            $this->mediaChildrenFields = $fields;
        }

        public function setTimeout($timeout)
        {
            $this->timeout = $timeout;
        }

        public function setConnectTimeout($connectTimeout)
        {
            $this->connectTimeout = $connectTimeout;
        }
    }
}