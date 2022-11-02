<?php

namespace Dynamic\Orderreturn\Cron;

class OrderreturnQueue
{
    /**
     * Orderreturn
     *
     * @var \Dynamic\Orderreturn\Model\Orderreturn
     */
    protected $orderreturn;

    /**
     * Dynamic helper
     *
     * @var \Dynamic\Orderreturn\Helper\Data
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
     * @param \Dynamic\Orderreturn\Model\Orderreturn $orderreturn
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
     */
    public function __construct(
        \Dynamic\Orderreturn\Model\Orderreturn $orderreturn,
        \Dynamic\Orderreturn\Helper\Data $helper,
        \Magento\Framework\HTTP\Client\CurlFactory $curlFactory
    ) { 
        $this->orderreturn = $orderreturn;
        $this->helper = $helper;
        $this->curlFactory = $curlFactory;
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/orderReturnCron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("cron run successfully...");
        $this->generate();
        return $this;
    }

    public function generate() {

        $orderReturnQueue = $this->orderreturn->getCollection()->addfieldtofilter('status', 0);

        if(count($orderReturnQueue) > 0 && !empty($orderReturnQueue)) {

            foreach ($orderReturnQueue as $orderReturn) {

                $url = "https://erp.theluxuryunlimited.com/api/return-exchange-buyback/create";

                $storeManager  = $this->helper->getStoreManager();
                $storeCode = $storeManager->getStore()->getCode();
                $siteUrl = $this->helper->getScopeConfig()->getValue("web/secure/base_url");
                
                $curl = $this->curlFactory->create();
                $curl->setOption(CURLOPT_RETURNTRANSFER, 1);
                $curl->setOption(CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                $curl->post($url, json_encode($orderReturn->getData()));
                $result   = $curl->getBody();
                $response = [json_decode($result,true)];

                if(count($response) > 0 && !empty($response)) {
                    foreach ($response as $responseValue) {
                        if(isset($responseValue["status"]) && $responseValue["status"] == "success") {
                            $orderReturn->setStatus(1);
                            $orderReturn->setErpStatus(json_encode($response));
                            $orderReturn->save();
                        } else if(isset($responseValue["status"]) && $responseValue["status"] == "failed") {
                            $orderReturn->setStatus(2);
                            $orderReturn->setErpStatus(json_encode($response));
                            $orderReturn->save();
                        }
                    }
                }
            }
        }
        return $this;
    }
}
