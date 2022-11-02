<?php

namespace Dynamic\Abandonedcartapi\Cron;

class AbandonedcartapiQueue
{
    /**
     * Abandonedcartapi
     *
     * @var \Dynamic\Abandonedcartapi\Model\Abandonedcartapi
     */
    protected $abandonedcartapi;

    /**
     * CurlFactory
     *
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $curlFactory;

    /**
     * Constructor
     *
     * @param \Dynamic\Abandonedcartapi\Model\Abandonedcartapi $abandonedcartapi
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     */
    public function __construct(
        \Dynamic\Abandonedcartapi\Model\Abandonedcartapi $abandonedcartapi,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    ) { 
        $this->abandonedcartapi = $abandonedcartapi;
        $this->curlFactory = $curlFactory;
    }

	public function execute()
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/abandonedcartapiCron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("cron run successfully...");
		$this->generate();
		return $this;
	}

	public function generate() {

        $abandonedcartapiQueue = $this->abandonedcartapi->getCollection()->addfieldtofilter('status', 0);

		if(count($abandonedcartapiQueue) > 0 && !empty($abandonedcartapiQueue)) {

			foreach ($abandonedcartapiQueue as $abandonedcartapi) {

				$url = "https://erp.theluxuryunlimited.com/api/customer/add_cart_data";

				$data = [
				    "name" => $abandonedcartapi->getName(),
				    "email" => $abandonedcartapi->getEmail(),
				    "website" => "www.sololuxury.com",
				    "lang_code" => $abandonedcartapi->getLangCode(),
				    "item_info" => json_decode($abandonedcartapi->getItemInfo(), true)
				];

				$curl = $this->curlFactory->create();
				$curl->setOption(CURLOPT_RETURNTRANSFER, 1);
				$curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$curl->post($url, json_encode($data));
	            $result   = $curl->getBody();
	            $response = [json_decode($result,true)];

	            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/abandonedcartapiCron.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($response);

	            if(count($response) > 0 && !empty($response)) {
	            	foreach ($response as $responseValue) {
	            		if(isset($responseValue["status"]) && $responseValue["status"] == "success") {
	            			$abandonedcartapi->setStatus(1);
                			$abandonedcartapi->save();
	            		} else if(isset($responseValue["status"]) && $responseValue["status"] == "failed") {
	            			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/abandonedcartapiCron.log');
							$logger = new \Zend\Log\Logger();
							$logger->addWriter($writer);
							$logger->info($responseValue["message"]. ' - Failed');
	            		}
	            	}
	            }
			}
		}

		return true;
    }
}
