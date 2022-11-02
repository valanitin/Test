<?php

namespace Dynamic\CustomerDataSync\Cron;

class CustomerAddressDataQueue
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
     * CustomerRepository
     *
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * AddressFactory
     *
     * @var \Magento\Customer\Model\Address
     */
    protected $address;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface  $customerRepository
     * @param \Magento\Customer\Model\Address  $addressFactory
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\Address $address
    ) { 
        $this->_resource = $resource;
        $this->curlFactory = $curlFactory;
        $this->customerRepository = $customerRepository;
        $this->address = $address;
    }

	public function execute()
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customeraddresscron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("cron run successfully...");
		$this->generate();
		return $this;
	}

	public function generate() {

		$this->updateAddressSyncFlag();

        $connection  = $this->_resource->getConnection();
	    $tableName = $this->_resource->getTableName('customer_grid_flat');
	    $select_sql = "Select * FROM " . $tableName. " WHERE address_sync = 1";
	    $customerCollection = $connection->fetchAll($select_sql);

		if(count($customerCollection) > 0 && !empty($customerCollection)) {

			foreach ($customerCollection as $customer) {

				$customerCollection = $this->customerRepository->getById($customer["entity_id"]);

				if($customerCollection->getEmail()) {
					$customerEmail = $customerCollection->getEmail();
				    $website = 'www.sololuxury.com';

	 				$addressData = $this->address->getCollection()->addFieldToFilter("parent_id", ["eq" => $customerCollection->getId()]);

	 				if(!empty($addressData) && count($addressData) > 0) {

	 					$newjsonData = [];

	 					foreach ($addressData as $address) {
	 						$newjsonData[] = [
								"entity_id" => $address->getId(),
								"address_type" => "shipping",
								"region" => $address->getRegion(),
								"region_id" => $address->getRegionId(),
								"postcode" => $address->getPostcode(),
								"firstname" => $address->getFirstname(),
								"lastname" => $address->getLastname(),
								"middlename" => $address->getMiddlename(),
								"company" => $address->getCompany(),
								"country_id" => $address->getCountryId(),
								"telephone" => $address->getTelephone(),
								"prefix" => $address->getPrefix(),
								"street" => isset($address->getStreet()[0]) ? $address->getStreet()[0] : ""
						    ];
	 					}

					    $url = 'https://erp.theluxuryunlimited.com/api/customer/add_customer_data?website='.$website.'&email='.$customerEmail;

						$curl = $this->curlFactory->create();
						$curl->setOption(CURLOPT_RETURNTRANSFER, 1);
						$curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
						$curl->post($url, json_encode($newjsonData));
			            $result   = $curl->getBody();
			            $response = [json_decode($result,true)];

			            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customeraddresscron.log');
						$logger = new \Zend\Log\Logger();
						$logger->addWriter($writer);
						$logger->info($response);

						if(count($response) > 0 && !empty($response)) {
			            	foreach ($response as $responseValue) {
			            		if(isset($responseValue["code"]) && $responseValue["code"] == 200) {
			            			$sql = "Update " . $tableName . " Set address_sync = 2 where entity_id = ".$customer["entity_id"];
									$connection->query($sql);
			            		} else {
			            			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customeraddresscron.log');
									$logger = new \Zend\Log\Logger();
									$logger->addWriter($writer);
									$logger->info($customer["email"]. ' - Failed');
			            		}
			            	}
			            }
	 				}
				}
			}
		}

		return true;
    }

    public function updateAddressSyncFlag() {
    	$connection  = $this->_resource->getConnection();
	    $tableName = $this->_resource->getTableName('customer_grid_flat');
		$sql = "Update " . $tableName . " Set address_sync = 1 where address_sync IS NULL OR address_sync = 0";
		$connection->query($sql);
    }
}
