<?php

namespace Dynamic\Mytickets\Cron;

class MyticketsQueue
{
    /**
     * Mytickets
     *
     * @var \Dynamic\Mytickets\Model\Mytickets
     */
    protected $mytickets;

    /**
     * CurlFactory
     *
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $curlFactory;

    /**
     * Constructor
     *
     * @param \Dynamic\Mytickets\Model\Mytickets $mytickets
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     */
    public function __construct(
        \Dynamic\Mytickets\Model\Mytickets $mytickets,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    ) { 
        $this->mytickets = $mytickets;
        $this->curlFactory = $curlFactory;
    }

	public function execute()
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/myticketsCron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("cron run successfully...");
		$this->generate();
		return $this;
	}

	public function generate() {

        $myticketsQueue = $this->mytickets->getCollection()->addfieldtofilter('status', 0);

		if(count($myticketsQueue) > 0 && !empty($myticketsQueue)) {

			foreach ($myticketsQueue as $mytickets) {

				$url = "https://erp.theluxuryunlimited.com/api/ticket/create";

				$data = [
				    "name" => $mytickets->getName(),
				    "last_name" => $mytickets->getLastName(),
				    "email" => $mytickets->getEmail(),
				    "type_of_inquiry" => "Special Request",
				    "subject" => "Special Request | SoloLuxury",
				    "message" => $mytickets->getRemarks(),
				    "source_of_ticket" => "www.sololuxury.com",
				    "phone_no" => $mytickets->getPhone(),
				    "brand" => $mytickets->getBrand(),
				    "style" => $mytickets->getStyle(),
				    "keyword" => $mytickets->getKeyword(),
				    "image" => $mytickets->getImage(),
				    "lang_code" => $mytickets->getLangCode()
				];

				$curl = $this->curlFactory->create();
				$curl->setOption(CURLOPT_RETURNTRANSFER, 1);
				$curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$curl->post($url, json_encode($data));
	            $result   = $curl->getBody();
	            $response = [json_decode($result,true)];

	            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/myticketsCron.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info($response);

	            if(count($response) > 0 && !empty($response)) {
	            	foreach ($response as $responseValue) {
	            		if(isset($responseValue["status"]) && $responseValue["status"] == "success") {
	            			$ticketCode = (isset($responseValue["data"]["id"])) ? $responseValue["data"]["id"] : '';
	            			$mytickets->setTicketCode($ticketCode);
	            			$mytickets->setStatus(1);
                			$mytickets->save();
	            		} else if(isset($responseValue["status"]) && $responseValue["status"] == "failed") {
	            			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/myticketsCron.log');
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
