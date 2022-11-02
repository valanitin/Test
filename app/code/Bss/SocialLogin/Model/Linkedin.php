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
 * Class Linkedin
 * @package Bss\SocialLogin\Model
 */
class Linkedin extends \Bss\SocialLogin\Model\SocialLogin
{

    const OAUTH2_TOKEN_URI = 'https://www.linkedin.com/uas/oauth2/accessToken';

    const OAUTH2_AUTH_URI = 'https://www.linkedin.com/uas/oauth2/authorization';

    const OAUTH2_SERVICE_URI = 'https://api.linkedin.com/v1';
    /**
     * @var string
     */
    protected $type = 'linkedin';
    /*
     * @inheritdoc
     */
    protected $scope = [
        'r_liteprofile',
        'r_emailaddress'
    ];
    /**
     * @var string
     */
    protected $state = '987654321';
    /**
     * @var array
     */
    protected $fields = [
                    'token_id' => 'id',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'email' => 'email',
                    'dob' => null,
                    'gender' => null,
                    'photo' => null,
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
                'client_id' => $this->applicationId,
                'client_secret' => $this->secret,
                'grant_type' => 'authorization_code',
                'redirect_uri' => $this->redirectUri,
            ]
        );

        $userInfoApi = [
                        'id',
                        'firstName',
                        'lastName',
                        'headline',
                        'vanityName',
                        'emailAddress',
                        'phone-numbers',
                        'location'
                    ];
                    
        $url = 'https://api.linkedin.com/v2/me?projection=('.implode(',', $userInfoApi).')';

        $params = [];
        $params = array_merge([
            'oauth2_access_token' => $token->access_token
        ], $params);
        $responses = $this->_httpRequest($url, 'GET', $params);
        $urlEmail = "https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))";
        $getEmail = $this->_httpRequest($urlEmail, 'GET', $params);
        $dataEmail = json_decode(json_encode($getEmail), true);
        $data = json_decode(json_encode($responses), true);
        $data2['id'] = $data['id'];
        $data2['lastname'] = $data['lastName']['localized']['en_US'] ? $data['lastName']['localized']['en_US'] : '...';
        $data2['firstname'] = $data['firstName']['localized']['en_US'] ? $data['firstName']['localized']['en_US'] : '...';
        $data2['email'] = $dataEmail['elements'][0]['handle~']['emailAddress'] ?
            $dataEmail['elements'][0]['handle~']['emailAddress'] : '';
        if (!$this->accountData = $this->_filterData($data2)) {
            return false;
        }
        return true;
    }

    /**
     * @return string
     */
    public function createAuthUrl()
    {
        $this->state = md5(uniqid(rand(), true));
        $url =
        self::OAUTH2_AUTH_URI.'?'.
            http_build_query(
                [
                    'response_type' => 'code',
                    'redirect_uri' => $this->redirectUri,
                    'client_id' => $this->applicationId,
                    'scope' => implode(' ', $this->scope),
                    'state' => $this->state,
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
