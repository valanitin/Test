<?php

namespace Dynamic\StoreCreditSync\Cron;

class StoreCreditSyncQueue
{
    /**
     * CurlFactory
     *
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $curlFactory;

    /**
     * Order
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * Store
     *
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * Constructor
     *
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     * @param \Magento\Sales\Model\Order $order
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Sales\Model\Order $order,
        \Magento\Store\Model\Store $store,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) { 
        $this->curlFactory = $curlFactory;
        $this->order = $order;
        $this->store = $store;
        $this->resourceConnection = $resourceConnection;
    }

	public function execute()
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/StoreCreditSync.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("cron run successfully...");
		$this->generate();
		return $this;
	}

	public function generate() {

		$orderData = $this->order->getCollection()->addFieldToFilter("store_credit_sync_flag", ["eq", 0]);

		if(count($orderData) > 0 && !empty($orderData)) {

			foreach ($orderData as $order) {

				$connection = $this->resourceConnection->getConnection();
			    $select = $connection->select()
			              ->from('swarming_credit_order_attribute', ["amount"])
			              ->where('order_id = "'.$order->getId().'"');
        		$results = $connection->fetchCol($select);

        		if(!empty($results) && count($results) > 0 && isset($results[0])) {

        			$storeData = $this->store->load($order->getStoreId());
					$languageCode = $storeData->getCode();
					$deductAmount = abs($results[0]);

					if($deductAmount && $deductAmount > 0) {
						$newjsonData = [
							"website" => "www.sololuxury.com",
							"platform_id" => $order->getCustomerId(),
							"amount" => $deductAmount,
							"lang_code" => $languageCode,
						];

						$url = 'https://erp.theluxuryunlimited.com/api/deduct-credit';
					    $curl = $this->curlFactory->create();
						$curl->setOption(CURLOPT_RETURNTRANSFER, 1);
						$curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
						$curl->post($url, json_encode($newjsonData));

					    try {
					      	$result   = $curl->getBody();
					      	$response = json_decode($result,true);
					      	$status = $response['status'];
					      	$message = $response['message'];
					      	$response['incrementId'] =  $order->getIncrementId();

					      	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/StoreCreditSync.log');
							$logger = new \Zend\Log\Logger();
							$logger->addWriter($writer);
							$logger->info($response);

							if($status == 'success') {
					      		$order->setStoreCreditSyncFlag(1);
					      		$order->save();
					      	}

					     	if($status != 'success'){
					     		$order->setStoreCreditSyncFlag(2);
					      		$order->save();
					      	}

					    } catch(Exception $e) {
					      	$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/StoreCreditSync.log');
							$logger = new \Zend\Log\Logger();
							$logger->addWriter($writer);
							$logger->info($e->getMessage().' == '.$incrementId);
					    }
					} else {
						$order->setStoreCreditSyncFlag(3);
						$order->save();
					}
        		} else {
				 	$order->setStoreCreditSyncFlag(3);
					$order->save();
				}
        	}
		}
		return true;
    }
}
