<?php
namespace Meetanshi\CurrencySwitcher\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const DEFAULT_STORE_CODE = 'english';

    /**
     * countries to store relation
     * default is default
     * @var array
     */
    protected $_countryToStoreCode = array(
        'SA' => 'arabic',
        'CN' => 'chinese',
        'NL' => 'dutch',
        'FR' => 'french',
        'DE' => 'german',
        'ES' => 'spanish',
        'KR' => 'korean',
        'RU' => 'russian',
        'IT' => 'italian',
        'JP' => 'japanes',
    );

    /**
     * get store view code by country
     * @param $country
     * @return bool
     */
    public function getStoreCodeByCountry($country)
    {
        if (isset($this->_countryToStoreCode[$country])) {
            return $this->_countryToStoreCode[$country];
        }
        return self::DEFAULT_STORE_CODE;
    }
    public function deleteCookievalue($name){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
        return $cookieManager->deleteCookie($name);
    }
    public function setStorecookie($store){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $cookieManager = $objectManager->get('Magento\Framework\Stdlib\CookieManagerInterface');
        $cookieMetadataFactory = $objectManager->get('Magento\Framework\Stdlib\Cookie\CookieMetadataFactory');
        $publicCookieMetadata = $cookieMetadataFactory->createPublicCookieMetadata()->setDuration(86400)->setPath('/')->setHttpOnly(false);
        $cookieManager->setPublicCookie('store', $store,$publicCookieMetadata);
    }
}
