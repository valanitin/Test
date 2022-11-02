<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Observer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Model\AccountProviderInterface;
use Plumrocket\SocialLoginFree\Model\Frontend\PopupManager;
use Plumrocket\SocialLoginFree\Model\Success\RedirectManager;

/**
 * - Link social network user id to magento customer id
 * - load photo from social network
 * - Show share popup
 * - Modify after register redirect according to the configuration
 */
class RegistrationSuccessObserver implements ObserverInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\AccountProviderInterface
     */
    private $accountProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Success\RedirectManager
     */
    private $successRedirectManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager
     */
    private $popupManager;

    /**
     * RegistrationSuccessObserver constructor.
     *
     * @param \Magento\Customer\Model\Session                            $customerSession
     * @param \Magento\Framework\App\RequestInterface                    $httpRequest
     * @param \Plumrocket\SocialLoginFree\Model\AccountProviderInterface $accountProvider
     * @param \Plumrocket\SocialLoginFree\Helper\Config                  $config
     * @param \Plumrocket\SocialLoginFree\Model\Success\RedirectManager  $successRedirectManager
     * @param \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager    $popupManager
     */
    public function __construct(
        Session $customerSession,
        RequestInterface $httpRequest,
        AccountProviderInterface $accountProvider,
        Config $config,
        RedirectManager $successRedirectManager,
        PopupManager $popupManager
    ) {
        $this->session = $customerSession;
        $this->request = $httpRequest;
        $this->accountProvider = $accountProvider;
        $this->config = $config;
        $this->successRedirectManager = $successRedirectManager;
        $this->popupManager = $popupManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        if (! $this->config->isModuleEnabled()) {
            return;
        }

        $data = $this->session->getData('pslogin');

        if (! empty($data['provider']) && ! empty($data['timeout']) && $data['timeout'] > time()) {
            try {
                $model = $this->accountProvider->getByType($data['provider']);
            } catch (LocalizedException $e) {
                return;
            }
            $customerId = null;
            /** @var \Magento\Customer\Model\Customer $customer */
            if ($customer = $observer->getCustomer()) {
                $customerId = (int) $customer->getId();
            }

            if ($customerId) {
                $model->setUserData($data);

                // Remember customer.
                $model->linkCustomerIdToNetwork($customerId);

                // Load photo.
                if ($this->config->isPhotoEnabled()) {
                    $model->loadAndSaveCustomerPhotoFromNetwork($customerId);
                }
            }
            $this->session->unsPsloginFields();
        }

        $this->popupManager->showSharePopup();

        $this->request->setParam(
            RedirectInterface::PARAM_NAME_SUCCESS_URL,
            $this->successRedirectManager->getAfterRegisterUrl()
        );
    }
}
