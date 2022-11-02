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
namespace Bss\GeoIPAutoSwitchStore\Helper;

class GeoIPData
{
    const XML_PATH_CURRENCY_ALLOW = 'currency/options/allow';

    /**
     * @var Data
     */
    public $dataHelper;

    /**
     * @var \Magento\Framework\Session\GenericFactory
     */
    public $session;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Cookie\GeoIp
     */
    public $cookieGeoIp;

    /**
     * @var \Magento\Store\Model\GroupFactory
     */
    private $groupFactory;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\UrlRewriteFactory
     */
    protected $urlRewriteFactory;

    /**
     * GeoIPData constructor.
     * @param \Magento\Framework\Session\GenericFactory $session
     * @param \Bss\GeoIPAutoSwitchStore\Cookie\GeoIp $cookieGeoIp
     * @param Data $dataHelper
     * @param \Magento\Store\Model\GroupFactory $groupFactory
     * @param \Bss\GeoIPAutoSwitchStore\Model\UrlRewriteFactory $urlRewriteFactory
     */
    public function __construct(
        \Magento\Framework\Session\GenericFactory $session,
        \Bss\GeoIPAutoSwitchStore\Cookie\GeoIp $cookieGeoIp,
        \Bss\GeoIPAutoSwitchStore\Helper\Data $dataHelper,
        \Magento\Store\Model\GroupFactory $groupFactory,
        \Bss\GeoIPAutoSwitchStore\Model\UrlRewriteFactory $urlRewriteFactory
    ) {
        $this->dataHelper = $dataHelper;
        $this->session = $session;
        $this->cookieGeoIp = $cookieGeoIp;
        $this->groupFactory = $groupFactory;
        $this->urlRewriteFactory = $urlRewriteFactory;
    }

