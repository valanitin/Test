<?php
/**
 * Copyright © 2016 ToBai. All rights reserved.
 */
namespace Magentizer\GeoIP\Plugin;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
use Magento\Framework\App\Area;
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
	
	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resourceConnection;
	
	
    public function __construct(
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
		\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
		\Magentizer\GeoIP\Helper\Address $addressHelper,
		\Magentizer\GeoIP\Model\Config\ScopeCodeResolver $scopeCodeResolver,
		\Magento\Framework\App\Request\Http $request,
		HttpContext $httpContext,
		StoreCookieManagerInterface $storeCookieManager,
		StoreRepositoryInterface $storeRepository,
		\Magento\Framework\App\ResourceConnection $resource
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
		$this->resourceConnection = $resource;
		
		// $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/templog.log');
		// $this->logger = new \Zend\Log\Logger();
		// $this->logger->addWriter($writer);
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
			return;
		} else {
			$geoCountryId = $this->_cookieManager->getCookie("mb_geo_country");
			if (!$geoCountryId) {			   
				$this->switchStore();				
			}
        }
    }
  
    
    /**
     * @return void
     */
    protected function switchStore()
    {
        //$this->logger->info("OLD : " . $storeId . " | " . $storecode . " | " . $defaultstoreCode);
      
        /* START CODE FOR SWICH STORE */
        $this->ipAddress = $this->getIpAddress();
        $contents = $this->getApiData($this->getIpApiUrl());
        $geoData = json_decode($contents);

        if(empty($geoData) || $geoData->status == 'fail'){
          $contents = $this->getApiData($this->getIpInfoUrl());
          $geoData = json_decode($contents);
        }
		
        if(isset($geoData->countryCode)){
          $currentCountryCode = $geoData->countryCode;
        }else{
          $currentCountryCode = "gb";
        }

        $finalCountryCode = $currentCountryCode;

        $resource = $this->resourceConnection;
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('store_website'); //gives table name with prefix

        if($finalCountryCode!=""){
          $query = 'SELECT website_id FROM ' . $tableName . ' WHERE code = "'. $finalCountryCode . '" LIMIT 1';
          $websiteId = $connection->fetchOne($query);
          if($websiteId==""){
            $query = 'SELECT website_id FROM ' . $tableName . ' WHERE is_default = 1 LIMIT 1';
            $websiteId = $connection->fetchOne($query);
          }
        }else{
          $query = 'SELECT website_id FROM ' . $tableName . ' WHERE is_default = 1 LIMIT 1';
          $websiteId = $connection->fetchOne($query);
        }

        $storeId = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
        $storecode = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getCode();
        $defaultstoreCode = $this->storeManager->getDefaultStoreView()->getCode();

       // $this->logger->info("NEW : " . $storeId . " | " . $storecode . " | " . $defaultstoreCode);
       
        if ($storeId) {
            $store = $this->storeRepository->getActiveStoreByCode($storecode);
            $this->httpContext->setValue(Store::ENTITY, $storecode, $defaultstoreCode);
            $this->storeCookieManager->setStoreCookie($store);
            $this->storeManager->setCurrentStore($storeId);
            $this->scopeCodeResolver->reset();
			$this->updateTheCookie("mb_geo_country",$finalCountryCode); 
			$this->updateTheCookie("mb_store_cookie",$storeId); 
        }
    }

    /* START CUSTOM FUNCTIONS */
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
    /* END CUSTOM FUNCTIONS */
    
    public function updateTheCookie($cookieName,$cookieValue){
        
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath("/")
            ->setDuration(86400);
        $this->_cookieManager
            ->setPublicCookie($cookieName, $cookieValue, $metadata);
        
    }
}
