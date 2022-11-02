<?php

namespace Dynamic\Referfriend\Cron;

class ReferfriendQueue
{
    /**
     * Referfriend
     *
     * @var \Dynamic\Referfriend\Model\Referfriend
     */
    protected $referfriend;

    /**
     * Dynamic helper
     *
     * @var \Dynamic\Referfriend\Helper\Data
     */
    protected $helper;

    /**
     * CurlFactory
     *
     * @var \Magento\Framework\HTTP\Client\CurlFactory
     */
    protected $curlFactory;

    /**
     * Constructor
     *
     * @param \Dynamic\Referfriend\Model\Referfriend $referfriend
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     */
    public function __construct(
        \Dynamic\Referfriend\Model\Referfriend $referfriend,
        \Dynamic\Referfriend\Helper\Data $helper,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    ) { 
        $this->referfriend = $referfriend;
        $this->helper = $helper;
        $this->curlFactory = $curlFactory;
    }

	public function execute()
	{
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/referFriendCron.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info("cron run successfully...");
		$this->generate();
		return $this;
	}

	public function generate() {

        $referFriendQueue = $this->referfriend->getCollection()->addfieldtofilter('status', 0);

		if(count($referFriendQueue) > 0 && !empty($referFriendQueue)) {

			foreach ($referFriendQueue as $referFriend) {

				$url = "https://erp.theluxuryunlimited.com/api/friend/referral/create";

				$storeManager  = $this->helper->getStoreManager();
				$storeCode = $storeManager->getStore()->getCode();
				$siteUrl = $this->helper->getScopeConfig()->getValue("web/secure/base_url");

				$data = [
				    "referrer_first_name" => $referFriend->getReferrerFirstName(),
				    "referrer_email" => $referFriend->getReferrerEmail(),
				    "referrer_phone" => $referFriend->getReferrerPhone(),
				    "referee_first_name" => $referFriend->getYourfirstname(),
				    "referee_email" => $referFriend->getYouremailaddress(),
				    "referee_phone" => $referFriend->getYourphonenumber(),
				    "website" => "www.sololuxury.com",
				    "lang_code" => $storeCode
				];

				$curl = $this->curlFactory->create();
				$curl->setOption(CURLOPT_RETURNTRANSFER, 1);
				$curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				$curl->post($url, json_encode($data));
	            $result   = $curl->getBody();
	            $response = [json_decode($result,true)];

	            if(count($response) > 0 && !empty($response)) {
	            	foreach ($response as $responseValue) {
	            		if(isset($responseValue["status"]) && $responseValue["status"] == "success") {
	            			$referrerCode = (isset($responseValue["referrer_code"])) ? $responseValue["referrer_code"] : '';
	            			$referFriend->setReferrerCode($referrerCode);
	            			$referFriend->setStatus(1);
                			$referFriend->save();
	            		} else if(isset($responseValue["status"]) && $responseValue["status"] == "failed") {
	            			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/referFriendCron.log');
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
