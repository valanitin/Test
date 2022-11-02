<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

// phpcs:ignoreFile

namespace Plumrocket\SocialLoginFree\Model;

class Twitter extends Account
{
    /**
     * @var string
     */
    protected $_type = 'twitter';

    const URL_REQUEST_TOKEN = 'https://api.twitter.com/oauth/request_token';
    const URL_AUTHORIZE = 'https://api.twitter.com/oauth/authorize';
    const URL_ACCESS_TOKEN = 'https://api.twitter.com/oauth/access_token';
    const URL_ACCOUNT_DATA = 'https://api.twitter.com/1.1/account/verify_credentials.json';

    /**
     * @var array
     */
    protected $_responseType = ['oauth_token', 'oauth_verifier'];

    /**
     * @var array
     */
    protected $_fields = [
                    'user_id' => 'id',
                    'firstname' => 'name',
                    'lastname' => 'name2',
                    'email' => 'email',
                    'dob' => null,
                    'gender' => null,
                    'photo' => 'profile_image_url',
                ];

    /**
     * @var null | string
     */
    protected $_buttonLinkParams = null;

    /**
     * @var array
     */
    protected $_popupSize = [630, 650];

    public function getProviderLink()
    {
        $token = $this->_getStartToken();
        if (!empty($token['oauth_token'])) {
            $this->_buttonLinkParams = self::URL_AUTHORIZE .'?oauth_token='. $token['oauth_token'];
        }
        return parent::getProviderLink();
    }

