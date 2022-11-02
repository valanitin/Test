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
 * Class Googleplus
 *
 * @package Bss\SocialLogin\Models
 */
class Googleplus extends \Bss\SocialLogin\Model\SocialLogin
{
    const OAUTH2_REVOKE_URI = 'https://accounts.google.com/o/oauth2/revoke';

    const OAUTH2_TOKEN_URI = 'https://accounts.google.com/o/oauth2/token';

    const OAUTH2_AUTH_URI = 'https://accounts.google.com/o/oauth2/auth';

    const OAUTH2_SERVICE_URI = 'https://www.googleapis.com/oauth2/v2';
    /**
     * @var string
     */
    protected $type = 'googleplus';
    /**
     * @var array
     */
    protected $scope = [

        'https://www.googleapis.com/auth/userinfo.profile',

        'https://www.googleapis.com/auth/userinfo.email',

    ];
    /**
     * @var string
     */
    protected $state = '';
    /**
     * @var string
     */
    protected $access = 'offline';
    /**
     * @var string
     */
    protected $prompt = 'auto';
    /**
     * @var array
     */
    protected $fields = [
                    'token_id' => 'id',
                    'firstname' => 'given_name',
                    'lastname' => 'family_name',
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
     * @return |null
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

        $url = self::OAUTH2_SERVICE_URI.'/userinfo';

        $params = [];
        $params = array_merge([
            'access_token' => $token->access_token

        ], $params);

        $data = [];

        $responses = $this->_httpRequest($url, 'GET', $params);
        $data = json_decode(json_encode($responses), true);


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
                    'state' => $this->state,
                    'access_type' => $this->access,
                    'approval_prompt' => $this->prompt
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
