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
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GeoIPAutoSwitchStore\Plugin;

use Magento\Store\Model\StoreManagerInterface;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\NewRelicReporting\Model\Config;
use Magento\NewRelicReporting\Model\NewRelicWrapper;
use Psr\Log\LoggerInterface;

/**
 * Class SwitchStore
 * @package Bss\GeoIPAutoSwitchStore\Plugin
 */
class SwitchStore extends \Magento\NewRelicReporting\Plugin\StatePlugin
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData
     */
    private $geoIpHelper;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $reponseHttp;

    /**
     * @var int
     */
    protected $currentStoreId = 0;

    /**
     * SwitchStore constructor.
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Response\Http $reponseHttp
     * @param \Magento\Framework\UrlInterface $url
     * @param \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper
     * @param \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData $geoIpHelper
     * @param Config $config
     * @param NewRelicWrapper $newRelicWrapper
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $reponseHttp,
        \Magento\Framework\UrlInterface $url,
        \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper,
        \Bss\GeoIPAutoSwitchStore\Helper\GeoIPData $geoIpHelper,
        Config $config,
        NewRelicWrapper $newRelicWrapper,
        LoggerInterface $logger
    ) {
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        $this->url = $url;
        $this->geoIpHelper = $geoIpHelper;
        $this->request = $request;
        $this->reponseHttp = $reponseHttp;
        parent::__construct($config, $newRelicWrapper, $logger);
    }

    /**
     * Do redirect by module
     * Mix plugin framework-state-newrelic
     *
     * @param \Magento\Framework\App\State $subject
     * @param mixed $result
     * @return $this|bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function afterSetAreaCode(
        \Magento\Framework\App\State $subject,
        $result
    ) {
        // Do module Magento_NewRelicReporting business
        parent::afterSetAreaCode($subject, $result);

        // is Frontend area?
        if ($subject->getAreaCode() !== 'frontend' ||
            !$this->dataHelper->getEnableModule() ||
            $this->request->isAjax()) {
            return $result;
        }

        //Get IP for Tester from Param URL
        $ipForTester = $this->request->getParam('ipTester');
        //Get IP Customer.
        $ipCustomer = $this->dataHelper->getIpCustomer($ipForTester);

        // If customer IP on restricted list, finish
        // Get Ip not Redirects (Array), if this IP is current Customer IP then not Redirects
        if ($this->geoIpHelper->isRestrictedIP($ipCustomer)) {
            return $result;
        }

        // This is Status Block config
        $enableBlackList = $this->dataHelper->getEnableBlackList($this->getStoreId());
        //Get Country List, if Current Country of Customer on this Country list then Not redirects

        if ($enableBlackList && $this->geoIpHelper->isIpBlocked($ipCustomer)) {
            $this->redirectToBlockUrl();
        }
        //Get Bot as Google Bot, Yahoo..., Not redirects it.
        $userBots = $this->dataHelper->restrictionUserAgent($this->getStoreId());
        $countUserBot = $this->geoIpHelper->countBot($userBots);
        if ($countUserBot > 0) {
            return $result;
        }
        //Get Enable Cookie
        $enableCookie = $this->dataHelper->getEnableCookie($this->getStoreId());

        //Now, handle Redirects With Country Code
        return $this->handleSwitchStoreByCountry($ipCustomer, $ipForTester, $enableCookie, $enableBlackList);
    }

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @param string $redirectScope
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setStore($store, $redirectScope)
    {
        $currentStoreView = ',' . $store->getId() . ',';
        if ($redirectScope == 'website') {
            $storeViewIdScope = ',' . $this->getStoreIdFromWebsite();
            if (strpos($storeViewIdScope, $currentStoreView) === false) {
                return false;
            }
        }
        if ($redirectScope == 'store') {
            $storeViewIdScope = ',' . $this->getStoreIdFromGroup();
            if (strpos($storeViewIdScope, $currentStoreView) === false) {
                return false;
            }
        }

        $this->storeManager->setCurrentStore($store);
        //fix error cache page when switch store view manual
        $this->geoIpHelper->getCookieGeoIP()->setUrl($store->getBaseUrl(), 86400);
        return $store->getBaseUrl();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function redirectToBlockUrl()
    {
        $blockUrl = trim($this->dataHelper->getUrlBlackList($this->getStoreId()));
        if (stripos($blockUrl, 'http') === false) {
            $blockUrl = $this->url->getUrl($blockUrl);
        }

        $currentUrl = $this->storeManager->getStore()->getCurrentUrl(false);
        if (strpos($currentUrl, $blockUrl) === false) {
            return $this->reponseHttp->setRedirect($blockUrl);
        }
    }

    /**
     * @param string $ipCustomer
     * @param string $ipForTester
     * @param string $enableCookie
     * @param string $enableBlackList
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handleSwitchStoreByCountry($ipCustomer, $ipForTester, $enableCookie, $enableBlackList)
    {
        // If not testing, check if we have country code already (from session or cookie)
        $countryCode = null;

        if (!$ipForTester) {
            $countryCode = $this->geoIpHelper->getSession()->getCountryCode();

            if (!$countryCode) {
                $countryCode = $this->geoIpHelper->getCookieGeoIP()->get();
            }
        }

        // If no country code yet, get it
        if (null == $countryCode) {
            $countryCode = $this->dataHelper->getCountryCodeFromIp($ipCustomer);
        }

        // Now if we have a country code, check if we need to switch
        if ($countryCode) {
            //Save Country Code to Cookie If Config Enable
            $this->geoIpHelper->getCookieGeoIP()
                ->saveCountryCookie($countryCode, $enableCookie, $ipForTester, $this->getStoreId());

            // Check for blocked countries
            if ($enableBlackList) {
                if ($this->redirectCountryBlackList($countryCode)) {
                    return true;
                }
            }
            // Instigate redirect
            $this->redirectFunction($countryCode);
        }

        return true;
    }

    /**
     * @param string $countryCode
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function redirectCountryBlackList($countryCode)
    {
        $storeId = $this->getStoreId();
        $countryStoreBlackList = $this->dataHelper->getCountriesBlackList($storeId);
        $countryStoreBlackList = explode(',', $countryStoreBlackList);
        if (in_array($countryCode, $countryStoreBlackList)) {
            $this->redirectToBlockUrl();
        }
        return false;
    }

    /**
     * @param string $countryCode
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function redirectFunction($countryCode)
    {
        // Find store for country code and switch
        // $codeKey = false, store array keys are storeId, can use array_sort function
        $stores = $this->storeManager->getStores(false, false);
        asort($stores);

        // 1. Get Popup status, if it Enable, stop
        $popupStatus = $this->handlePopupStatus($countryCode);

        // 2. Check Stop Redirects Status
        $stopRedirectStatus = $this->checkStopRedirect($countryCode, $stores);

        // 3. get Is Restriction Url
        $restrictionUrl = $this->geoIpHelper->isRestrictionUrl($this->request);

        //Handle Redirects
        if ($stopRedirectStatus) {
            $this->handleRedirectCurrency($countryCode, $popupStatus);
        }

        // If 1 in 3, then STOP it
        if ($popupStatus || $stopRedirectStatus || $restrictionUrl) {
            return $this;
        }

        //Now, Redirects

        //Get Redirects Scope to Redirects
        $redirectScope = $this->dataHelper->getRedirectsScope($this->getStoreId());
        $redirectStoreFirstVisitUrl = null;

        foreach ($stores as $store) {
            $countryStore = $this->dataHelper->getCountries($store->getId());
            if (strpos($countryStore, $countryCode) !== false && $store->isActive()) {
                //Set Redirects if it Correct.
                if (!$this->setStore($store, $redirectScope)) {
                    continue;
                }
                $redirectStoreFirstVisitUrl = $this->setStore($store, $redirectScope);
                break;
            }
        }
        if ($redirectStoreFirstVisitUrl) {
            return $this->reponseHttp->setRedirect($redirectStoreFirstVisitUrl);
        }

        $this->handleRedirectCurrency($countryCode, $popupStatus);
        return $this;
    }

    /**
     * @param string $countryCode
     * @param bool $popupStatus
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handleRedirectCurrency($countryCode, $popupStatus)
    {
        //Return False if have Cookie or Popup is Enable
        if (($this->geoIpHelper->getCookieGeoIP()->getCurrency() &&
                $this->geoIpHelper->getCookieGeoIP()->getCurrency() != null) || $popupStatus) {
            return false;
        }

        $this->handleCurrencySwitch($countryCode);

        $originalPath = $this->request->getOriginalPathInfo();
        if (strpos($originalPath, 'stores/store/redirect') !== false) {
            return false;
        }

        if (strpos($originalPath, 'stores/store/switch') !== false) {
            return false;
        }

        return true;
    }

    /**
     * @param string $countryCode
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handlePopupStatus($countryCode)
    {
        //Check Config Popup Status
        $popupStatus = $this->dataHelper->getPopupStatus($this->getStoreId());

        if ($popupStatus) {
            //Save Remember Popup if Client click to Switch in Popup
            $isFromPopup = $this->request->getPost('is_from_popup');
            if ($isFromPopup == 'true') {
                $this->geoIpHelper->getCookieGeoIP()->setRememberPopup($countryCode);
                $this->handleCurrencySwitch($countryCode);
            }

            //Get Remember Popup Status
            $rememberPopupStatus = $this->geoIpHelper->getCookieGeoIP()->getRememberPopup();
            $isRememberPopup = $this->dataHelper->isRememberPopupStatus();

            //If is The second time, redirects
            if ($rememberPopupStatus && $isRememberPopup) {
                return false;
            }
            return true;
        }

        return false;
    }

    /**
     * @param string $countryCode
     * @param array $stores
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function checkStopRedirect($countryCode, $stores)
    {
        $originalPath = $this->request->getOriginalPathInfo();
        // When  switching store, the following two URLs are invoked, ignore.
        if (strpos($originalPath, 'stores/store/redirect') !== false) {
            return true;
        }
        if (strpos($originalPath, 'stores/store/switch') !== false) {
            return true;
        }

        $currentStoreId = $this->getStoreId();

        $countryStoreId = null;
        $storeFinal = null;
        // Find store for country code and switch
        foreach ($stores as $store) {
            $countryStore = $this->dataHelper->getCountries($store->getId());
            if (strpos($countryStore, $countryCode) !== false && $store->isActive()) {
                $storeFinal = $store;
                $countryStoreId = $store->getId();
                break;
            }
        }
        if ($currentStoreId == $countryStoreId) {
            //Save Cookie for Fisrt Redirect
            $this->geoIpHelper->getCookieGeoIP()->setUrl($storeFinal->getBaseUrl(), 86400);
            return true;
        }

        return $this->checkRedirectWithConfigAllowSwitchStore();
    }

    /**
     * {@inheritdoc}
     */
    protected function getStoreId()
    {
        if ($this->currentStoreId == 0) {
            $this->currentStoreId = $this->storeManager->getStore()->getId();
        }
        return $this->currentStoreId;
    }

    /**
     * @return bool
     */
    protected function checkRedirectWithConfigAllowSwitchStore()
    {
        // Get Allow Switch Store Config. If Allow Switch Store = NO, then User cant Switch Store.
        // Switch Store ON Website with URL Code and non-URL Code
        $allowSwitchStore = $this->dataHelper->getAllowSwitch($this->getStoreId());

        // If allowing switching, check for reasons not to switch
        if ($allowSwitchStore) {
            // Is this a default URL controlled by store settings
            $currentUrl = $this->url->getCurrentUrl();
            $isDefaultUrl = $this->geoIpHelper->isDefaultUrl($currentUrl);
            $isFirstTimeGeoIP = $this->geoIpHelper->getCookieGeoIP()->getUrl();
            //If allow Switch Store, return True to Stop Redirect.
            //But it is Default URL, then Redirect it
            if ($isDefaultUrl || !$isFirstTimeGeoIP) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreIdFromWebsite()
    {
        $websiteId = $this->storeManager->getStore()->getWebsiteId();
        $storesView = $this->geoIpHelper
            ->getGroupFactory()
            ->create()
            ->getCollection()
            ->addFieldToFilter('website_id', $websiteId);
        $storeViewId = '';

        foreach ($storesView as $storeView) {
            foreach ($storeView->getStores() as $myStore) {
                $storeViewId = $storeViewId . $myStore->getId() . ',';
            }
        }
        return $storeViewId;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getStoreIdFromGroup()
    {
        $groupId = $this->storeManager->getStore()->getGroupId();
        $storesView = $this->geoIpHelper
            ->getGroupFactory()
            ->create()
            ->getCollection()
            ->addFieldToFilter('group_id', $groupId);
        $storeViewId = '';

        foreach ($storesView as $storeView) {
            foreach ($storeView->getStores() as $myStore) {
                $storeViewId = $storeViewId . $myStore->getId() . ',';
            }
        }
        return $storeViewId;
    }

    /**
     * @param string $countryCode
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    protected function handleCurrencySwitch($countryCode)
    {
        $this->geoIpHelper->getCookieGeoIP()->setCurrency('NONE', 86400);
        // check currency
        $currencyCode = $this->geoIpHelper->getCurrencyByCountryCode($countryCode);
        $enableSwitchCurrency = $this->dataHelper->isAutoSwitchCurrency();
        // set currency
        if ($currencyCode && $enableSwitchCurrency) {
            $this->geoIpHelper->getCookieGeoIP()->setCurrency($currencyCode, 86400);
            $this->storeManager->getStore()->setCurrentCurrencyCode($currencyCode);
        }
        return true;
    }
}
