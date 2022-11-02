<?php
namespace Firas\OrderPushToERP\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface as Logger;

class OrderPushCustom implements ObserverInterface
{
  protected $_logger;
  protected $_customLogger;

    /**
     * [__construct ]
     *
     * @param Logger $logger
     */
    public function __construct(
        Logger $logger,
        \Firas\OrderPushToERP\Logger\Logger $customLogger
    ) {
        $this->_logger = $logger;
        $this->_customLogger = $customLogger;
    }

  public function execute(\Magento\Framework\Event\Observer $observer)
  {

    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    
    $orders = $observer->getEvent()->getOrder();
    $billingAddress = $orders->getBillingAddress();
    $shippingAddress = $orders->getShippingAddress();
    $orderRealId = $orders->getIncrementId();
	
	$itemArray=array();
	$allItems = $orders->getAllItems();
	$count=0;
	foreach($allItems as $item){
		$itemArray[$count]['sku'] = $item->getSku();
		$itemArray[$count]['qty'] = $item->getQtyOrdered();
		$count++;
	}
	$websiteName = 'WWW.SOLOLUXURY.COM';
	$customerName = $orders->getCustomerFirstname()." ".$orders->getCustomerLastname();
	$customerEmail = $orders->getCustomerEmail();
	$orderRealId = $orders->getIncrementId();
	$status = $orders->getStatus();

	if($status == "Canceled" || $status == "Pending" )
	{
		$url = 'https://erp.theluxuryunlimited.com/api/customer/add_cart_data';

		//Initiate cURL.
		$ch = curl_init($url);
		
		$newjsonData = array(
						"name"=>$customerName,
						"lang_code"=>"en_US",
						"email"=>$customerEmail,
						"website"=>$websiteName,
						"type"=>"payment-failed",
						"item_info"=>$itemArray
					);
		
		//Encode the array into JSON.
		$jsonDataEncoded = json_encode($newjsonData);

		//Tell cURL that we want to send a POST request.
		curl_setopt($ch, CURLOPT_POST, 1);

		//Attach our encoded JSON string to the POST fields.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);

		//Set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//Execute the request
		try{
		  $result = curl_exec($ch);
		  $result = json_decode($result, true);
		  
		  $status = $result['status'];
		  $message = $result['message'];
		  $this->_customLogger->info($status." ".$message);
		  $err = curl_error($ch);
		  curl_close($ch);
		  //$this->_customLogger->info($message.'=='.$incrementId);
		}
		catch(Exception $e){
		  $this->_customLogger->info($e.'=='.$incrementId);
		}
	}
    //end
  }

}
