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
 * Class Pinterest
 *
 * @package Bss\SocialLogin\Model
 */
class Pinterest extends \Bss\SocialLogin\Model\SocialLogin
{
    const OAUTH_AUTH_URI = 'https://api.pinterest.com/oauth/';
    /**
     * @var string
     */
    protected $type = 'pinterest';
    /**
     * @var array
     */
    protected $fields = [
                    'token_id' => 'id',
                    'firstname' => 'first_name',
                    'lastname' => 'last_name',
                    'email' => 'username',
                    'photo' => 'image',
                ];
    /**
     * @var array
     */
    protected $authUrl = [
                    'scope' => 'read_public',
                    'display' => 'popup',
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
            'grant_type' => 'authorization_code'
        ];
    
        $token = null;
        $token = $this->_httpRequest('https://api.pinterest.com/v1/oauth/token', 'POST', $params, $this->type);
       
        if (isset($token->access_token)) {
            $params = [
                'access_token'  => $token->access_token,
                'fields'        => implode(',', $this->fields)
            ];
        
            if ($data = $this->_httpRequest('https://api.pinterest.com/v1/me', 'GET', $params, $this->type)) {
                $data = json_decode(json_encode($data), true);
            }

            if (isset($data['data'])) {
                $data = $data['data'];

                if (isset($data['image']['60x60']['url'])) {
                    $data['image'] = $data['image']['60x60']['url'];
                }

                if (isset($data['username'])) {
                    $data['username'] = $data['username'].'@'. $this->type.'-user.com';
                }
            }
        }

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
            urldecode(http_build_query(
                [
                    'client_id'     => $this->applicationId,
                    'redirect_uri'  => $this->redirectUri,
                    'response_type'  => $this->response_type,
                    'display'       => 'popup',
                    'scope'         => 'read_public',
                ]
            ));

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
