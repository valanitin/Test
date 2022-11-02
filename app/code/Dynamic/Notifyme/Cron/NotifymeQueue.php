<?php

namespace Dynamic\Notifyme\Cron;

class NotifymeQueue
{
    /**
     * Notifyme
     *
     * @var \Dynamic\Notifyme\Model\Notifyme
     */
    protected $notifyme;

    /**
     * CurlFactory
     *
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $curlFactory;

    /**
     * Constructor
     *
     * @param \Dynamic\Notifyme\Model\Notifyme $notifyme
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     */
    public function __construct(
        \Dynamic\Notifyme\Model\Notifyme $notifyme,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    ) { 
        $this->notifyme = $notifyme;
        $this->curlFactory = $curlFactory;
    }

	public function execute()
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/notifymeCron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("cron run successfully...");
		$this->generate();
		return $this;
	}

	public function generate() {

        $notifymeQueue = $this->notifyme->getCollection()->addfieldtofilter('status', 0);

		if(count($notifymeQueue) > 0 && !empty($notifymeQueue)) {

			foreach ($notifymeQueue as $notifyme) {

				$url = "https://erp.theluxuryunlimited.com/api/out-of-stock-subscription";

				$data = [
				    "email" => $notifyme->getEmail(),
				    "sku" => $notifyme->getProductSku(),
				    "size" => ($notifyme->getProductSize()) ? $notifyme->getProductSize() : "",
				    "website" => "www.sololuxury.com"
				];

				$curl = $this->curlFactory->create();
				$curl->setOption(CURLOPT_RETURNTRANSFER, 1);
				$curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$curl->post($url, json_encode($data));
	            $result   = $curl->getBody();
	            $response = [json_decode($result,true)];

	            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/notifymeCron.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info(print_r($response, true));

	            if(count($response) > 0 && !empty($response)) {
	            	foreach ($response as $responseValue) {
	            		if(isset($responseValue["code"]) && $responseValue["code"] == "success") {
	            			$notifyme->setStatus(1);
                			$notifyme->save();
	            		} else {
	            			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/notifymeCron.log');
							$logger = new \Zend\Log\Logger();
							$logger->addWriter($writer);
							$logger->info($responseValue["message"]);
	            		}
	            	}
	            }
			}
		}
    }
}
