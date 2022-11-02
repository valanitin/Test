<?php

namespace Magentizer\Pricing\Cron;

use Magentizer\Pricing\Api\Data\DataInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magentizer\Pricing\Api\Data\DataInterface;
use Magentizer\Pricing\Api\DataRepositoryInterface;


class FetchPrices
{

    const XML_PATH_ERP_API_URL = "custom/erp_api/erp_api_url";
    const XML_PATH_GEO_PRI_WEB = "geoip/general/pricing_website";

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magentizer\GeoIP\Helper\Address $addressHelper,
        DataInterfaceFactory $dataFactory,
        DataObjectHelper $dataObjectHelper,
        DataRepositoryInterface $dataRepository
    )
    {
        $this->scopeConfig = $scopeConfig;
        $this->_addressHelper = $addressHelper;
        
        $this->dataFactory      = $dataFactory;
        $this->dataObjectHelper  = $dataObjectHelper;
        $this->dataRepository   = $dataRepository;
        
        $this->storeManager = $storeManager;
    }

    public function execute()
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(__method__);

        $this->getPrices();

        return $this;

    }
    
    
    public function getProductPrice($SKU){
        //$SKU = "BK600JK0BG004";
        $groupId = $this->storeManager->getGroup()->getId();
        
        $fetchmodel = $this->dataFactory->create();
        $collection = $fetchmodel->getCollection();
        $collection->addFieldToFilter("pricing_store",$groupId)->addFieldToFilter("pricing_sku",$SKU);
        
        if($collection->getFirstItem()->getPricingValue()){
            return $collection->getFirstItem()->getPricingValue();
        }
        return false;
        
        
    }
    
    
    
    
    public function getPrices()
    {
        $pricingDate = date("Y-m-d");
        //$pricingDate = "2020-07-04";



        $MApping = $this->_addressHelper->getGroupCountryMappingForPrices();
        $STaticURL = $this->getErpApiUrl()."uploads/pricing-".$pricingDate.".json";
        $dynamicWebsite = $this->getGeoIpPriceWebsite();
                
        
        $finalUrl = $STaticURL;
        $get_data = $this->callAPI('GET', $finalUrl, false);
        $response = json_decode($get_data, true);
        $returnData = array();
        foreach($response as $websiteIndex => $Data){
            if(trim(strtolower($websiteIndex)) == trim(strtolower($dynamicWebsite)) ){
                $returnData = $Data;
            }
            
        }
        
        
        if(count($Data)>0){
            $trancutemodel = $this->dataFactory->create();
            $connection = $trancutemodel->getCollection()->getConnection();
            $tableName = $trancutemodel->getCollection()->getMainTable();
            $connection->truncateTable($tableName);
            //echo "<pre>";
//            $connection->query("INSERT INTO $tableName (pricing_value)
//VALUES ('9.55')");
// print_r($connection->fetchAll("SELECT * FROM $tableName"));
//            exit;
            
        }
        
        
        foreach($returnData as $sku => $CountryData){
            foreach($CountryData as $CountryId => $PricesDAta){
                if(isset($MApping[$CountryId]) && (int)$MApping[$CountryId] > 0){
                    $tempDAtaArray = array();
                    $tempDAtaArray["pricing_store"] = $MApping[$CountryId];
                    $tempDAtaArray["pricing_sku"] = $sku;
                    if(isset($PricesDAta["price"]) && isset($PricesDAta["price"]["total"])){
                        $tempDAtaArray["pricing_value"] = $PricesDAta["price"]["total"];
                    }else{
                        $tempDAtaArray["pricing_value"] = 0;
                    }
        $model = $this->dataFactory->create();
        $this->dataObjectHelper->populateWithArray($model, $tempDAtaArray, DataInterface::class);
        $this->dataRepository->save($model);
        
                }
                
            }
        }
        return true;
    }

    public function callAPI($method, $url, $data)
    {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', ));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        // EXECUTE:
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }
    public function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getErpApiUrl()
    {
        return $this->getConfig(self::XML_PATH_ERP_API_URL);
    }
    public function getGeoIpPriceWebsite()
    {
        return $this->getConfig(self::XML_PATH_GEO_PRI_WEB);
    }
    
}
