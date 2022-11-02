<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Observer;

use Plumrocket\SocialLoginFree\Helper\Config;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Plumrocket\SocialLoginFree\Model\Success\RedirectManager;

/**
 * Modify after login redirect according to the configuration
 */
class LoginObserver implements ObserverInterface
{
    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Success\RedirectManager
     */
    private $successRedirectManager;

    /**
     * LoginObserver constructor.
     *
     * @param \Plumrocket\SocialLoginFree\Helper\Config                 $config
     * @param \Magento\Customer\Model\Session                           $customerSession
     * @param \Plumrocket\SocialLoginFree\Model\Success\RedirectManager $successRedirectManager
     */
    public function __construct(
        Config $config,
        Session $customerSession,
        RedirectManager $successRedirectManager
    ) {
        $this->config = $config;
        $this->customerSession = $customerSession;
        $this->successRedirectManager = $successRedirectManager;
    }

    public function execute(Observer $observer)
    {
        if (! $this->config->isModuleEnabled()) {
            return;
        }

        $this->customerSession->setBeforeAuthUrl($this->successRedirectManager->getAfterLoginUrl());
    }
}
