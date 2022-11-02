<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model;

/**
 * @since 1.0.0
 */
class Facebook extends Account
{
    const PROVIDER = 'facebook';

    protected $provider = self::PROVIDER;

    protected $_type = self::PROVIDER;

    protected $_url = 'https://www.facebook.com/dialog/oauth';

    protected $fieldsMapping = [
        'user_id'   => 'id',
        'firstname' => 'first_name',
        'lastname'  => 'last_name',
        'email'     => 'email',
        'dob'       => 'birthday',
        'gender'    => 'gender',
        'photo'     => 'picture',
    ];

    protected $_buttonLinkParams = [
        'scope'   => 'email',
        'display' => 'popup',
    ];

    protected $_popupSize = [650, 350];

    public function _construct()
    {
        parent::_construct();

        $this->_buttonLinkParams = array_merge(
            $this->_buttonLinkParams,
            [
                'client_id'     => $this->_applicationId,
                'redirect_uri'  => $this->_redirectUri,
                'response_type' => $this->_responseType,
            ]
        );
    }

    public function loadUserData($response)
    {
        if (empty($response)) {
            $responseParams = $this->request->getParams();
            if (isset($responseParams['error']) && $responseParams['error'] === "access_denied") {
                $this->_registry->register('close_popup', true);
            }

            return false;
        }

        $data = [];

        $params = [
            'client_id'     => $this->_applicationId,
            'client_secret' => $this->_secret,
            'code'          => $response,
            'redirect_uri'  => $this->_redirectUri,
        ];

        $token = $this->getToken($params);

        $this->_setLog($token, true);
        if (isset($token['access_token'])) {
            $userId = $this->getUserId($token['access_token']);

            $params = [
                'fields'       => implode(',', $this->getNetworkFieldsMapping()),
                'access_token' => $token['access_token'],
            ];

            $response = $this->_call("https://graph.facebook.com/v11.0/$userId/", $params);
            if ($response) {
                $data = $this->serializer->unserialize($response);
            }

            if (! empty($data['id'])) {
                $data['picture'] = "https://graph.facebook.com/{$data['id']}/picture?" .
                    "access_token={$token['access_token']}";
            }

            $this->_setLog($data, true);
        }

        if (! $this->_userData = $this->_prepareData($data)) {
            return false;
        }

        $this->_setLog($this->_userData, true);

        return true;
    }

    protected function _prepareData($data)
    {
        if (empty($data['id'])) {
            return false;
        }

        return parent::_prepareData($data);
    }

    /**
     * @param array $params
     * @return array|bool|float|int|string|null
     */
    private function getToken(array $params)
    {
        $token = null;
        if ($response = $this->_call('https://graph.facebook.com/oauth/access_token', $params)) {
            try {
                $token = $this->serializer->unserialize($response);
            } catch (\InvalidArgumentException $exception) {
                $token = false;
            }

            if (! $token) {
                parse_str($response, $token);
            }
        }
        return $token;
    }

    /**
     * @param string $accessToken
     * @return string
     */
    private function getUserId(string $accessToken): string
    {
        $response = $this->_call(
            'https://graph.facebook.com/v11.0/me',
            [
                'fields'       => 'id',
                'access_token' => $accessToken
            ]
        );

        if (! $response) {
            return '';
        }

        return (string) $this->serializer->unserialize($response)['id'];
    }
}
