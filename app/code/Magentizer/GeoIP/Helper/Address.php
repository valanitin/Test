<?php
/**
 * Magentizer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magentizer.com license that is
 * available through the world-wide-web at this URL:
 * https://www.Magentizer.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magentizer
 * @package     Magentizer_GeoIP
 * @copyright   Copyright (c) Magentizer (https://www.Magentizer.com/)
 * @license     https://www.Magentizer.com/LICENSE.txt
 */

namespace Magentizer\GeoIP\Helper;

use Exception;
use GeoIp2\Database\Reader;
use Magento\Customer\Helper\Address as CustomerAddressHelper;
use Magento\Directory\Model\Region;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Locale\Resolver;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Address
 * @package Magentizer\GeoIP\Helper
 */
class Address extends Data
{
    /**
     * @type DirectoryList
     */
    protected $_directoryList;

    /**
     * @type Resolver
     */
    protected $_localeResolver;

    /**
     * @type Region
     */
    protected $_regionModel;

    /**
     * @var CustomerAddressHelper
     */
    protected $addressHelper;
    public $jsonSerializer;
    public $groupFactory;

    /**
     * Address constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param DirectoryList $directoryList
     * @param Resolver $localeResolver
     * @param Region $regionModel
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        DirectoryList $directoryList,
        Resolver $localeResolver,
        Region $regionModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\GroupFactory $groupFactory,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer
    ) {
        $this->_directoryList  = $directoryList;
        $this->_localeResolver = $localeResolver;
        $this->_regionModel    = $regionModel;
        $this->scopeConfig = $scopeConfig;
        $this->groupFactory = $groupFactory;
        $this->_storeManager = $storeManager;
        $this->jsonSerializer = $jsonSerializer;

        parent::__construct($context);
    }

    /***************************************** Maxmind Db GeoIp ******************************************************/
    /**
     * Check has library at path var/Magentizer/GeoIp/GeoIp/
     * @return bool|string
     * @throws FileSystemException
     */
    public function checkHasLibrary()
    {
        $path = $this->_directoryList->getPath('var') . '/Magentizer/GeoIp/GeoIp';
        if (!file_exists($path)) {
            return false;
        }

        $folder   = scandir($path, true);
        $pathFile = $path . '/' . $folder[0] . '/GeoLite2-City.mmdb';
        if (!file_exists($pathFile)) {
            return false;
        }

        return $pathFile;
    }
    
    public function getDefaultCountry($CountryCode){
    $storeManagerDataList = $this->_storeManager->getStores();
     $options = array();
     
     foreach ($storeManagerDataList as $key => $value) {
               $options[$this->scopeConfig->getValue('general/country/default', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$key)] = array("id"=>$key,"code"=>$value['code']);
     }
     
     if(isset($options[$CountryCode])){
        return $options[$CountryCode];
     }
     return false;
}
    
    
    public function getAllGroupIds(){
        
        $croupCollection = $this->groupFactory->create()->getCollection();
        $Groupname = array();
        
        foreach($croupCollection as $groupdata){
            $Groupname[$groupdata->getId()] = array("name"=>$groupdata->getName(),"cTstoreId"=>$groupdata->getDefaultStoreId());
        }
        
        return $Groupname;
    }
    
