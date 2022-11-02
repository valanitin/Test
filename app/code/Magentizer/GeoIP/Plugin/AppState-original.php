<?php
/**
 * Copyright © 2016 ToBai. All rights reserved.
 */
namespace Magentizer\GeoIP\Plugin;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;

class AppState
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;
    protected $_cookieManager;
    protected $cookieMetadataFactory;

    /**
     * @var array
     */
    private $disabledAreas = [
        Area::AREA_ADMIN,
        Area::AREA_ADMINHTML,
        Area::AREA_CRONTAB
    ];

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
     
      /**
     * @var \Magentizer\GeoIP\Model\Config\ScopeCodeResolver
     */
    private $scopeCodeResolver;
    public $_addressHelper;
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magentizer\GeoIP\Helper\Address $addressHelper,
        \Magentizer\GeoIP\Model\Config\ScopeCodeResolver $scopeCodeResolver,
        \Magento\Framework\App\Request\Http $request,
        HttpContext $httpContext,
    StoreCookieManagerInterface $storeCookieManager,
    StoreRepositoryInterface $storeRepository
    ) {
        $this->storeManager = $storeManager;
        $this->scopeCodeResolver = $scopeCodeResolver;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->_cookieManager = $cookieManager;
        $this->_addressHelper = $addressHelper;
        $this->httpContext = $httpContext;
        $this->request = $request;
        $this->storeCookieManager = $storeCookieManager;
        $this->storeRepository = $storeRepository;
    }

    /**
     * @param \Magento\Framework\App\State $subject
     * @param void $result
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSetAreaCode(
        \Magento\Framework\App\State $subject,
        $result
    ) {
        
       
            
        if ($subject->getAreaCode() === Area::AREA_ADMINHTML || in_array($subject->getAreaCode(),$this->disabledAreas)) {
        }else{
            
        /* GET IP WISE COUNTRY CODE */
        $this->ipAddress = $this->getIpAddress();
        $contents = $this->getApiData($this->getIpApiUrl());
        $geoData = json_decode($contents);

        if(empty($geoData) || $geoData->status == 'fail'){
          $contents = $this->getApiData($this->getIpInfoUrl());
          $geoData = json_decode($contents);
        }

        print "<pre>";print_r($geoData); print "</pre>";
        echo "Your Country Code : ". $currentCountry = $geoData->countryCode;
        echo "<br/>";
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath("/")
            ->setDuration(86400);
          $this->_cookieManager
            ->setPublicCookie("mb_geo_country_k", $currentCountry , $metadata);
        /* END IP WISE COUNTRY CODE */

        $this->_cookieManager->deleteCookie("mb_show_popup");

        $geoCountryId = $currentCountry;
        //$geoCountryId = $this->_cookieManager->getCookie("mb_geo_country"); //country_code
        $mb_first_visit = $this->_cookieManager->getCookie("mb_first_visit");


        if(!isset($mb_first_visit)){

          echo "first time - ".$geoCountryId;
          /* START CODE FOR FIRST VISIT */
          $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath("/")
            ->setDuration(86400);
          $this->_cookieManager
            ->setPublicCookie("mb_first_visit", "first", $metadata);

            /* START GROUP CODE */
            $finalCountryId = $geoCountryId;

            $australia_newzealand = "AU,NZ";
            $store_ip = explode(',',$australia_newzealand);
            if (in_array($geoCountryId, $store_ip)) {
              $finalCountryId = "australia_newzealand";
            }

            $group_saarc = "IN,MV,MU,PK";
            $store_ip = explode(',',$group_saarc);
            if (in_array($geoCountryId, $store_ip)) {
              $finalCountryId = "group_saarc";
            }

            $germany_austria = "AT,DE";
            $store_ip = explode(',',$germany_austria);
            if (in_array($geoCountryId, $store_ip)) {
              $finalCountryId = "germany_austria";
            }

            $east_europe = "AL,AM,BY,BG,HR,CZ,EE,GE,HU,LV,LT,MD,PL,RO,SK,SI,UA";
            $store_ip = explode(',',$east_europe);
            if (in_array($geoCountryId, $store_ip)) {
              $finalCountryId = "east_europe";
            }

            $south_america = "AR,BB,BO,CL,CO,CR,EC,SV,GT,HN,MX,PA,PY,PE,TT,UY,VE";
            $store_ip = explode(',',$south_america);
            if (in_array($geoCountryId, $store_ip)) {
              $finalCountryId = "south_america";
            }

            $group_south_east = "ID,MY,PH,TH,VN";
            $store_ip = explode(',',$group_south_east);
            if (in_array($geoCountryId, $store_ip)) {
              $finalCountryId = "group_south_east";
            }

            $group_africa = "BW,GH,KE,MA,NA,NI,NG,RW,TZ,TN,UG";
            $store_ip = explode(',',$group_africa);
            if (in_array($geoCountryId, $store_ip)) {
              $finalCountryId = "group_africa";
            }
            /* END GROUP CODE */

          
          $object_Manager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
          $resource = $object_Manager->get('Magento\Framework\App\ResourceConnection');
          $connection = $resource->getConnection();
          $tableName = $resource->getTableName('store_website'); //gives table name with prefix

          if($finalCountryId!=""){
            $query = 'SELECT website_id FROM ' . $tableName . ' WHERE code = "'. $finalCountryId . '" LIMIT 1';
            $websiteId = $connection->fetchOne($query);
            if($websiteId==""){
              $query = 'SELECT website_id FROM ' . $tableName . ' WHERE is_default = 1 LIMIT 1';
              $websiteId = $connection->fetchOne($query);
            }
          }else{
            $query = 'SELECT website_id FROM ' . $tableName . ' WHERE is_default = 1 LIMIT 1';
            $websiteId = $connection->fetchOne($query);
          }

          $currentStoreIdk = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
          $currentStoreCodek = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getCode();
          $defaultStoreCode = $this->storeManager->getDefaultStoreView()->getCode();

          //store id, store_code, default store code
          //$this->switchStore("90","be_en","be_en");
          $this->switchStore($currentStoreIdk,$currentStoreCodek,$defaultStoreCode);
          /* END CODE FOR FIRST VISIT */
        }else{
          echo "not first time - ".$geoCountryId;
          /* START CODE FOR NOT FIRST VISIT */
          if(!isset($geoCountryId)){
              $this->updateTheCookie("mb_show_popup",1);
              $GeoIPData = $this->_addressHelper->getGeoIpData();
              $cTstoreId = 0;
              if(isset($GeoIPData["country_id"])){
                $geoCountryId =  $GeoIPData["country_id"];
                $this->updateTheCookie("mb_geo_country",$geoCountryId); 
                $DefaultSelection = $this->_addressHelper->getDefaultCountry($geoCountryId);
                if($DefaultSelection){
                  $cTstoreId = $DefaultSelection["id"];
                      if($cTstoreId){
                          $cTstorecode = (string)$DefaultSelection["code"];
                          $cTdefaultCode = "";
                          $this->switchStore($cTstoreId,$cTstorecode,$cTdefaultCode); 
                      }
                  }else{  
                      $cTstoreId = $this->_addressHelper->getGroupStoreId($geoCountryId);
                      if($cTstoreId){
                          $storeData = $this->storeManager->getStore($cTstoreId);
                          $cTstorecode = $cTdefaultCode = (string)$storeData->getCode();
                          $this->switchStore($cTstoreId,$cTstorecode,$cTdefaultCode);
                      }
                  }
                  $this->updateTheCookie("mb_shipping_country",$geoCountryId);  
              }else{
                  $geoCountryId = "Geo IP NA";
                  $this->updateTheCookie("mb_geo_country",$geoCountryId); 
              }
                        
              $this->updateTheCookie("mb_store_cookie",$cTstoreId); 
              $currentStoreId = $this->storeManager->getStore()->getId();
          }elseif($this->request->getPost("shipping_cnt")){ 
              $this->updateTheCookie("mb_shipping_country",$this->request->getPost("shipping_cnt")); 
              $DefaultSelection = $this->_addressHelper->getDefaultCountry($this->request->getPost("shipping_cnt")); 
              if($DefaultSelection){
                  $cTstoreId = $DefaultSelection["id"];
                  if($cTstoreId){
                      $this->storeManager->setCurrentStore($cTstoreId);
                      $this->scopeCodeResolver->reset();
                      $this->updateTheCookie("mb_store_cookie",$cTstoreId); 
                  }  
              }       
          }
          /* END CODE FOR NOT FIRST VISIT */
        }

        /*
        $geoCountryId = "cn";
        
        $object_Manager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $object_Manager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('store_website'); //gives table name with prefix

        if($geoCountryId!=""){
          $query = 'SELECT website_id FROM ' . $tableName . ' WHERE code = "'. $geoCountryId . '" LIMIT 1';
          $websiteId = $connection->fetchOne($query);
          if($websiteId==""){
            $query = 'SELECT website_id FROM ' . $tableName . ' WHERE is_default = 1 LIMIT 1';
            $websiteId = $connection->fetchOne($query);
            $websiteId = "20";
          }
        }else{
          $query = 'SELECT website_id FROM ' . $tableName . ' WHERE is_default = 1 LIMIT 1';
          $websiteId = $connection->fetchOne($query);
        }

        $currentStoreIdk = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
        $currentStoreCodek = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getCode();
        $defaultStoreCode = $this->storeManager->getDefaultStoreView()->getCode();

        //store id, store_code, default store code
        //$this->switchStore("90","be_en","be_en");
        $this->switchStore($currentStoreIdk,$currentStoreCodek,$defaultStoreCode);
        */
        
         
        }
    }

    public function getApiData($url)
    {
        if(empty($url)) {
            $url = $this->getIpApiUrl();
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, false);
        //Tell cURL that it should only spend 5 seconds to connect to the URL.
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
         
        //A given cURL operation should only take 5 seconds max.
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $contents = curl_exec($ch);
        curl_close($ch);  
        return $contents;
    }
    public function getIpAddress()
    {
        if ($this->request->getServer('HTTP_CLIENT_IP')) {
            $ipAddress = $this->request->getServer('HTTP_CLIENT_IP');
        } elseif ($this->request->getServer('HTTP_X_FORWARDED_FOR')) {
            $ipAddress = $this->request->getServer('HTTP_X_FORWARDED_FOR');
        } elseif ($this->request->getServer('HTTP_X_FORWARDED')) {
            $ipAddress = $this->request->getServer('HTTP_X_FORWARDED');
        } elseif ($this->request->getServer('HTTP_FORWARDED_FOR')) {
            $ipAddress = $this->request->getServer('HTTP_FORWARDED_FOR');
        } elseif ($this->request->getServer('HTTP_FORWARDED')) {
            $ipAddress = $this->request->getServer('HTTP_FORWARDED');
        } elseif ($this->request->getServer('REMOTE_ADDR')) {
            $ipAddress = $this->request->getServer('REMOTE_ADDR');
        } else {
            $ipAddress = 'UNKNOWN';
        }

        return $ipAddress;
    }
    

    public function getIpApiUrl()
    {
       return "http://ip-api.com/json/".$this->ipAddress; 
    }
    
    public function getIpInfoUrl()
    {
        //get env variable
        $geoip_token = '6bbcd474d5141c';
        $url = "http://ipinfo.io/".$this->ipAddress."/geo?token=".$geoip_token;
        
        return $url;
    }

    /**
     * @return void
     */
    protected function switchStore($storeId,$storecode,$defaultstoreCode)
    {
        
        if ($storeId) {
            $store = $this->storeRepository->getActiveStoreByCode($storecode);
            $this->httpContext->setValue(Store::ENTITY, $storecode, $defaultstoreCode);
            $this->storeCookieManager->setStoreCookie($store);
            $this->storeManager->setCurrentStore($storeId);
            $this->scopeCodeResolver->reset();
        }
    }
    
    public function updateTheCookie($cookieName,$cookieValue){
        
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath("/")
            ->setDuration(86400);
        $this->_cookieManager
            ->setPublicCookie($cookieName, $cookieValue, $metadata);
        
    }
}
