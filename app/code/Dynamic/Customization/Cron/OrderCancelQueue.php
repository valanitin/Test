<?php

namespace Dynamic\Customization\Cron;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class OrderCancelQueue
{
    /**
    * Mytickets
    *
    * @var \Dynamic\Mytickets\Model\Mytickets
    */

    /**
    * CurlFactory
    *
    * @var \Magento\Framework\HTTP\Client\CurlFactory
    */
    protected $curlFactory;
     protected $_orderCollectionFactory;
     protected $dynamicHelper;

    /**
    * Constructor
    *
    * @param \Dynamic\Mytickets\Model\Mytickets $mytickets
    * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Dynamic\Customization\Helper\Data $dynamicHelper,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    ) { 
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->dynamicHelper= $dynamicHelper;
        $this->curlFactory = $curlFactory;
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/OrderCancelQueue.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("cron run successfully...");
        $this->generate();
        return $this;
    }

    public function generate() {

        $cancelledOderQueue = $this->_orderCollectionFactory->create()
        ->addFieldToSelect('*')
        ->addFieldToFilter('status', ['eq' => 'canceled']);
        $storeManager  = $this->dynamicHelper->getStoreManager();
        $storeCode = $storeManager->getStore()->getCode();


        if(count($cancelledOderQueue) > 0 && !empty($cancelledOderQueue)) {

            foreach ($cancelledOderQueue as $order) {


                $url = "https://erp.theluxuryunlimited.com/api/return-exchange-buyback/create";

                $ch = curl_init($url);

                $customerEmail = $order->getCustomerName();
                $orderId = $order->getIncrementId();
                $website = "WWW.SOLOLUXURY.COM";
                $type = "cancellation";
                $langCode = $storeCode;
                // echo $customerEmail.'=='.$orderId.'=='.$sku;exit;
                //The JSON data.
                $newjsonData = array(
                    'customer_email' => $customerEmail,
                    'website' => $website,
                    'order_id'   => $orderId,
                    'type' => $type,
                    'lang_code' => $langCode
                );

                //Encode the array into JSON.
                $jsonDataEncoded = json_encode($newjsonData);
                $curl = $this->curlFactory->create();
                $curl->setOption(CURLOPT_POST, 1);
                $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                $curl->post($url, json_encode($newjsonData));
                $result   = $curl->getBody();
                $response = [json_decode($result,true)];

                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/OrderCancelQueue.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($response);


            }


        }


        return true;
    }
}