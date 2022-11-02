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
 * Class Vkontakte
 *
 * @package Bss\SocialLogin\Model
 */
class Vkontakte extends \Bss\SocialLogin\Model\SocialLogin
{
    const OAUTH_AUTH_URI = 'https://oauth.vk.com/authorize';

    const VERSION = '5.95';
    /**
     * @var string
     */
    protected $type = 'vkontakte';
    /**
     * @var array
     */
    protected $fields = [
        'user_id' => 'uid',
        'firstname' => 'first_name',
        'lastname' => 'last_name',
        'email' => 'user_email',
        'phone' => 'phone',
        'dob' => null,
        'gender' => 'sex',
        'photo' => 'photo'
    ];
    /**
     * @var array
     */
    protected $authUrl = [
        'scope' => 'email',
        'response_type' => 'token'
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
            'scope' => 'email'
        ];

        $token = null;
        $token = $this->_httpRequest('https://oauth.vk.com/access_token', 'POST', $params, $this->type);
        if (isset($token->access_token)) {
            $params = [
                'access_token'  => $token->access_token,
                'fields'        => implode(',', $this->fields),
                'v'      => self::VERSION
            ];
            if ($data = $this->_httpRequest('https://api.vk.com/method/getProfiles?', 'GET', $params, $this->type)) {
                $data = json_decode(json_encode($data), true);
            }
            $data = $data['response'][0];
            $data['sex'] = ($data['sex'] == 0 || $data['sex'] == 2) ? 1 : 0;
            $data['user_email'] = (isset($token->email)) ? $token->email : '';
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
            http_build_query(
                [
                    'client_id'     => $this->applicationId,
                    'redirect_uri'  => $this->redirectUri,
                    'response_type' => $this->response_type,
                    'scope' => 'email'
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
