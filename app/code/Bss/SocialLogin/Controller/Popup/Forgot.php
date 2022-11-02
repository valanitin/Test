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

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Bss\SocialLogin\Helper\DataHelp;

/**
 * Class Forgot
 *
 * @package Bss\SocialLogin\Controller\Popup
 */
class Forgot extends Action
{
    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;
    /**
     * @var Escaper
     */
    protected $escaper;
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;
    /**
     * @var DataHelp
     */
    protected $helper2;

    /**
     * Forgot constructor.
     * @param DataHelp $helper2
     * @param Context $context
     * @param AccountManagementInterface $customerAccountManagement
     * @param Escaper $escaper
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        DataHelp $helper2,
        Context $context,
        AccountManagementInterface $customerAccountManagement,
        Escaper $escaper,
        JsonHelper $jsonHelper
    ) {
        $this->helper2 = $helper2;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->escaper                   = $escaper;
        $this->jsonHelper                = $jsonHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Zend_Http_Client_Exception
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        $result  = [];
        $email = (string)$this->getRequest()->getPost('email');
        $recaptcha_response = $this->getRequest()->getParam('g-recaptcha-response', false);
        $captchaError = '';
        if ($recaptcha_response) {
            $model = $this->helper2->getReCapcha();
            $captchaError = $model->verify($recaptcha_response);
        }

        if (!empty($captchaError)) {
            $result['error'] = $captchaError;
        } elseif ($email) {
            if (!\Zend_Validate::is($email, 'EmailAddress')) {
                $this->helper2->getSession()->setForgottenEmail($email);
                $result['error']     = true;
                $result['message'][] = __('Please correct the email address.');
            }

            try {
                $this->customerAccountManagement->initiatePasswordReset(
                    $email,
                    AccountManagement::EMAIL_RESET
                );
                $result['success']   = true;
                $result['message'][] = __(
                    'If there is an account associated with %1 you 
                    will receive an email with a link to reset your password.',
                    $this->escaper->escapeHtml($email)
                );
            } catch (NoSuchEntityException $e) {
                // Do nothing, we don't want anyone to use this action to determine which email accounts are registered.
            } catch (\Exception $exception) {
                $result['error']     = true;
                $result['message'][] = __('We\'re unable to send the password reset email.');
            }
        }

        $this->getResponse()->setBody($this->jsonHelper->jsonEncode($result));
        return;
    }
}
