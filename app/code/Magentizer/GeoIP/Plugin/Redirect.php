<?php 

namespace Magentizer\GeoIP\Plugin;

use \Magento\Framework\Controller\ResultFactory;
use \Magento\Framework\App\FrontControllerInterface;
use \Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Model\Store;

class Redirect
{
	/**
	 * @var ResultFactory
	 */
    protected $_resultFactory;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * @var \Magento\Framework\Stdlib\CookieManagerInterface
	 */
	protected $_cookieManager;
	
	/**
	 * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
	 */
	protected $cookieMetadataFactory;
	
	/**
	 * @var \Magentizer\GeoIP\Helper\Address
	 */
	protected $_addressHelper;
	
	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;
	
	/**
	 * @var HttpContext
	 */
	protected $httpContext;
    
	/**
	 * @var StoreCookieManagerInterface
	 */
	protected $storeCookieManager;
	
	/**
	 * @var StoreRepositoryInterface
	 */
	protected $storeRepository;
	
	/**
	 * @var \Magentizer\GeoIP\Model\Config\ScopeCodeResolver
	 */
	protected $scopeCodeResolver;
	
	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resourceConnection;
	
	/**
	 * Construct
	 */
    public function __construct(
        ResultFactory $resultFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
		\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
		\Magentizer\GeoIP\Helper\Address $addressHelper,
		\Magento\Framework\App\Request\Http $request,
		HttpContext $httpContext,
		StoreCookieManagerInterface $storeCookieManager,
		StoreRepositoryInterface $storeRepository,
		\Magentizer\GeoIP\Model\Config\ScopeCodeResolver $scopeCodeResolver,
		\Magento\Framework\App\ResourceConnection $resource
        
    )  {
        $this->_resultFactory = $resultFactory;
		$this->storeManager = $storeManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->_cookieManager = $cookieManager;
        $this->_addressHelper = $addressHelper;
        $this->request = $request;
		$this->httpContext = $httpContext;
        $this->storeCookieManager = $storeCookieManager;
        $this->storeRepository = $storeRepository;
		$this->scopeCodeResolver = $scopeCodeResolver;
		$this->resourceConnection = $resource;
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/redirect-log.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
		
    }
       
    // public function aroundDispatch(
        // FrontControllerInterface $subject,
        // callable $proceed,
        // RequestInterface $request
    // ) {
		// $geoCountryId = $this->_cookieManager->getCookie("mb_geo_country");
		// $this->logger->info("Cokkie country - ".$geoCountryId);
		// if (!$geoCountryId) {
			// $switchSTore = $this->switchStore();
			// if($switchSTore) {
				// $geoRedirectUrl = $this->storeManager->getStore()->getBaseUrl();
				// $this->logger->info("Switch store - ".$switchSTore);
				// $this->logger->info($geoRedirectUrl);
				// $resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
				// $resultRedirect->setHeader('Cache-Control','null');
				// $resultRedirect->setUrl($geoRedirectUrl);
				// return $resultRedirect;
			// }
		// } else {
			// $this->logger->info($geoCountryId);
		// }
        // return $proceed($request);
    // }
	
	
	
	public function afterDispatch(
		FrontControllerInterface $subject,
		$result
    ) {
		$geoCountryId = $this->_cookieManager->getCookie("mb_geo_country");
		$this->logger->info("Cokkie country - ".$geoCountryId);
		$this->logger->info("user ip address - ".$this->getIpAddress());
		if (!$geoCountryId) {
			$switchSTore = $this->switchStore();
			if($switchSTore) {
				$geoRedirectUrl = $this->storeManager->getStore()->getBaseUrl();
				$this->logger->info("Switch store - ".$switchSTore);
				$this->logger->info($geoRedirectUrl);
				$resultRedirect = $this->_resultFactory->create(ResultFactory::TYPE_REDIRECT);
				$resultRedirect->setHeader('Cache-Control','null');
				$resultRedirect->setUrl($geoRedirectUrl);
				return $resultRedirect;
			}
		} else {
			$this->logger->info($geoCountryId);
		}
		return $result;
    }
	
	 /**
     * @return void
     */
    protected function switchStore()
    {      
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

        $connection = $this->resourceConnection->getConnection();
        $tableName = $this->resourceConnection->getTableName('store_website'); //gives table name with prefix

        if($finalCountryCode!=""){
			$query = 'SELECT website_id FROM ' . $tableName . ' WHERE code = "'. $finalCountryCode . '" LIMIT 1';
			$websiteId = $connection->fetchOne($query);
			if($websiteId==""){
				$query = 'SELECT website_id FROM ' . $tableName . ' WHERE is_default = 1 LIMIT 1';
				$websiteId = $connection->fetchOne($query);
			}
			$this->updateTheCookie("mb_geo_country",$finalCountryCode); 
        }else{
			$query = 'SELECT website_id FROM ' . $tableName . ' WHERE is_default = 1 LIMIT 1';
			$websiteId = $connection->fetchOne($query);
        }

        $storeId = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
        $storecode = $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getCode();
        $defaultstoreCode = $this->storeManager->getDefaultStoreView()->getCode();

        $this->logger->info("NEW : " . $storeId . " | " . $storecode . " | " . $defaultstoreCode);
		$this->updateTheCookie("mb_store_cookie",$storeId); 
       
        if ($storeId) {
            $store = $this->storeRepository->getActiveStoreByCode($storecode);
            $this->httpContext->setValue(Store::ENTITY, $storecode, $defaultstoreCode);
            $this->storeCookieManager->setStoreCookie($store);
            $this->storeManager->setCurrentStore($storeId);
            $this->scopeCodeResolver->reset();
			$returnStoreCode = $storecode ?: $defaultstoreCode;
			return $returnStoreCode;
        }
		return false;
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
    
    public function updateTheCookie($cookieName,$cookieValue){
        
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setPath("/")
            ->setDuration(86400);
        $this->_cookieManager
            ->setPublicCookie($cookieName, $cookieValue, $metadata);
        
    }
}
