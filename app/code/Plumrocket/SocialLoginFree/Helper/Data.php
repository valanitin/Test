<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Helper;

use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;
use Plumrocket\SocialLoginFree\Model\Account\Photo;
use Plumrocket\SocialLoginFree\Model\Frontend\PopupManager;
use Plumrocket\SocialLoginFree\Model\Information;

class Data extends Main
{
    /**
     * Section name for configs
     * @deprecated since 3.0.0
     * @see Information::CONFIG_SECTION
     */
    const SECTION_ID = Information::CONFIG_SECTION;

    const REFERER_QUERY_PARAM_NAME = 'pslogin_referer';
    const REFERER_STORE_PARAM_NAME = 'pslogin_referer_store';
    /**
     * @deprecated since 3.0.0
     * @see PopupManager::SHOW_SHARE_POPUP_COOKIE_NAME
     */
    const SHOW_POPUP_PARAM_NAME = PopupManager::SHOW_SHARE_POPUP_COOKIE_NAME;
    const API_CALL_PARAM_NAME = 'pslogin_api_call';
    /**
     * @deprecated since 3.0.0
     * @see \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail::FAKE_EMAIL_PREFIX
     */
    const FAKE_EMAIL_PREFIX = FakeEmail::FAKE_EMAIL_PREFIX;
    const TIME_TO_EDIT = 300;
    const DEFAULT_STORE_CODE = 1;

    /**
     * @var string
     */
    protected $_configSectionId = Information::CONFIG_SECTION;

    /**
     * @var null | array
     */
    private $_buttons = null;

    /**
     * @var null | array
     */
    private $_buttonsPrepared = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Buttons\Factory
     */
    private $buttonsFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Api\TypesProviderInterface
     */
    private $typesProvider;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Photo
     */
    private $photo;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Success\RedirectManager
     */
    private $successRedirectManager;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Provider\Callback
     */
    private $callback;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager
     */
    private $popupManager;

