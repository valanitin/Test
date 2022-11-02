<?php
namespace Firas\CustomerRegistration\Observer;

use Zend\Mail\Transport\Smtp;

use Magento\Framework\Event\Observer;

use Magento\Framework\Event\ObserverInterface;

use Psr\Log\LoggerInterface as Logger;

class OrderObserver implements ObserverInterface
{
  protected $_logger;

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

    $customer = $observer->getEvent()->getData('customer');
    $customerEmail = $customer->getEmail();
    $customerId = $customer->getId();
    $customerFirstName = $customer->getFirstname();
    $customerLastName = $customer->getLastname();
    $customerName = $customerFirstName.' '.$customerLastName;
    $website = $_SERVER['HTTP_HOST'];
    $dob = $customer->getDob();

    if(!isset($dob)){
      $dob = "";
    }
    
    $dom = $customer->getCustomAttribute('dom');

    if(isset($dom)){
      $wedding = $dom->getValue();
    }else{
      $wedding = '';
    }
    
    // $wedding = $customer->getSkype();
    $data = array($customerEmail);
    $this->_logger->info('Customer data posted', $data);
    // echo $customerName.'=='.$website.'=='.$dob.'=='.$wedding;exit;

    // print_r($customer->getData());exit;

    //API to post customer registration data start
    $url = 'https://erp.theluxuryunlimited.com/api/magento/customer-reference';

    //Initiate cURL.
    $ch = curl_init($url);


    //The JSON data.
    $newjsonData = array(
      'name' => $customerName,
      'email' => $customerEmail,
      'website'   => $website,
      'dob' => $dob,
      'wedding_anniversery' => $wedding,
      'phone'=> '',
      'platform_id'=> $customerId
    );

    //Encode the array into JSON.
    $jsonDataEncoded = json_encode($newjsonData);

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
      curl_close($ch);
      $this->_logger->info('Customer data posted to API', $res);

    }
    catch(Exception $e){

    }
    //API End

  }

}
