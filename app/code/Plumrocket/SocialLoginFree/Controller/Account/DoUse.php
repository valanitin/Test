<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Controller\Account;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManager;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Helper\Data;
use Plumrocket\SocialLoginFree\Model\AccountProviderInterface;
use Plumrocket\SocialLoginFree\Model\Frontend\PopupManager;
use Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface;
use Plumrocket\SocialLoginFree\Model\Success\RedirectManager;

class DoUse extends Action
{
    /**
     * @var \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager
     */
    private $popupManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\AccountProviderInterface
     */
    private $accountProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

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
     * @var \Plumrocket\SocialLoginFree\Model\Success\RedirectManager
     */
    private $successRedirectManager;

    /**
     * DoUse constructor.
     *
     * @param \Magento\Framework\App\Action\Context                                     $context
     * @param \Magento\Customer\Model\Session                                           $customerSession
     * @param \Plumrocket\SocialLoginFree\Helper\Data                                   $dataHelper
     * @param \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager                   $popupManager
     * @param \Plumrocket\SocialLoginFree\Model\AccountProviderInterface                $accountProvider
     * @param \Plumrocket\SocialLoginFree\Helper\Config                                 $config
     * @param \Magento\Store\Model\StoreManager                                         $storeManager
     * @param \Plumrocket\SocialLoginFree\Model\Network\ApiCallParamsPersistorInterface $apiCallParamsPersistor
     * @param \Plumrocket\SocialLoginFree\Model\Success\RedirectManager                 $successRedirectManager
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        Data $dataHelper,
        PopupManager $popupManager,
        AccountProviderInterface $accountProvider,
        Config $config,
        StoreManager $storeManager,
        ApiCallParamsPersistorInterface $apiCallParamsPersistor,
        RedirectManager $successRedirectManager
    ) {
        parent::__construct($context);
        $this->popupManager = $popupManager;
        $this->accountProvider = $accountProvider;
        $this->config = $config;
        $this->dataHelper = $dataHelper;
        $this->customerSession = $customerSession;
        $this->storeManager = $storeManager;
        $this->apiCallParamsPersistor = $apiCallParamsPersistor;
        $this->successRedirectManager = $successRedirectManager;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $isAjax = $this->getRequest()->isXmlHttpRequest();

        if (! $this->config->isModuleEnabled()) {
            return $this->popupManager->close($isAjax);
        }

        $this->customerSession->unsPsloginLog();
        if ($this->customerSession->isLoggedIn() && !$this->getRequest()->getParam('call')) {
            return $this->popupManager->close($isAjax);
        }

        $type = $this->getRequest()->getParam('type');

        try {
            $model = $this->accountProvider->getByType($type);
        } catch (LocalizedException $e) {
            return $this->popupManager->close($isAjax);
        }

        if (! $model->enabled()) {
            return $this->popupManager->close($isAjax);
        }

        if ($call = $this->getRequest()->getParam('call')) {
            $this->apiCallParamsPersistor->add('type', $type);
            $this->apiCallParamsPersistor->add('action', $call);
        } else {
            $this->apiCallParamsPersistor->clear();
        }

        // Set current store.
        try {
            $currentStoreId = $this->storeManager->getStore()->getId();
        } catch (NoSuchEntityException $e) {
            return $this->popupManager->close($isAjax);
        }
        if ($currentStoreId) {
            $this->dataHelper->refererStore($currentStoreId);
        }

        // Set redirect url.
        if ($referer = $this->dataHelper->getCookieRefererLink()) {
            $this->successRedirectManager->setRefererUrl($referer);
        }

        switch ($model->getProtocol()) {
            case 'OAuth':
                if ($link = $model->getProviderLink()) {
                    return $this->_redirect($link);
                }

                return $this->config->isDebugMode()
                    ? $this->popupManager->showDeveloperErrors($model)
                    : $this->popupManager->showProductionError();

            case 'OpenID':
            case 'BrowserID':
            default:
                return $this->popupManager->close($isAjax);
        }
    }
}