    // Step 2.
    public function loadUserData($response)
    {
        if (empty($response['oauth_token']) || empty($response['oauth_verifier'])) {
            $responseParams = $this->request->getParams();
            if (isset($responseParams['denied'])) {
                $this->_registry->register('close_popup', true);
            }

            return false;
        }

        $data = [];

        $oauth_nonce = $this->generateNonce();
        $oauth_timestamp = time();

        if(empty($response['oauth_token']) || empty($response['oauth_verifier']) || !$this->customerSession->getData('oauth_token_secret')) {
            return false;
        }

        $oauth_token = $response['oauth_token'];
        $oauth_verifier = $response['oauth_verifier'];
        $oauth_token_secret = $this->customerSession->getData('oauth_token_secret');

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode(self::URL_ACCESS_TOKEN)."&";
        $oauth_base_text .= urlencode("oauth_consumer_key=".$this->_applicationId."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_token=".$oauth_token."&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
        $oauth_base_text .= urlencode("oauth_verifier=".$oauth_verifier."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $key = $this->_secret .'&'. $oauth_token_secret;
        $oauth_signature = base64_encode(hash_hmac('sha1', $oauth_base_text, $key, true));

        $url = self::URL_ACCESS_TOKEN;
        $url .= '?oauth_nonce='.$oauth_nonce;
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$oauth_timestamp;
        $url .= '&oauth_consumer_key='.$this->_applicationId;
        $url .= '&oauth_token='.urlencode($oauth_token);
        $url .= '&oauth_verifier='.urlencode($oauth_verifier);
        $url .= '&oauth_signature='.urlencode($oauth_signature);
        $url .= '&oauth_version=1.0';

        $result = null;
        if ($response = $this->_call($url)) {
            parse_str($response, $result);
        }
        $this->_setLog($result);

        // Get user data.
        if (!empty($result['oauth_token']) && !empty($result['oauth_token_secret'])) {
            $oauth_nonce = $this->generateNonce();
            $oauth_timestamp = time();

            $oauth_token = $result['oauth_token'];
            $oauth_token_secret = $result['oauth_token_secret'];
            $screen_name = $result['screen_name'];

            $oauth_base_text = "GET&";
            $oauth_base_text .= urlencode(self::URL_ACCOUNT_DATA).'&';
            $oauth_base_text .= urlencode("include_email=true&");
            $oauth_base_text .= urlencode('oauth_consumer_key='.$this->_applicationId.'&');
            $oauth_base_text .= urlencode('oauth_nonce='.$oauth_nonce.'&');
            $oauth_base_text .= urlencode('oauth_signature_method=HMAC-SHA1&');
            $oauth_base_text .= urlencode('oauth_timestamp='.$oauth_timestamp."&");
            $oauth_base_text .= urlencode('oauth_token='.$oauth_token."&");
            $oauth_base_text .= urlencode('oauth_version=1.0&');
            $oauth_base_text .= urlencode('screen_name=' . $screen_name);

            $key = $this->_secret .'&'. $oauth_token_secret;
            $signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

            $url = self::URL_ACCOUNT_DATA;
            $url .= "?include_email=true";
            $url .= '&oauth_consumer_key=' . $this->_applicationId;
            $url .= '&oauth_nonce=' . $oauth_nonce;
            $url .= '&oauth_signature=' . urlencode($signature);
            $url .= '&oauth_signature_method=HMAC-SHA1';
            $url .= '&oauth_timestamp=' . $oauth_timestamp;
            $url .= '&oauth_token=' . urlencode($oauth_token);
            $url .= '&oauth_version=1.0';
            $url .= '&screen_name=' . $screen_name;

            $data = [];
            if ($response = $this->_call($url)) {
                $data = json_decode($response, true);
            }

            $this->_setLog($data, true);
        }

        if (!$this->_userData = $this->_prepareData($data)) {
            return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    // Step 1.
    protected function _getStartToken()
    {
        $result = null;

        $oauth_nonce = $this->generateNonce();
        $oauth_timestamp = time();

        $oauth_base_text = "GET&";
        $oauth_base_text .= urlencode(self::URL_REQUEST_TOKEN)."&";
        $oauth_base_text .= urlencode("oauth_callback=".urlencode($this->_redirectUri)."&");
        $oauth_base_text .= urlencode("oauth_consumer_key=".$this->_applicationId."&");
        $oauth_base_text .= urlencode("oauth_nonce=".$oauth_nonce."&");
        $oauth_base_text .= urlencode("oauth_signature_method=HMAC-SHA1&");
        $oauth_base_text .= urlencode("oauth_timestamp=".$oauth_timestamp."&");
        $oauth_base_text .= urlencode("oauth_version=1.0");

        $key = $this->_secret."&";
        $oauth_signature = base64_encode(hash_hmac("sha1", $oauth_base_text, $key, true));

        $url = self::URL_REQUEST_TOKEN;
        $url .= '?oauth_callback='.urlencode($this->_redirectUri);
        $url .= '&oauth_consumer_key='.$this->_applicationId;
        $url .= '&oauth_nonce='.$oauth_nonce;
        $url .= '&oauth_signature='.urlencode($oauth_signature);
        $url .= '&oauth_signature_method=HMAC-SHA1';
        $url .= '&oauth_timestamp='.$oauth_timestamp;
        $url .= '&oauth_version=1.0';

        if ($response = $this->_call($url)) {
            parse_str($response, $result);
        }

        if (!empty($result['oauth_token_secret'])) {
            $this->customerSession->setData('oauth_token_secret', $result['oauth_token_secret']);
        }

        $this->_setLog($response);

        return $result;
    }

    protected function _prepareData($data)
    {
        if (empty($data['id'])) {
            return false;
        }

        if (!empty($data['name'])) {
            $nameParts = explode(' ', $data['name'], 2);
            $data['name'] = $nameParts[0];
            $data['name2'] = !empty($nameParts[1])? $nameParts[1] : '';
        }

        return parent::_prepareData($data);
    }

    /**
     * @return string
     */
    private function generateNonce(): string
    {
        try {
            $prefix = random_int(5, 15);
        } catch (\Exception $e) {
            $prefix = '';
        }

        $nonce = base64_encode(uniqid($prefix, true));
        return preg_replace('#[\W]#','', $nonce);
    }
}
