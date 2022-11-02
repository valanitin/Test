<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_AjaxSocialLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SocialLogin\Model;

/**
 * Class Live
 * @package Bss\SocialLogin\Model
 */
class Live extends \Bss\SocialLogin\Model\SocialLogin
{
    const OAUTH2_TOKEN_URI = 'https://login.live.com/oauth20_token.srf';

    const OAUTH2_AUTH_URI = 'https://login.live.com/oauth20_authorize.srf';

    const OAUTH2_SERVICE_URI = 'https://apis.live.net/v5.0';
    /**
     * @var string
     */
    protected $type = 'live';
    /**
     * @var array
     */
    protected $scope = [
        'wl.signin',
        'wl.basic',
        'wl.emails'
    ];
    /**
     * @var array
     */
    protected $fields = [
                    'token_id' => 'id',
                    'firstname' => 'first_name',
                    'lastname' => 'last_name',
                    'email' => 'email',
                    'dob' => null,
                    'gender' => null,
                    'photo' => 'picture',
                ];
    /**
     * @var null
     */
    protected $authUrl = null;

    /**
     * @var array
     */
    protected $popupSize = [630, 650];

    /**
     * @return string
     */
    public function getButtonUrl()
    {
        $this->authUrl = $this->createAuthUrl();
        return parent::getButtonUrl();
    }

    /**
     * @param $response
     * @return bool
     * @throws \Magento\Framework\Validator\Exception
     * @throws \Zend_Http_Client_Exception
     */
    public function loadAccountInfo($response)
    {
        $token = $this->_httpRequest(
            self::OAUTH2_TOKEN_URI,
            'POST',
            [
                'code' => $response,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->applicationId,
                'client_secret' => $this->secret,
                'grant_type' => 'authorization_code'
            ]
        );

         $url = self::OAUTH2_SERVICE_URI.'/me';

        $params = [];
        $params = array_merge([
            'access_token' => $token->access_token
        ], $params);

        $data = [];

        $responses = $this->_httpRequest($url, 'GET', $params);
        $data = json_decode(json_encode($responses), true);

        $data['email'] = $data['emails']['account'];

        if (!$this->accountData = $this->_filterData($data)) {
            return false;
        }

        return true;
    }

    /**
     * @return string
     */
    public function createAuthUrl()
    {
        $url =
        self::OAUTH2_AUTH_URI.'?'.
            http_build_query(
                [
                    'response_type' => 'code',
                    'redirect_uri' => $this->redirectUri,
                    'client_id' => $this->applicationId,
                    'scope' => implode(' ', $this->scope),
                ]
            );

        return $url;
    }

    /**
     * @param $data
     * @return array|bool
     */
    protected function _filterData($data)
    {
        if (empty($data['id'])) {
            return false;
        }

        return parent::_filterData($data);
    }
}
