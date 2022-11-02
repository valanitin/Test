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

use Magento\Store\Model\ScopeInterface;
use Bss\GeoIPAutoSwitchStore\Model\Config\Source\TimeCookie;
use Symfony\Component\Console\Exception\LogicException;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PARAM_NAME = '___store';
    const PARAM_NAME_URL_ENCODED = 'uenc';

    /**
     * @var string
     */
    public $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\GeoIpMaxMindFactory
     */
    private $geoIpMaxMind;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\LocationsMaxMindFactory
     */
    private $locationsMaxMind;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    public $postDataHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Api\StoreCookieManagerInterface
     */
    public $storeCookieManager;

    /**
     * @var \Bss\GeoIPAutoSwitchStore\Model\GeoIpMaxMindIPv6Factory
     */
    private $geoIpMaxMindIPv6;
    
    const GEOIP_ENABLE = 'bss_geoip/general/enable';
    const POPUP = 'bss_geoip/general/popup';
    const MESSAGE = 'bss_geoip/general/popup_message';
    const BUTTON = 'bss_geoip/general/popup_button';
    const GEOIP_COUNTRIES = 'bss_geoip/general/country';
    const GEOIP_URL = 'bss_geoip/general/restriction_url';
    const ENABLE_SWITCH_WEBSITE = 'bss_geoip/general/enable_switch_website';
    const RESTRICTION_USER_AGENT = 'bss_geoip/general/restriction_user_agent';
    const RESTRICTION_IP = 'bss_geoip/general/restriction_ip';
    const DEFAULT_REDIRECT = 'bss_geoip/general/default_redirect';
    const GEOIP_COUNTRIES_BLACK_LIST = 'bss_geoip/black_list/country';
    const GEOIP_URL_BLACK_LIST = 'bss_geoip/black_list/url';
    const GEOIP_ENABLE_BLACK_LIST = 'bss_geoip/black_list/enable';
    const GEOIP_IP_BLACK_LIST = 'bss_geoip/black_list/ip';
    const GEOIP_ALLOW_SWITCH = 'bss_geoip/general/allow_switch';
    const GEOIP_IP_FOR_TESTER = 'bss_geoip/general/tester_ip';
    const GEOIP_TIME_COOKIE = 'bss_geoip/general/time_cookie';
    const GEOIP_REDIRECT_SCOPE = 'bss_geoip/general/redirect_scope';
    const GEOIP_ENABLE_COOKIE = 'bss_geoip/general/enable_cookie';
    const GEOIP_URL_CUSTOM = 'bss_geoip_update/update/file_url';
    const GEOIP_FILE_CUSTOM = 'bss_geoip_update/update/file_upload';
    const GEOIP_URL_CUSTOM_IPV6 = 'bss_geoip_update/update_ipv6/file_url_ipv6';
    const GEOIP_FILE_CUSTOM_IPV6 = 'bss_geoip_update/update_ipv6/file_upload_ipv6';
    const WEB_URL_USE_STORE = 'web/url/use_store';

    const XML_PATH_BSS_AUTOSW_CODES = 'bss_geoip_currency/currency/codes';
    const XML_PATH_BSS_AUTOSW_ENABLE = 'bss_geoip_currency/currency/enable';

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Bss\GeoIPAutoSwitchStore\Model\GeoIpMaxMindFactory $geoIpMaxMind
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Bss\GeoIPAutoSwitchStore\Model\GeoIpMaxMindIPv6Factory $geoIpMaxMindIPv6
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Store\Api\StoreCookieManagerInterface $storeCookieManager
     * @param \Bss\GeoIPAutoSwitchStore\Model\LocationsMaxMindFactory $locationsMaxMind
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Bss\GeoIPAutoSwitchStore\Model\GeoIpMaxMindFactory $geoIpMaxMind,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Bss\GeoIPAutoSwitchStore\Model\GeoIpMaxMindIPv6Factory $geoIpMaxMindIPv6,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\StoreCookieManagerInterface $storeCookieManager,
        \Bss\GeoIPAutoSwitchStore\Model\LocationsMaxMindFactory $locationsMaxMind
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->geoIpMaxMind = $geoIpMaxMind;
        $this->geoIpMaxMindIPv6 = $geoIpMaxMindIPv6;
        $this->locationsMaxMind = $locationsMaxMind;
        $this->storeCookieManager = $storeCookieManager;
        $this->postDataHelper = $postDataHelper;
    }

    /**
     * @return mixed
     */
    public function returnHttpUserAgent()
    {
        return $this->_request->getServer('HTTP_USER_AGENT');
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    /**
     * {@inheritdoc}
     */
    protected function getStoreCode()
    {
        return $this->storeManager->getStore()->getCode();
    }

    /**
     * @param string $ipCustomer
     * @return mixed|null
     */
    public function getCountryCodeFromIp($ipCustomer = null)
    {
        if ($ipCustomer == null) {
            $ipCustomer = $this->getIpCustomer();
        }
        $dataCollection = null;
        //Check if IPv4
        if (filter_var($ipCustomer, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $ipArray = explode('.', $ipCustomer);
            $ipCustomerLong = (16777216*$ipArray[0])+(65536*$ipArray[1])+(256*$ipArray[2] )+$ipArray[3];
            //Ip of Customer convert to Long Ip
            $collection = $this->geoIpMaxMind->create()
            ->getCollection()
            ->addFieldToFilter(
                'begin_ip',
                ['lteq' => $ipCustomerLong]
            )->addFieldToFilter('end_ip', ['gteq' => $ipCustomerLong]);
            $dataCollection = $collection->getData();
        } elseif (filter_var($ipCustomer, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $ipv6Long = $this->ipv6ToLong($ipCustomer);
            $collection = $this->geoIpMaxMindIPv6->create()
            ->getCollection()
            ->addFieldToFilter(
                'begin_ip',
                ['lteq' => $ipv6Long]
            )->addFieldToFilter('end_ip', ['gteq' => $ipv6Long]);
            $dataCollection = $collection->getData();
        }
        $countryCode = $this->getCountryByIpInfo($ipCustomer, $dataCollection);
        return $countryCode;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @param string $currentUrl
     * @return string
     */
    public function getTargetStorePostData(\Magento\Store\Model\Store $store, $currentUrl)
    {
        $url = $store->getBaseUrl().'stores/store/switch/'.self::PARAM_NAME.'/'.$store->getCode().'/___from_store/';
        $url .= $this->storeManager->getStore()->getCode().'/'.self::PARAM_NAME_URL_ENCODED.'/';
        $url .= $this->urlEncoder->encode($currentUrl).'/is_from_popup/true';
        return $url;
    }

    /**
     * @return \Magento\Store\Api\StoreCookieManagerInterface
     */
    public function getStoreCookieManager()
    {
        return $this->storeCookieManager;
    }

    /**
     * @param string $ipCustomer
     * @param null|array $dataCollection
     * @return mixed|null
     */
    protected function getCountryByIpInfo($ipCustomer, $dataCollection)
    {
        $countryCode = null;

        if ($dataCollection) {
            $network = $dataCollection[0]['geoname_id'];
            $collection = $this->locationsMaxMind->create()
                ->getCollection()
                ->addFieldToFilter(
                    'geoname_id',
                    ['eq' => $network]
                )
                ->addFieldToFilter(
                    'locale_code',
                    ['eq' => 'en']
                );
            $locationCollection = $collection->getData();
            if (isset($locationCollection[0])) {
                $countryCode = $locationCollection[0]['country_iso_code'];
            }
        } else {
            try {
                // @codingStandardsIgnoreStart
                $url = 'http://ip-api.com/json/'.$ipCustomer;
                $timeout = 5;
                $url = str_replace("&amp;", "&", urldecode(trim($url)));
                $ch = curl_init();
                curl_setopt(
                    $ch,
                    CURLOPT_USERAGENT,
                    "Mozilla/5.0 (Windows; U; Windows NT 5.1; rv:1.7.3) Gecko/20041001 Firefox/0.10.1"
                );
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_ENCODING, "");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_AUTOREFERER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
                $response = curl_exec($ch);
                curl_close($ch);
                $response = json_decode($response, true);
                // @codingStandardsIgnoreEnd

                if (is_array($response) && isset($response['countryCode'])) {
                    $countryCode = $response['countryCode'];
                }
            } catch (\Exception $e) {
                throw new LogicException(_($e->getMessage()));
            }
        }
        return $countryCode;
    }

    /**
     * @param string $ip
     * @return bool|string
     */
    protected function ipv6ToLong($ip)
    {
        // @codingStandardsIgnoreStart
        $pton = inet_pton($ip);
        if (!$pton) {
            return false;
        }
        $number = '';
        foreach (unpack('C*', $pton) as $byte) {
            $number .= str_pad(decbin($byte), 8, '0', STR_PAD_LEFT);
        }
        return base_convert(ltrim($number, '0'), 2, 10);
        // @codingStandardsIgnoreEnd
    }

    /**
     * @return mixed
     */
    public function getEnableModule()
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getPopupStatus($storeId)
    {
        $dataReturn = $this->scopeConfig->getValue(
            self::POPUP,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($dataReturn == '' || $dataReturn == null) {
            return false;
        } else {
            return $dataReturn;
        }
    }

    /**
     * @return bool
     */
    public function isRememberPopupStatus()
    {
        return true;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        $baseUrl = $this->_urlBuilder->getUrl();
        return $baseUrl;
    }

    /**
     * @return string
     */
    public function getUrl($param)
    {
        $baseUrl = $this->_urlBuilder->getUrl($param);
        return $baseUrl;
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getPopupMessage($storeId)
    {
        $popupMessage = $this->scopeConfig->getValue(
            self::MESSAGE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($popupMessage == '' || $popupMessage == null) {
            return __("We think you are in [country], do you want to switch store?");
        } else {
            return $popupMessage;
        }
    }

    /**
     * @param string $storeId
     * @return \Magento\Framework\Phrase|mixed
     */
    public function getPopupButton($storeId)
    {
        $buttonTitle = $this->scopeConfig->getValue(
            self::BUTTON,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($buttonTitle == '' || $buttonTitle == null) {
            return __("Switch Store");
        } else {
            return $buttonTitle;
        }
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getCountries($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_COUNTRIES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function restrictionUrl($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_URL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getDefaultRedirect($storeId)
    {
        return $this->scopeConfig->getValue(
            self::DEFAULT_REDIRECT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function getUrlCustom()
    {
        $result = $this->scopeConfig->getValue(self::GEOIP_URL_CUSTOM, $this->scopeStore);
        return $result;
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getFileCustom()
    {
        $result = $this->scopeConfig->getValue(self::GEOIP_FILE_CUSTOM, $this->scopeStore);
        return $result;
    }

    /**
     * @return mixed
     */
    public function getUrlCustomIPv6()
    {
        $result = $this->scopeConfig->getValue(self::GEOIP_URL_CUSTOM_IPV6, $this->scopeStore);
        return $result;
    }

    /**
     * @return mixed
     */
    public function getFileCustomIPv6()
    {
        $result = $this->scopeConfig->getValue(self::GEOIP_FILE_CUSTOM_IPV6, $this->scopeStore);
        return $result;
    }

    /**
     * @return bool
     */
    public function isUseStoreCode()
    {
        $result = $this->scopeConfig->isSetFlag(self::WEB_URL_USE_STORE, $this->scopeStore);
        return $result;
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function restrictionUserAgent($storeId)
    {
        return $this->scopeConfig->getValue(
            self::RESTRICTION_USER_AGENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function restrictionIp($storeId)
    {
        return $this->scopeConfig->getValue(
            self::RESTRICTION_IP,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getCountriesBlackList($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_COUNTRIES_BLACK_LIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getUrlBlackList($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_URL_BLACK_LIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getEnableBlackList($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_ENABLE_BLACK_LIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getIpBlackList($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_IP_BLACK_LIST,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function isAutoSwitchCurrency()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_BSS_AUTOSW_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $storeId
     * @return mixed
     */
    public function getCodes()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_BSS_AUTOSW_CODES,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $value;
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getAllowSwitch($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_ALLOW_SWITCH,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getIpForTester($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_IP_FOR_TESTER,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @return mixed
     */
    public function isSwitchWebsite()
    {
        return $this->scopeConfig->isSetFlag(
            self::ENABLE_SWITCH_WEBSITE,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param string $storeId
     * @return mixed|string
     */
    public function getTimeCookie($storeId)
    {
        $result = $this->scopeConfig->getValue(
            self::GEOIP_TIME_COOKIE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if ($result == null || $result == '') {
            $result = 1;
        }
        if ((int)$result > 3650) {
            $result = 3650;
        }
        return $result;
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getRedirectsScope($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_REDIRECT_SCOPE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param string $storeId
     * @return mixed
     */
    public function getEnableCookie($storeId)
    {
        return $this->scopeConfig->getValue(
            self::GEOIP_ENABLE_COOKIE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param null $ipForTester
     * @return string|null
     */
    public function getIpCustomer($ipForTester = null)
    {
        //If IP For Tester NULL then return current IP of Customer
        $ipCustomer = '';
        if ($ipForTester == null || $ipForTester == '') {
            $ipCustomers = explode(',', $this->getIpAdress());
            if (!empty($ipCustomers)) {
                $ipCustomer = $ipCustomers[0];
            }
        } else {
            $ipCustomer = $ipForTester;
        }
        if ($ipCustomer == '127.0.0.1' || $ipCustomer == 'UNKNOWN') {
            //Return a US IP
            $ipCustomer = '23.235.227.106';
        }
        return $ipCustomer;
    }

    /**
     * @return string
     */
    protected function getIpAdress()
    {
        if ($this->_request->getServer('HTTP_CLIENT_IP')) {
            $ipAddress = $this->_request->getServer('HTTP_CLIENT_IP');
        } elseif ($this->_request->getServer('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = $this->_request->getServer('HTTP_X_FORWARDED_FOR');
        } elseif ($this->_request->getServer('HTTP_X_FORWARDED')) {
            $ipAddress = $this->_request->getServer('HTTP_X_FORWARDED');
        } elseif ($this->_request->getServer('HTTP_FORWARDED_FOR')) {
            $ipAddress = $this->_request->getServer('HTTP_FORWARDED_FOR');
        } elseif ($this->_request->getServer('HTTP_FORWARDED')) {
            $ipAddress = $this->request->getServer('HTTP_FORWARDED');
        } elseif ($this->_request->getServer('REMOTE_ADDR')) {
            $ipAddress = $this->_request->getServer('REMOTE_ADDR');
        } else {
            $ipAddress = 'UNKNOWN';
        }

        return $ipAddress;
    }
}
