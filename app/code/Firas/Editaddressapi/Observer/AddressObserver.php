<?php
namespace Firas\Editaddressapi\Observer;

use Zend\Mail\Transport\Smtp;

use Magento\Framework\Event\Observer;

use Magento\Framework\Event\ObserverInterface;

use Psr\Log\LoggerInterface as Logger;

class AddressObserver implements ObserverInterface
{
  protected $_logger;
  const XML_PATH_ERP_API_URL = "custom/erp_api/erp_api_url";
    /**
     * [__construct ]
     *
     * @param Logger $logger
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Logger $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_logger = $logger;
    }

  public function execute(\Magento\Framework\Event\Observer $observer)
  {
    $customerAddress = $observer->getCustomerAddress();
    $customer = $customerAddress->getCustomer();

    //The JSON data.
    $customerEmail = $customer->getData('email');
    $website = 'www.solo.com';


    $billingflag  = $customerAddress->getData('default_billing');
    $shippingflag = $customerAddress->getData('default_shipping');
    $addresstype = array();
    if($billingflag){
      $addresstype[] = "billing";
    }
    if($shippingflag){
      $addresstype[] = "shipping";
    }

    //The JSON data.
    $newjsonData = array(
      "entity_id" => $customerAddress->getData('entity_id'),
      "address_type" => $addresstype,
      "region" => $customerAddress->getData('region'),
      "region_id" => $customerAddress->getData('region_id'),
      "postcode" => $customerAddress->getData('postcode'),
      "firstname" => $customerAddress->getData('firstname'),
      "lastname" => $customerAddress->getData('lastname'),
      "middlename" => $customerAddress->getData('middlename'),
      "company" => $customerAddress->getData('company'),
      "country_id" => $customerAddress->getData('country_id'),
      "telephone" => $customerAddress->getData('telephone'),
      "prefix" => $customerAddress->getData('prefix'),
      "street" => $customerAddress->getData('street')
    );


    $this->_logger->info('Customer address data posted', $newjsonData);
    //Encode the array into JSON.
    $jsonDataEncoded = json_encode($newjsonData);


    //API to post customer registration data start
    $url = $this->getErpApiUrl().'/api/customer/add_customer_data?website='.$website.'&email='.$customerEmail;


    //Initiate cURL.
    $ch = curl_init($url);


    //Tell cURL that we want to send a POST request.
    curl_setopt($ch, CURLOPT_POST, 1);

    //Attach our encoded JSON string to the POST fields.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

    //Set the content type to application/json
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    // Return response instead of outputting
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    //Execute the request
    try{
      $result = curl_exec($ch);
      // $result = 'success';
      $err = curl_error($ch);
      $res = array($result);
      //curl_close($ch);
      $this->_logger->info('Customer Address data posted to API', $res);

    }
    catch(Exception $e){

    }
    //API End

  }
  public function getConfig($path, $storeId = null)
  {
    return $this->scopeConfig->getValue($path,\Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId);
  }
  public function getErpApiUrl()
  {
    return $this->getConfig(self::XML_PATH_ERP_API_URL);
  }
}