    /**
     * @var \Plumrocket\Base\Model\Utils\Config
     */
    private $configUtils;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\ObjectManagerInterface                 $objectManager
     * @param \Magento\Framework\App\Helper\Context                     $context
     * @param \Magento\Customer\Model\Session                           $customerSession
     * @param \Magento\Customer\Model\Customer                          $customer
     * @param \Magento\Framework\Stdlib\CookieManagerInterface          $cookieManager
     * @param \Plumrocket\SocialLoginFree\Model\Buttons\Factory         $buttonsFactory
     * @param \Plumrocket\SocialLoginFree\Api\TypesProviderInterface    $typesProvider
     * @param \Plumrocket\SocialLoginFree\Helper\Config                 $config
     * @param \Plumrocket\SocialLoginFree\Model\Account\Photo           $photo
     * @param \Plumrocket\SocialLoginFree\Model\Success\RedirectManager $successRedirectManager
     * @param \Plumrocket\SocialLoginFree\Model\Provider\Callback       $callback
     * @param \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager   $popupManager
     * @param \Plumrocket\Base\Model\Utils\Config                       $configUtils
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Plumrocket\SocialLoginFree\Model\Buttons\Factory $buttonsFactory,
        \Plumrocket\SocialLoginFree\Api\TypesProviderInterface $typesProvider,
        Config $config,
        Photo $photo,
        \Plumrocket\SocialLoginFree\Model\Success\RedirectManager $successRedirectManager,
        \Plumrocket\SocialLoginFree\Model\Provider\Callback $callback,
        \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager $popupManager,
        \Plumrocket\Base\Model\Utils\Config $configUtils
    ) {
        parent::__construct($objectManager, $context);
        $this->customerSession  = $customerSession;
        $this->customer         = $customer;
        $this->cookieManager    = $cookieManager;
        $this->_configSectionId = Information::CONFIG_SECTION;
        $this->buttonsFactory = $buttonsFactory;
        $this->typesProvider = $typesProvider;
        $this->config = $config;
        $this->photo = $photo;
        $this->successRedirectManager = $successRedirectManager;
        $this->callback = $callback;
        $this->popupManager = $popupManager;
        $this->configUtils = $configUtils;
    }

    /**
     * Check if module is enabled.
     *
     * @return bool
     * @deprecated since 3.2.0
     * @see \Plumrocket\SocialLoginFree\Helper\Config::isModuleEnabled
     */
    public function moduleEnabled()
    {
        return $this->config->isModuleEnabled();
    }

    public function validateIgnore()
    {
        return $this->configUtils->isSetFlag('psloginfree/general/validate_ignore');
    }

    public function getShareData()
    {
        return $this->configUtils->getStoreConfig('psloginfree/share');
    }

    public function shareEnabled()
    {
        return $this->config->isModuleEnabled() && $this->configUtils->isSetFlag('psloginfree/share/enable');
    }

    public function hasButtons()
    {
        if (! $this->config->isModuleEnabled()) {
            return false;
        }

        if ($this->customerSession->isLoggedIn()) {
            return false;
        }

        return (bool)$this->getButtons();
    }

    public function isGlobalScope()
    {
        return $this->customer->getSharingConfig()->isGlobalScope();
    }

    /**
     * @return array
     */
    public function getButtons()
    {
        if (null === $this->_buttons) {
            $this->_buttons = $this->buttonsFactory->create($this->typesProvider->getAll());
        }

        return $this->_buttons;
    }

    public function getCookieRefererLink()
    {
        return $this->cookieManager->getCookie(self::REFERER_QUERY_PARAM_NAME);
    }

    public function refererStore($value = false)
    {
        $prevValueByCustomer = $this->customerSession->getData(self::REFERER_STORE_PARAM_NAME);

        if ($value) {
            $this->customerSession->setData(self::REFERER_STORE_PARAM_NAME, $value);
        } elseif ($value === null) {
            $this->customerSession->unsetData(self::REFERER_STORE_PARAM_NAME);
        }

        return $prevValueByCustomer;
    }

    public function getRefererLinkSkipModules()
    {
        return ['customer/account', /*'checkout',*/ 'pslogin/account'];
    }

    /**
     * @param string $url
     * @return bool
     */
    public function isUrlInternal(string $url): bool
    {
        return stripos($url, 'http') === 0;
    }

    public function moduleInvitationsEnabled()
    {
        return false;
    }

    public function getCheckoutJsViewAuthentication()
    {
        if ($this->config->isModuleEnabled()
            && $this->configUtils->isSetFlag('psloginfree/general/replace_templates')
        ) {
            $viewFile = 'Plumrocket_SocialLoginFree/js/view/checkout/authentication';
        } else {
            $viewFile = 'Magento_Checkout/js/view/authentication';
        }

        return $viewFile;
    }

    public function getCheckoutJsViewFormElementEmail()
    {
        if ($this->config->isModuleEnabled()
            && $this->configUtils->isSetFlag('psloginfree/general/replace_templates')
        ) {
            $viewFile = 'Plumrocket_SocialLoginFree/js/view/checkout/form/element/email';
        } else {
            $viewFile = 'Magento_Checkout/js/view/form/element/email';
        }

        return $viewFile;
    }

    public function getCustomerJsViewAuthenticationPopup()
    {
        if ($this->config->isModuleEnabled()
            && $this->configUtils->isSetFlag('psloginfree/general/replace_templates')
        ) {
            $viewFile = 'Plumrocket_SocialLoginFree/js/view/customer/authentication-popup';
        } else {
            $viewFile = 'Magento_Customer/js/view/authentication-popup';
        }

        return $viewFile;
    }

    /**
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Helper\Config::isPhotoEnabled
     * @return bool
     */
    public function photoEnabled()
    {
        return $this->config->isPhotoEnabled();
    }

    /**
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Model\Account\Photo::getPhotoUrl
     * @param bool $checkIsEnabled
     * @param null $customerId
     * @return bool|string
     */
    public function getPhotoPath($checkIsEnabled = true, $customerId = null)
    {
        if ($checkIsEnabled && ! $this->config->isPhotoEnabled()) {
            return false;
        }

        if ($customerId === null) {
            if (! $this->customerSession->isLoggedIn()) {
                return false;
            }

            if (! $customerId = $this->customerSession->getCustomerId()) {
                return false;
            }
        } elseif (!is_numeric($customerId) || $customerId <= 0) {
            return false;
        }

        return $this->photo->getPhotoUrl($customerId);
    }

    /**
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Model\Success\RedirectManager::getRedirectsConfiguration
     *
     * @return array|string[]
     */
    public function getRedirect()
    {
        return $this->successRedirectManager->getRedirectsConfiguration();
    }

    /**
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Model\Provider\Callback::getUrl
     *
     * @param      $provider
     * @param bool $byRequest
     * @return string
     */
    public function getCallbackURL($provider, $byRequest = false)
    {
        return $this->callback->getUrl($provider, $byRequest);
    }

    /**
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Model\Frontend\PopupManager::showSharePopup
     *
     * @return bool
     */
    public function showPopup()
    {
        return $this->popupManager->showSharePopup();
    }

    /**
     * @param string $after
     * @return string
     *
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Model\Success\RedirectManager::getRedirectUrl
     */
    public function getRedirectUrl($after = 'login')
    {
        return $this->successRedirectManager->getRedirectUrl((string) $after);
    }

    /**
     * @deprecated
     * @see \Plumrocket\SocialLoginFree\Helper\Config::isDebugMode
     * @return bool
     */
    public function getDebugMode()
    {
        return $this->config->isDebugMode();
    }

    /**
     * @param null|string $email
     * @return bool
     */
    public function isFakeMail($email = null)
    {
        if (null === $email && $this->customerSession->isLoggedIn()) {
            $email = $this->customerSession->getCustomer()->getEmail();
        }

        return strpos($email, FakeEmail::FAKE_EMAIL_PREFIX) === 0;
    }
}
