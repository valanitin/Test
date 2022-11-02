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
namespace Bss\SocialLogin\Controller\Account;

use Magento\Framework\App\Action\Context;

/**
 * Class Login
 *
 * @package Bss\SocialLogin\Controller\Account
 */
class Login extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;
    /**
     * @var \Bss\SocialLogin\Helper\Data
     */
    protected $helper;

    /**
     * Login constructor.
     * @param \Magento\Customer\Model\Session $session
     * @param \Bss\SocialLogin\Helper\Data $helper
     * @param Context $context
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Bss\SocialLogin\Helper\Data $helper,
        Context $context
    ) {
        $this->helper = $helper;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    protected function _getSession()
    {
        return $this->session;
    }

    /**
     * @param $url
     * @param array $params
     * @return string
     */
    protected function _getUrl($url, $params = [])
    {
        return $this->_url->getUrl($url, $params);
    }

    /**
     * @return \Bss\SocialLogin\Helper\Data
     */
    protected function _getHelper()
    {
        return $this->helper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $session = $this->_getSession();

        $type = $this->getRequest()->getParam('type');

        $close = '<script type="text/javascript">window.close();</script>';

        $className = 'Bss\SocialLogin\Model\\' . ucfirst($type);

        $this->_close($session, $type, $className, $close);

        $model = \Magento\Framework\App\ObjectManager::getInstance()->get($className);

        $this->_refreshtwitter($type, $model);

        $responseTypes = $model->getResponseType();

        $this->_responseType($responseTypes, $model, $close);

        if ($customerId = $model->getCustomerIdByTokenId()) {
            $redirectUrl = $this->_getHelper()->getRedirectUrl();
        } elseif ($customerId = $model->getCustomerIdByEmail()) {
            $model->setCustomerIdByUserId($customerId);
            $url = $this->_getUrl('customer/account/forgotpassword');
            $redirectUrl = $this->_getHelper()->getRedirectUrl();
        } else {
            if ($customerId = $model->registerAccount()) {
                $fakemail = strpos($model->getAccountData('email'), $type . '-user.com');

                if ($fakemail) {
                    $this->messageManager->addNotice(
                        __(
                            'Your account needs to be updated.The email address in your profile is invalid.
                            We were unable to send you your store accout credentials. 
                            To be able to login using store account credentials you will 
                            need to update your email address 
                            and password using our <a href="%1">Edit Account Information</a>.',
                            \Magento\Framework\App\ObjectManager::getInstance()
                                ->get(\Magento\Store\Model\Store::class)
                                ->getUrl('customer/account/edit')
                        )
                    );
                    $this->messageManager->addSuccess(
                        __('Customer registration successful.')
                    );
                } else {
                    $this->messageManager->addSuccess(
                        __(
                            'Customer registration successful. Your password was send to the email: %1',
                            $model->getAccountData('email')
                        )
                    );
                }

                if ($errors = $model->getErrors()) {
                    foreach ($errors as $error) {
                        $this->messageManager->addNotice($error);
                    }
                }
                $model->setCustomerIdByTokenId($customerId);
                $redirectUrl = $this->_getHelper()->getRedirectUrl('register');
            } else {
                $session->setCustomerFormData($model->getAccountData());

                $redirectUrl = $this->_getUrl('customer/account/create', ['_secure' => true]);

                if ($errors = $model->getErrors()) {
                    foreach ($errors as $error) {
                        $this->messageManager->addError($error);
                    }
                }
            }
        }
        $this->_customerphoto($customerId, $model, $session);
        $this->childFunction($redirectUrl);
    }

    /**
     * @param $redirectUrl
     * @return mixed
     */
    protected function childFunction($redirectUrl)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->getResponse()->clearHeaders()->setHeader('Content-type', 'application/json', true);
            $this->getResponse()->setBody(json_encode([
                'redirectUrl' => $redirectUrl
            ]));
        } else {
            $js = '
                var windowsocial = window.opener ? window.opener.document : document;
                windowsocial.getElementById("sociallogin-login-referer").value = "'.htmlspecialchars(base64_encode($redirectUrl)).'";
                windowsocial.getElementById("sociallogin-login-submit").click();
            ';
            $body = '<script type="text/javascript">
            if(window.opener && window.opener.location &&  !window.opener.closed)
            { window.close(); }; ' . $js . ';</script>';
            $this->getResponse()->setBody($body);
        }
    }

    /**
     * @param $session
     * @param $type
     * @param $className
     * @param $close
     */
    protected function _close($session, $type, $className, $close)
    {
        if ($session->isLoggedIn() || !$type || !class_exists($className)) {
            $this->getResponse()->setBody($close);
        }
    }

    /**
     * @param $type
     * @param $model
     * @return \Magento\Framework\App\ResponseInterface
     */
    protected function _refreshtwitter($type, $model)
    {
        if ($type=='twitter' and $this->getRequest()->getParam('refresh')) {
            return $this->_redirect($model->getButtonUrl());
        }
    }

    /**
     * @param $responseTypes
     * @param $model
     * @param $close
     */
    protected function _responseType($responseTypes, $model, $close)
    {
        if (is_array($responseTypes)) {
            $response = [];
            foreach ($responseTypes as $responseType) {
                $response[$responseType] = $this->getRequest()->getParam($responseType);
            }
        } else {
            $response = $this->getRequest()->getParam($responseTypes);
        }

        if (!$model->loadAccountInfo($response)) {
            $this->getResponse()->setBody($close);
        }
    }

    /**
     * @param $customerId
     * @param $model
     * @param $session
     */
    protected function _customerphoto($customerId, $model, $session)
    {
        if ($customerId) {
            if ($this->_getHelper()->photoEnabled()) {
                $model->setCustomerPhoto($customerId);
            }

            if ($session->loginById($customerId)) {
                $session->regenerateId();
            }
        }
    }
}
