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
namespace Bss\SocialLogin\Controller\Popup;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Bss\SocialLogin\Helper\Data as HelperData;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Login
 *
 * @package Bss\SocialLogin\Controller\Popup
 */
class Login extends Action
{
    /**
     * @var HelperData
     */
    protected $helperData;
    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;
    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;
    /**
     * @var \Bss\SocialLogin\Model\Recaptcha
     */
    protected $recaptcha;

    /**
     * Login constructor.
     * @param Context $context
     * @param HelperData $helperData
     * @param AccountManagementInterface $accountManagement
     * @param CustomerSession $customerSession
     * @param \Bss\SocialLogin\Model\Recaptcha $recaptcha
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        Context $context,
        HelperData $helperData,
        AccountManagementInterface $accountManagement,
        CustomerSession $customerSession,
        \Bss\SocialLogin\Model\Recaptcha $recaptcha,
        JsonHelper $jsonHelper
    ) {
        parent::__construct($context);
        $this->helperData        = $helperData;
        $this->accountManagement = $accountManagement;
        $this->customerSession   = $customerSession;
        $this->jsonHelper        = $jsonHelper;
        $this->recaptcha = $recaptcha;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Zend_Http_Client_Exception
     */
    public function execute()
    {
        $username      = $this->getRequest()->getParam('username', false);
        $password      = $this->getRequest()->getParam('password', false);
        $recaptcha_response = $this->getRequest()->getParam('g-recaptcha-response', false);
        $result = [];
        $captchaError = '';
        if ($recaptcha_response) {
            $model = $this->recaptcha;
            $captchaError = $model->verify($recaptcha_response);
        }
        if (!empty($captchaError)) {
            $result['error'] = $captchaError;
            $this->getResponse()->setBody($this->jsonHelper->jsonEncode($result));
        } elseif ($username && $password) {
            try {
                $accountManage = $this->accountManagement;
                $customer      = $accountManage->authenticate(
                    $username,
                    $password
                );
                $this->customerSession->setCustomerDataAsLoggedIn($customer);
                $this->customerSession->regenerateId();
            } catch (\Exception $e) {
                $result['error']   = true;
                $result['message'] = $e->getMessage();
            }
            
            if (!isset($result['error'])) {
                $result['success'] = true;
                $result['message'] = __('Login successfully. Please wait ...');
                $result['redirect'] = $this->helperData->getRedirectUrl();
            }
        } else {
            $result['error']   = true;
            $result['message'] = __(
                'Please enter a username and password.'
            );
        }
        
        $this->getResponse()->setBody($this->jsonHelper->jsonEncode($result));
        return;
    }
}
