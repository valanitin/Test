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
namespace Bss\SocialLogin\Helper;

/**
 * Class Data
 * @SuppressWarnings(PHPMD)
 * @package Bss\SocialLogin\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_SECURE_IN_FRONTEND = 'web/secure/use_in_frontend';
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $helperbackend;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpcontext;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlInterface;
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;
    /**
     * @var null
     */
    protected $buttons = null;
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * Data constructor.
     * @param \Magento\Backend\Helper\Data $helperbackend
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Http\Context $httpcontext
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Backend\Helper\Data $helperbackend,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Http\Context $httpcontext,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->customerUrl = $customerUrl;
        $this->customerSession = $customerSession;
        $this->customer = $customer;
        $this->helperbackend = $helperbackend;
        $this->storeManager = $storeManager;
        $this->httpcontext = $httpcontext;
        $this->filesystem = $filesystem;
        $this->urlInterface = $context->getUrlBuilder();
        parent::__construct($context);
    }

    /**
     * @param $path
     * @param null $store
     * @param null $scope
     * @return mixed
     */
    public function getConfig($path, $store = null, $scope = null)
    {
        if ($scope === null) {
            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        }
        return $this->scopeConfig->getValue($path, $scope, $store);
    }

    /**
     * @return string
     */
    public function getConfigSectionId()
    {
        return 'bss_sociallogin';
    }

    /**
     * @return bool
     */
    public function moduleEnabled()
    {
        return (bool)$this->getConfig('bss_sociallogin/general/enable');
    }

    /**
     * @return bool
     */
    public function popupEnabled()
    {
        return (bool)$this->getConfig('bss_sociallogin/general/popup_ajax');
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return (bool)$this->getConfig(self::XML_PATH_SECURE_IN_FRONTEND);
    }

    /**
     * @return mixed
     */
    public function displayPopup()
    {
        return $this->getConfig('bss_sociallogin/button_social/display_at_popup');
    }

    /**
     * @return array
     */
    public function displayButtonLogin()
    {
        return explode(',', $this->getConfig('bss_sociallogin/button_social/login_byclass'));
    }

    /**
     * @return array
     */
    public function displayButtonRegister()
    {
        return explode(',', $this->getConfig('bss_sociallogin/button_social/register_byclass'));
    }

    /**
     * @return mixed
     */
    public function showLimit()
    {
        return $this->getConfig('bss_sociallogin/button_social/show_limit');
    }

    /**
     * @return bool
     */
    public function showPassword()
    {

        return $this->moduleEnabled() && $this->getConfig('bss_sociallogin/general/send_password');
    }

    /**
     * @return bool
     */
    public function photoEnabled()
    {
        return $this->moduleEnabled() && $this->getConfig('bss_sociallogin/general/enable_photo');
    }

    /**
     * @param $path
     * @return bool
     */
    public function recaptcha($path)
    {
        $recaptcha = explode(',', $this->getConfig('bss_sociallogin/recaptcha/form_apply'));
        return in_array($path, $recaptcha);
    }

    /**
     * @return mixed
     */
    public function getSiteKey()
    {
        return $this->getConfig('bss_sociallogin/recaptcha/site_key');
    }

    /**
     * @return mixed
     */
    public function getSecretKey()
    {
        return $this->getConfig('bss_sociallogin/recaptcha/secret_key');
    }

    /**
     * @return mixed
     */
    public function getTheme()
    {
        return $this->getConfig('bss_sociallogin/recaptcha/theme');
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->getConfig('bss_sociallogin/recaptcha/type');
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->getConfig('bss_sociallogin/recaptcha/size');
    }

    /**
     * @return bool
     */
    public function checkLogin()
    {
        if ($this->httpcontext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
            return false;
        }

        if ($this->customerSession->isLoggedIn()) {
            return false;
        }

        return true;
    }

    /**
     * @param bool $checkIsEnabled
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPhotoPath($checkIsEnabled = true)
    {
        if ($checkIsEnabled && !$this->photoEnabled()) {
            return false;
        }

        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }
        if (!$customerId = $this->customerSession->getCustomerId()) {
            return false;
        }
        $directoryRead = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = 'bss_sociallogin' . DIRECTORY_SEPARATOR . 'photo' . DIRECTORY_SEPARATOR . $customerId . '.png';
        $pathUrl = $this->storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) .
            'bss_sociallogin/photo/' . $customerId . '.png';

        if (!$directoryRead->isExist($path)) {
            return false;
        }

        return $pathUrl;
    }

    /**
     * @return bool
     */
    public function isGlobalScope()
    {
        return $this->customer->getSharingConfig()->isGlobalScope();
    }

    public function getCallbackURL($provider, $byRequest = false)
    {
        $request = $this->_getRequest();
        $websiteCode = $request->getParam('website');
        $defaultStoreId = $this->storeManager
            ->getWebsite($byRequest ? $websiteCode : null)
            ->getDefaultGroup()
            ->getDefaultStoreId();

        if (!$defaultStoreId) {
            $websites = $this->storeManager->getWebsites(true);
            foreach ($websites as $website) {
                $defaultStoreId = $website
                    ->getDefaultGroup()
                    ->getDefaultStoreId();

                if ($defaultStoreId) {
                    break;
                }
            }
        }

        if (!$defaultStoreId) {
            $defaultStoreId = 1;
        }

        $url = $this->storeManager->getStore($defaultStoreId)
            ->getUrl('sociallogin/account/login', ['type' => $provider, 'key' => '', '_nosid' => true]);

        $url = str_replace($this->helperbackend->getAreaFrontName() . '/', '', $url);

        if (false!==($length = stripos($url, '?'))) {
            $url = substr($url, 0, $length);
        }

        if ($byRequest) {
            if ($this->getConfig('web/seo/use_rewrites')) {
                $url = str_replace('index.php/', '', $url);
            }
        }

        return $url;
    }

    /**
     * @param bool $onlyEnabled
     * @return array
     */
    public function getTypes($onlyEnabled = true)
    {
        $groups = $this->getConfig('bss_sociallogin');
        unset($groups['general']);
        unset($groups['recaptcha']);
        unset($groups['button_social']);
        $types = [];
        if (!empty($groups)) {
            foreach ($groups as $name => $fields) {
                if ($onlyEnabled && empty($fields['enable'])) {
                    continue;
                }
                $types[] = $name;
            }
        }
        return $types;
    }

    /**
     * @return array|null
     */
    public function getButtons()
    {
        if (null===$this->buttons) {
            $types = $this->getTypes(false);

            $this->buttons = [];
            foreach ($types as $type) {
                $type = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get('Bss\SocialLogin\Model\\' . ucfirst($type));

                if ($type->enabled()) {
                    $button = $type->getButton();
                    $this->buttons[$button['type']] = $button;
                }
            }
        }
        return $this->buttons;
    }

    /**
     * @return array
     */
    public function getPreparedButtons()
    {
        $buttonsPrepared = [];
        $buttons = $this->getButtons();

        $storeName = $this->_getRequest()->getParam('store');
        $sortableString = $this->getConfig('bss_sociallogin/button_social/sort_bss_sociallogin', $storeName);
        $sortable = null;
        parse_str($sortableString, $sortable);

        if (is_array($sortable)) {
            if (isset($sortable['sort'])) {
                foreach ($sortable['sort'] as $button) {
                    if (isset($buttons[$button])) {
                        $buttonsPrepared[$button] = $buttons[$button];
                        unset($buttons[$button]);
                    }
                }
            }
        }
        return array_merge($buttonsPrepared, $buttons);
    }

    /**
     * @param string $path
     * @return mixed|string
     */
    public function getRedirectUrl($path = 'login')
    {
        $redirectUrl = $this->getConfig('bss_sociallogin/general/redirect_for_' . $path);
        if ($redirectUrl=='custom') {
            $redirectUrl = $this->getConfig('bss_sociallogin/general/redirect_for_' . $path . '_link');
        }

        if ($redirectUrl=='url_dashboard') {
            $redirectUrl = $this->customerUrl->getDashboardUrl();
        }
        if ($redirectUrl=='url_shoppingcart') {
            $redirectUrl = $this->urlInterface->getUrl('checkout/cart');
        }
        if ($redirectUrl=='url_checkoutpage') {
            $redirectUrl = $this->urlInterface->getUrl('checkout');
        }
        return $redirectUrl;
    }
}
