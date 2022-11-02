<?php
declare(strict_types=1);
/**
 * Magentizer_GeoIP extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Magentizer
 * @package   Magentizer_GeoIP
 * @copyright 2018 Magentizer
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Qaiser Bashir
 */

namespace Magentizer\GeoIP\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Store\Model\ScopeInterface;

class CountrySwitcher extends \Magento\Framework\View\Element\Template
{

    public function __construct(
    \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
    \Magentizer\GeoIP\Helper\Address $addressHelper,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
        Context $context,
        array $data = []
    ) {
        $this->_cookieManager = $cookieManager;
        $this->_storeManager = $storeManager;
        $this->_addressHelper = $addressHelper;
        parent::__construct($context, $data);
    }
    
    
    public function getCountryFromGeoIp(){
        $geoipcountry = $this->_cookieManager->getCookie("mb_geo_country");
        return (isset($geoipcountry))?$geoipcountry:"";
        
    }
    
    public function getCurrentStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }
    
    
    public function getTestingHtml(){
     $GroupMapping = $this->_addressHelper->getGroupCountryMapping();
     $returnHtml = "Mapping is also Done Like the Below can be changed from Configuration <br><br>";
     foreach($GroupMapping as $countryid => $groupdatails){
        $returnHtml = $returnHtml . "If country from GeoIP is $countryid By Detault it will to Group " . $groupdatails["name"] . "<br>";
     }
     return $returnHtml;   
    } 
    
}