    /**
     * @param string $countryCode
     * @return String | bool
     */
    public function getCurrencyByCountryCode($countryCode)
    {
        $codes = $this->dataHelper->getCodes();

        $additionalData = json_decode($codes, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $additionalData = array_values($additionalData);
        foreach ($additionalData as $item) {
            if ($item['country_code'] == $countryCode) {
                return $item['currency_code'];
            }
        }
        return false;
    }

    /**
     * @return string
     */
    public function getCountSwitch()
    {
        $countRedirects = $this->session->create()->getCountSwitch();
        if ($countRedirects) {
            return $countRedirects;
        } else {
            return 0;
        }
    }

    /**
     * @param string $currentPath
     * @param string $url
     * @param null $currentStore
     * @param null $targetStore
     * @return string
     */
    public function getCurrentPath($currentPath, $url, $currentStore = null, $targetStore = null)
    {
        $currentPathAfter = $currentPath;
        $originalPath = trim($currentPath, "/");

        if ($currentStore && $targetStore && $originalPath) {
            $currentPathAfter = $this->getUrlObjectFromPath($originalPath, $currentStore, $targetStore);
        }

        $currentPath = ltrim($currentPath, '/');
        $currentPathAfter = ltrim($currentPathAfter, '/');

        if ($currentPath !== null && $currentPath !== '') {
            $paramPath = strstr($url, "?");
            if ($paramPath) {
                $currentPath = $currentPathAfter.$paramPath;
            } else {
                $currentPath = $currentPathAfter;
            }
        } else {
            $currentPath = strstr($url, '?');
        }
        return $currentPath;
    }

    /**
     * @param string $requestPath
     * @param string  $requestStoreId
     * @param string $targetStoreId
     * @return mixed
     */
    public function getUrlObjectFromPath($requestPath, $requestStoreId, $targetStoreId)
    {
        $collection = $this->urlRewriteFactory->create()
            ->getCollection()
            ->addFieldToFilter('request_path', $requestPath)
            ->addFieldToFilter('store_id', $requestStoreId);

        if ($collection->getData()) {
            $collectionData = $collection->getData()[0];
            $targetPath = $collectionData['target_path'];
            $targetCollection = $this->urlRewriteFactory->create()
                ->getCollection()
                ->addFieldToFilter('target_path', $targetPath)
                ->addFieldToFilter('store_id', $targetStoreId);

            if ($targetCollection->getData()) {
                $targetCollectionData = $targetCollection->getData()[0];
                return $targetCollectionData['request_path'];
            }
        }
        return $requestPath;
    }

    /**
     * @return \Magento\Store\Model\GroupFactory
     */
    public function getGroupFactory()
    {
        return $this->groupFactory;
    }

    /**
     * @param string $currentUrl
     * @return bool
     */
    public function isDefaultUrl($currentUrl)
    {
        //Default Redirect URLs is Url allways redirects.
        $count = 0;
        //Get config Url Redirects Default.
        $defaultUrl = $this->dataHelper->getDefaultRedirect($this->dataHelper->getStoreId());

        if ($defaultUrl != null && $defaultUrl != '') {
            $urlArray = explode("\n", $defaultUrl);

            $currentUrl = ltrim($currentUrl, '/');
            $currentUrl = rtrim($currentUrl, '/');

            foreach ($urlArray as $myUrl) {
                $myUrl = rtrim($myUrl, "\n");
                $myUrl = rtrim($myUrl, "\r");
                $myUrl = rtrim($myUrl, " ");
                $myUrl = ltrim($myUrl, "\n");
                $myUrl = ltrim($myUrl, "\r");
                $myUrl = ltrim($myUrl, ' ');
                $myUrl = ltrim($myUrl, '/');
                if ($myUrl == $currentUrl) {
                    $count++;
                }
            }
        }

        //If current URL is'nt Default URL, return False.
        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param \Magento\Framework\App\Request\Http $request
     * @return bool
     */
    public function isRestrictionUrl($request)
    {
        $count = 0;
        //Get config Url Disable
        $restrictionUrl = $this->dataHelper->restrictionUrl($this->dataHelper->getStoreId());

        if ($restrictionUrl != null && $restrictionUrl != '') {
            $urlArray = explode("\n", $restrictionUrl);

            $currentPath = $request->getOriginalPathInfo();
            $currentPath = ltrim($currentPath, '/');
            $currentPath = rtrim($currentPath, '/');
            if (strpos($currentPath, '?') !== false) {
                $currentPath = strstr($currentPath, '?', true);
            }

            foreach ($urlArray as $myUrl) {
                $myUrl = rtrim($myUrl, "\n");
                $myUrl = rtrim($myUrl, "\r");
                $myUrl = rtrim($myUrl, " ");
                $myUrl = ltrim($myUrl, "\n");
                $myUrl = ltrim($myUrl, "\r");
                $myUrl = ltrim($myUrl, ' ');
                $myUrl = ltrim($myUrl, '/');
                if ($myUrl == $currentPath || ($myUrl != '' && strpos($currentPath, $myUrl) !== false)) {
                    $count++;
                }
            }
        }

        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param string $ipCustomer
     * @return int
     */
    public function isRestrictedIP($ipCustomer)
    {
        $restrictionIps = $this->dataHelper->restrictionIp($this->dataHelper->getStoreId());
        if (!empty($restrictionIps)) {
            $ipList = array_map('trim', explode("\n", $restrictionIps));
            return in_array($ipCustomer, $ipList);
        }
        return false;
    }

    /**
     * Checks if customer IP is blocked
     *
     * @param string $ipCustomer
     * @return int
     */
    public function isIpBlocked($ipCustomer)
    {
        $ipBlocks = $this->dataHelper->getIpBlackList($this->dataHelper->getStoreId());
        if (!empty($ipBlocks)) {
            $ipList = array_map('trim', explode("\n", $ipBlocks));
            return in_array($ipCustomer, $ipList);
        }
        return false;
    }

    /**
     * @param string $countryCode
     * @param string $enableCookie
     * @param string $ipForTester
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function saveCountryCookie($countryCode, $enableCookie, $ipForTester)
    {
        $this->session->create()->setCountryCode($countryCode);

        if ($enableCookie == '1' && $ipForTester == null && $countryCode) {
            $timeCookie = (int)$this->dataHelper->getTimeCookie($this->dataHelper->getStoreId());
            $timeCookie = $timeCookie*24*60*60;
            //Set Cookie Country Code to Browser Customer
            $this->cookieGeoIp->set($countryCode, $timeCookie);

            return $this;
        } else {
            return $this;
        }
    }

    /**
     * @param string $userBots
     * @return int
     */
    public function countBot($userBots)
    {
        $countUserBot = 0;
        $http_user_agent = $this->dataHelper->returnHttpUserAgent();
        if ($userBots != null && $userBots != '' && (bool)$http_user_agent) {
            $userBots = explode(',', $userBots);
            foreach ($userBots as $userBot) {
                $userBot = rtrim($userBot, ' ');
                $userBot = ltrim($userBot, ' ');
                if ($userBot == 'Google') {
                    if (strpos(strtolower($http_user_agent), 'GOOGLE') !== false) {
                        $countUserBot++;
                    }
                }
                if (strstr(strtolower($http_user_agent), strtolower($userBot))) {
                    $countUserBot++;
                }
            }
        }

        return $countUserBot;
    }

    /**
     * @return \Bss\GeoIPAutoSwitchStore\Cookie\GeoIp
     */
    public function getCookieGeoIP()
    {
        return $this->cookieGeoIp;
    }

    /**
     * @return \Magento\Framework\Session\GenericFactory
     */
    public function getSession()
    {
        return $this->session->create();
    }
}
