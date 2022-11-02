<?php

namespace Dynamic\CustomerDataSync\Cron;

class CustomerDataQueue
{
    /**
     * CustomerFactory
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * CurlFactory
     *
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $curlFactory;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    ) { 
        $this->_resource = $resource;
        $this->curlFactory = $curlFactory;
    }

	public function execute()
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customerdatacron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("cron run successfully...");
		$this->generate();
		return $this;
	}

	public function generate() {

	    $this->updateAccountSyncFlag();

      	$connection  = $this->_resource->getConnection();
	    $tableName = $this->_resource->getTableName('customer_grid_flat');
	    $select_sql = "Select * FROM " . $tableName. " WHERE account_sync = 1";
	    $customerCollection = $connection->fetchAll($select_sql);

        if(count($customerCollection) > 0 && !empty($customerCollection)) {

			foreach ($customerCollection as $customer) {

				$url = "https://erp.theluxuryunlimited.com/api/magento/customer-reference";

				$data = [
					'name' => $customer["name"],
					'email' => $customer["email"],
					'website'   => "WWW.SOLOLUXURY.COM",
					'dob' => $customer["dob"],
					'wedding_anniversery' => '',
					'phone'=> '',
					'platform_id'=> $customer["entity_id"]
			    ];

				$curl = $this->curlFactory->create();
				$curl->setOption(CURLOPT_RETURNTRANSFER, 1);
				$curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$curl->post($url, json_encode($data));
	            $result   = $curl->getBody();
	            $response = [json_decode($result,true)];

	            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customerdatacron.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($response);

	            if(count($response) > 0 && !empty($response)) {
	            	foreach ($response as $responseValue) {
	            		if(isset($responseValue["message"]) && $responseValue["message"] == "Saved SucessFully") {
	            			$sql = "Update " . $tableName . " Set account_sync = 2 where entity_id = ".$customer["entity_id"];
							$connection->query($sql);
	            		} else {
	            			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customerdatacron.log');
							$logger = new \Zend\Log\Logger();
							$logger->addWriter($writer);
							$logger->info($customer["email"]. ' - Failed');
	            		}
	            	}
	            }
			}
		}

		return true;
    }

    public function updateAccountSyncFlag() {
    	$connection  = $this->_resource->getConnection();
	    $tableName = $this->_resource->getTableName('customer_grid_flat');
		$sql = "Update " . $tableName . " Set account_sync = 1 where account_sync IS NULL OR address_sync = 0";
		$connection->query($sql);
    }
}