    public function getGroupCountryMapping(){
        $unsearlizedData = array();
        $returnMapArray = array();
        
        $allGroupsData = $this->getAllGroupIds();
        $valuefromConfiguration = $this->scopeConfig->getValue('geoip/general/country_groups', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($valuefromConfiguration){
           $unsearlizedData =  $this->jsonSerializer->unserialize($valuefromConfiguration);
        }
        if(count($unsearlizedData)){
            foreach($unsearlizedData as $BKData){
                if(isset($BKData["country_ids"]) && $BKData["country_ids"]){
                    $BKDataCountryIds = $BKData["country_ids"].",";
                    $exploaded = explode(",",$BKDataCountryIds);
                    foreach($exploaded as $countrycId){
                        if($countrycId){
                            if(isset($allGroupsData[$BKData["group"]])){
                                $returnMapArray[$countrycId] = array("name" => $allGroupsData[$BKData["group"]]["name"], "id" => $BKData["group"],"cTstoreId"=>$allGroupsData[$BKData["group"]]["cTstoreId"]);
                            }else{
                                $returnMapArray[$countrycId] = array("name" => "", "id" => $BKData["group"],"cTstoreId"=>0);
                            }
                            
                            
                        }
                        
                    }
                }
                
            }
        }
        
        
        
        
        
        
        return $returnMapArray;
    }
    
    
    public function getGroupCountryMappingForShipping(){
        $unsearlizedData = array();
        $returnMapArray = array();
        
        
        $valuefromConfiguration = $this->scopeConfig->getValue('geoip/general/country_groups', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($valuefromConfiguration){
           $unsearlizedData =  $this->jsonSerializer->unserialize($valuefromConfiguration);
        }
        if(count($unsearlizedData)){
            foreach($unsearlizedData as $BKData){
                if(isset($BKData["country_ids"]) && $BKData["country_ids"]){
                    
                    $BKDataCountryIds = $BKData["country_ids"].",";
                    $exploaded = explode(",",$BKDataCountryIds);
                    foreach($exploaded as $countrycId){
                        if($countrycId){
                            $returnMapArray[$BKData["group"]][] = $countrycId;
                        }
                        }
                    
                }
                
            }
        }
        
        
        
        
        
        
        return $returnMapArray;
    }
    
    
    public function getGroupCountryMappingForPrices(){
        $unsearlizedData = array();
        $returnMapArray = array();
        
        
        $valuefromConfiguration = $this->scopeConfig->getValue('geoip/general/country_groups', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($valuefromConfiguration){
           $unsearlizedData =  $this->jsonSerializer->unserialize($valuefromConfiguration);
        }
        if(count($unsearlizedData)){
            foreach($unsearlizedData as $BKData){
                if(isset($BKData["default_country_pricing"]) && $BKData["default_country_pricing"]){
                    $returnMapArray[$BKData["default_country_pricing"]] = $BKData["group"];
                }
                
            }
        }
        
        
        
        
        
        
        return $returnMapArray;
    }
    
    
    public function getGroupStoreId($countryId = ""){
        $GroupCountryMapping = $this->getGroupCountryMapping();
        if($countryId){
            if(isset($GroupCountryMapping[$countryId])){
                return (int)$GroupCountryMapping[$countryId]["cTstoreId"];
            }else{
              return 0;  
            }
        }else{
            return 0;
        }
        
    }


public function isEnabled($storeId = 0){
    if($storeId){
        return $this->scopeConfig->getValue('geoip/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }else{
        return $this->scopeConfig->getValue('geoip/general/enabled', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
}

public function getLicenseKey($storeId = 0){
    if($storeId){
        return $this->scopeConfig->getValue('geoip/general/token', \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
    }else{
        return $this->scopeConfig->getValue('geoip/general/token', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
}
    /**
     * @param null $storeId
     *
     * @return array
     */
     
     
     
     
    public function getGeoIpData($storeId = null)
    {
        try {
            $libPath = $this->checkHasLibrary();
            if ($this->isEnabled($storeId) && $libPath && class_exists('GeoIp2\Database\Reader')) {
                $geoIp  = new Reader($libPath, $this->getLocales());
                $record = $geoIp->city($this->getIpAddress());

                $geoIpData = [
                    'city'       => $record->city->name,
                    'country_id' => $record->country->isoCode,
                    'postcode'   => $record->postal->code
                ];

                if ($record->mostSpecificSubdivision) {
                    $code = $record->mostSpecificSubdivision->isoCode;
                    if ($regionId = $this->_regionModel->loadByCode($code, $record->country->isoCode)->getId()) {
                        $geoIpData['region_id'] = $regionId;
                    } else {
                        $geoIpData['region'] = $record->mostSpecificSubdivision->name;
                    }
                }
            } else {
                $geoIpData = [];
            }
        } catch (Exception $e) {
            // No Ip found in database
            $geoIpData = [];
        }

        return $geoIpData;
    }

    /**
     * Get IP
     * @return string
     */
    public function getIpAddress()
    {
        $fakeIP = $this->_request->getParam('fakeIp', false);
        if ($fakeIP) {
            return $fakeIP;
        }

        $server = $this->_getRequest()->getServer();

        $ip = $server['REMOTE_ADDR'];
        if (!empty($server['HTTP_CLIENT_IP'])) {
            $ip = $server['HTTP_CLIENT_IP'];
        } elseif (!empty($server['HTTP_X_FORWARDED_FOR'])) {
            $ip = $server['HTTP_X_FORWARDED_FOR'];
        }

        $ipArr = explode(',', $ip);

        return array_shift($ipArr);
    }

    /**
     * @return array
     */
    protected function getLocales()
    {
        $language = substr($this->_localeResolver->getLocale(), 0, 2) ?: 'en';

        $locales = [$language];
        if ($language !== 'en') {
            $locales[] = 'en';
        }

        return $locales;
    }
}
