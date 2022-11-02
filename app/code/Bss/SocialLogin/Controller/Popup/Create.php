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

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Escaper;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\InputException;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Bss\SocialLogin\Helper\Data;
use Bss\SocialLogin\Helper\DataHelp;

/**
 * Class Create
 * @SuppressWarnings(PHPMD)
 * @package Bss\SocialLogin\Controller\Popup
 */
class Create extends AbstractAccount
{
    /**
     * @var DataHelp
     */
    protected $helper2;
    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;
    /**
     * @var Escaper
     */
    protected $escaper;
    /**
     * @var CustomerExtractor
     */
    /**
     * @var CustomerExtractor
     */
    protected $customerExtractor;
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;
    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Create constructor.
     * @param Context $context
     * @param AccountManagementInterface $accountManagement
     * @param Escaper $escaper
     * @param CustomerExtractor $customerExtractor
     * @param DataObjectHelper $dataObjectHelper
     * @param JsonHelper $jsonHelper
     * @param Data $helperData
     * @param DataHelp $helper2
     */
    public function __construct(
        Context $context,
        AccountManagementInterface $accountManagement,
        Escaper $escaper,
        CustomerExtractor $customerExtractor,
        DataObjectHelper $dataObjectHelper,
        JsonHelper $jsonHelper,
        Data $helperData,
        DataHelp $helper2
    ) {
        $this->helper2 = $helper2;
        $this->accountManagement = $accountManagement;
        $this->escaper = $escaper;
        $this->customerExtractor = $customerExtractor;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->jsonHelper = $jsonHelper;
        $this->helperData = $helperData;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Customer\Api\Data\AddressInterface|null
     */
    protected function extractAddress()
    {
        if (!$this->getRequest()->getPost('create_address')) {
            return null;
        }
        $addressForm = $this->helper2->getFormFactory()->create('customer_address', 'customer_register_address');
        $allowedAttributes = $addressForm->getAllowedAttributes();
        $addressData = [];
        $regionDataObject = $this->helper2->getRegionDataFactory()->create();
        foreach ($allowedAttributes as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = $this->getRequest()->getParam($attributeCode);
            if ($value===null) {
                continue;
            }
            switch ($attributeCode) {
                case 'region_id':
                    $regionDataObject->setRegionId($value);
                    break;
                case 'region':
                    $regionDataObject->setRegion($value);
                    break;
                default:
                    $addressData[$attributeCode] = $value;
            }
        }
        $addressDataObject = $this->helper2->getAddressFactory()->create();
        $this->dataObjectHelper->populateWithArray(
            $addressDataObject,
            $addressData,
            \Magento\Customer\Api\Data\AddressInterface::class
        );
        $addressDataObject->setRegion($regionDataObject);

        $addressDataObject->setIsDefaultBilling(
            $this->getRequest()->getParam('default_billing', false)
        )->setIsDefaultShipping(
            $this->getRequest()->getParam('default_shipping', false)
        );

        return $addressDataObject;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $result = [];
        $this->helper2->getSession()->regenerateId();
        try {
            $addresses = $this->getAddress();
            $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
            $customer->setAddresses($addresses);
            $password = $this->getRequest()->getParam('password');
            $confirmation = $this->getRequest()->getParam('password_confirmation');
            $recaptcha_response = $this->getRequest()->getParam('g-recaptcha-response', false);
            if (!$this->checkPasswordConfirmation($password, $confirmation)) {
                $result['error'] = true;
                $result['message'][] = __('Please make sure your passwords match.');
            }
            $captchaError = $this->modelChild($recaptcha_response);
            if (!empty($captchaError)) {
                $result['error'] = $captchaError;
            }
            if ($this->checkError($result['error'])==true) {
                $customer = $this->accountManagement
                    ->createAccount($customer, $password);
                if ($this->getRequest()->getParam('is_subscribed', false)) {
                    $this->helper2->getSub()->create()->subscribeCustomerById($customer->getId());
                }
                $this->_eventManager->dispatch(
                    'customer_register_success',
                    ['account_controller' => $this, 'customer' => $customer]
                );
                $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
                if ($confirmationStatus==='account_confirmation_required') {
                    $email = $this->helper2->getCustomerUrl()->getEmailConfirmationUrl($customer->getEmail());
                    // @codingStandardsIgnoreStart
                    $result['success'] = false;
                    $result['message'][] = __(
                        'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
                        $email
                    );
                    $result['redirect'] = $this->helperData->getRedirectUrl('register');
                } else {
                    $result['success'] = true;
                    $result['message'][] = __(
                        'Create an account successfully. Please wait...'
                    );
                    $result['redirect'] = $this->helperData->getRedirectUrl('register');
                    $this->helper2->getSession()->setCustomerDataAsLoggedIn($customer);
                }
            }
        } catch (StateException $e) {
            $url = $this->helper2->getUrlFactory()->create()->getUrl('customer/account/forgotpassword');
            $result['error'] = true;

            $result['message'][] = __(
                'There is already an account with this email address. If you are sure that it is your email address, <a href="%1">click here</a> to get your password and access your account.',
                $url
            );
        } catch (InputException $e) {
            $result['error'] = true;
            $result['message'][] = $this->escaper->escapeHtml($e->getMessage());
        } catch (\Exception $e) {
            $result['error'] = true;
            $result['message'][] = $this->escaper->escapeHtml($e->getMessage());
        }
        $this->helper2->getSession()->setCustomerFormData($this->getRequest()->getPostValue());
        $this->getResponse()->setBody($this->jsonHelper->jsonEncode($result));
        return;
    }

    /**
     * @param $password
     * @param $confirmation
     * @return bool
     */
    protected function checkPasswordConfirmation($password, $confirmation)
    {
        return $password==$confirmation;
    }

    /**
     * @param $check
     * @return bool
     */
    protected function checkError($check)
    {
        if (!isset($check) || !$check) {
            return true;
        }
        return false;
    }

    /**
     * @param $recaptcha_response
     * @return string
     * @throws \Zend_Http_Client_Exception
     */
    protected function modelChild($recaptcha_response)
    {
        $captchaError = '';
        if ($recaptcha_response) {
            $model = $this->helper2->getReCapcha();
            $captchaError = $model->verify($recaptcha_response);
        }
        return $captchaError;
    }

    /**
     * @return array
     */
    protected function getAddress()
    {
        $address = $this->extractAddress();
        $addresses = $address===null ? [] : [$address];
        return $addresses;
    }
}
