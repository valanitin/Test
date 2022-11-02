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
 * Class Instagram
 *
 * @package Bss\SocialLogin\Model
 */
class Instagram extends \Bss\SocialLogin\Model\SocialLogin
{
    
    const OAUTH_AUTH_URI = 'https://api.instagram.com/oauth/authorize';
    /**
     * @var string
     */
    protected $type = 'instagram';
    /**
     * @var array
     */
    protected $fields = [
                    'token_id' => 'id',
                    'firstname' => 'username',
                    'lastname' => 'full_name',
                    'email' => null,
                    'dob' => null,
                    'gender' => null,
                    'photo' => 'profile_picture',
                ];
    /**
     * @var array
     */
    protected $popupSize = [650, 350];

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
        if (empty($response)) {
            return false;
        }

        $data = [];

        $params = [
            'client_id' => $this->applicationId,
            'client_secret' => $this->secret,
            'code' => $response,
            'redirect_uri' => $this->redirectUri,
            'grant_type'=>'authorization_code',
        ];

        $token = null;
        $token = $this->_httpRequest('https://api.instagram.com/oauth/access_token', 'POST', $params, $this->type);
        if (isset($token->access_token)) {
            $params = [
                'access_token'  => $token->access_token
            ];
    
            if ($data = $this->_httpRequest(
                'https://api.instagram.com/v1/users/self/?access_token='.$token->access_token,
                'GET',
                $params,
                    $this->type
            )) {
                $data = json_decode(json_encode($data), true);
            }
        }
        
        $data =  $data['data'];

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
        self::OAUTH_AUTH_URI.'?'.
            http_build_query(
                [
                    'response_type' => 'code',
                    'redirect_uri' => $this->redirectUri,
                    'client_id' => $this->applicationId,
                    'scope' => 'basic',
                ]
            );

        return $url;
    }


    protected function _filterData($data)
    {
        if (empty($data['id'])) {
            return false;
        }
        if (!empty($data['full_name'])) {
            $nameParts = explode(' ', $data['full_name'], 2);
            $data['firstname'] = $nameParts[0];
            $data['lastname'] = !empty($nameParts[1])? $nameParts[1] : '';
        }
        return parent::_filterData($data);
    }
}
