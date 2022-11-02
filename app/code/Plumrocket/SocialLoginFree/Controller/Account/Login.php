<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManager;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Helper\Data;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;
use Plumrocket\SocialLoginFree\Model\AccountProviderInterface;
use Plumrocket\SocialLoginFree\Model\Frontend\PopupManager;
use Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface;
use Plumrocket\SocialLoginFree\Model\Success\RedirectManager;

class Login extends Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $_registry;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\AccountProviderInterface
     */
    private $accountProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager
     */
    private $popupManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface
     */
    private $apiCallParamsPersistor;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    private $fakeEmail;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Success\RedirectManager
     */
    private $successRedirectManager;

    /**
     * @param \Magento\Framework\App\Action\Context                                     $context
     * @param \Magento\Customer\Model\Session                                           $customerSession
     * @param \Plumrocket\SocialLoginFree\Helper\Data                                   $dataHelper
     * @param \Magento\Framework\Registry                                               $registry
     * @param \Plumrocket\SocialLoginFree\Model\AccountProviderInterface                $accountProvider
     * @param \Plumrocket\SocialLoginFree\Helper\Config                                 $config
     * @param \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager                   $popupManager
     * @param \Magento\Store\Model\StoreManager                                         $storeManager
     * @param \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface $apiCallParamsPersistor
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail                  $fakeEmail
     * @param \Plumrocket\SocialLoginFree\Model\Success\RedirectManager                 $successRedirectManager
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Data $dataHelper,
        Registry $registry,
        AccountProviderInterface $accountProvider,
        Config $config,
        PopupManager $popupManager,
        StoreManager $storeManager,
        ApiCallParamsPersistorInterface $apiCallParamsPersistor,
        FakeEmail $fakeEmail,
        RedirectManager $successRedirectManager
    ) {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->accountProvider = $accountProvider;
        $this->config = $config;
        $this->popupManager = $popupManager;
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->apiCallParamsPersistor = $apiCallParamsPersistor;
        $this->fakeEmail = $fakeEmail;
        $this->successRedirectManager = $successRedirectManager;
    }

    public function execute()
    {
        $isAjax = $this->getRequest()->isXmlHttpRequest();
        $type = $this->getRequest()->getParam('type');

        $callParams = $this->apiCallParamsPersistor->get();

        // API.
        $callTarget = false;
        if ($callParams && isset($callParams['type']) && $callParams['type'] === $type && $callParams['action']) {
            $target = explode('.', $callParams['action'], 3);
            if (count($target) === 3) {
                $callTarget = $target;
            } else {
                return $this->popupManager->close($isAjax);
            }
        }

        if (! $callTarget && $this->customerSession->isLoggedIn()) {
            return $this->popupManager->close($isAjax);
        }

        try {
            $model = $this->accountProvider->getByType($type);
        } catch (LocalizedException $e) {
            return $this->popupManager->close($isAjax);
        }

        $responseTypes = $model->getResponseType();
        if (is_array($responseTypes)) {
            $networkResponse = [];
            foreach ($responseTypes as $responseType) {
                $networkResponse[$responseType] = $this->getRequest()->getParam($responseType);
            }
        } else {
            $networkResponse = $this->getRequest()->getParam($responseTypes);
        }
        $model->_setLog($this->getRequest()->getParams());

        if (! $model->loadUserData($networkResponse)) {
            if ($this->_registry->registry('close_popup')) {
                return $this->popupManager->close($isAjax);
            }

            return $this->config->isDebugMode()
                ? $this->popupManager->showDeveloperErrors($model)
                : $this->popupManager->showProductionError();
        }

        // Switch store.
        if ($storeId = $this->dataHelper->refererStore()) {
            $this->storeManager->setCurrentStore($storeId);
        }

        // API.
        if ($callTarget) {
            list($module, $controller, $action) = $callTarget;
            $this->_forward($action, $controller, $module, ['pslogin' => $model->getUserData()]);
            return;
        }

        if ($customerId = $model->getCustomerIdByUserId()) {
            if (! $this->fakeEmail->detect($model->getSocialNetworkEmail())) {
                $this->fakeEmail->changeToReal($customerId, $model->getSocialNetworkEmail());
            }

            $redirectUrl = $this->successRedirectManager->getAfterLoginUrl();
        } elseif ($customerId = $model->getCustomerIdByEmail()) {
            # Customer with received email was placed in db.
            // Remember customer.
            $model->linkCustomerIdToNetwork($customerId);
            $this->messageManager->addComplexNoticeMessage(
                'resetPasswordMessage',
                [
                    'email' => $model->getSocialNetworkEmail(),
                    'url' => $this->_url->getUrl('customer/account/forgotpassword', [])
                ]
            );

            $redirectUrl = $this->successRedirectManager->getAfterLoginUrl();
        } else {
            # Registration customer.
            if ($customerId = $model->registrationCustomer()) {
                # Success.
                /**
                 * Display system messages before \Plumrocket\SocialLoginFree\Model\Account::linkCustomerIdToNetwork
                 * because that method will reset messages
                 */
                if ($this->fakeEmail->detect($model->getSocialNetworkEmail())) {
                    $this->messageManager->addSuccessMessage(__('Customer registration successful.'));
                } else {
                    $this->messageManager->addSuccessMessage(
                        __(
                            'Customer registration successful. Your password was sent to %1',
                            $model->getSocialNetworkEmail()
                        )
                    );
                }

                if ($errors = $model->getErrors()) {
                    foreach ($errors as $error) {
                        $this->messageManager->addNoticeMessage($error);
                    }
                }

                $this->dispatchRegisterSuccessEvent($model->getCustomer());

                $model->linkCustomerIdToNetwork($customerId);

                $model->sendNewAccountEmail();

                $this->popupManager->showSharePopup();

                $redirectUrl = $this->successRedirectManager->getAfterRegisterUrl();
            } else {
                # Error.
                $userData = $model->getUserData();
                $this->customerSession->setCustomerFormData($userData);
                $this->customerSession->setPsloginFields($userData);
                $redirectUrl = $this->_url->getUrl('customer/account/create', ['_secure' => true]);

                if ($errors = $model->getErrors()) {
                    foreach ($errors as $error) {
                        $this->messageManager->addErrorMessage($error);
                    }
                }

                // Remember current provider data.
                $this->customerSession->setData('pslogin', [
                    'provider'  => $model->getProvider(),
                    'user_id'   => $model->getUserData('user_id'),
                    'photo'     => $model->getUserData('photo'),
                    'timeout'   => time() + Data::TIME_TO_EDIT,
                ]);
            }
        }

        if ($customerId) {
            if ($this->config->isPhotoEnabled()) {
                $model->loadAndSaveCustomerPhotoFromNetwork($customerId);
            }

            // Logged in.
            if ($this->customerSession->loginById($customerId)) {
                $this->customerSession->regenerateId();
            }

            $this->successRedirectManager->unsetRefererUrl();
        }

        $this->customerSession->unsPsloginLog();

        return $this->popupManager->redirect($isAjax, $redirectUrl);
    }

    /**
     * @param $customer
     */
    protected function dispatchRegisterSuccessEvent($customer)
    {
        $this->_eventManager->dispatch(
            'customer_register_success',
            ['account_controller' => $this, 'customer' => $customer]
        );
    }
}
