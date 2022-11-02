<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\NewsLetterSubscription\Cron;

use LuxuryUnlimited\NewsLetterSubscription\Model\Config;
use LuxuryUnlimited\NewsLetterSubscription\Logger\Logger;
use LuxuryUnlimited\NewsLetterSubscription\Model\Service\Api;
use Magento\Newsletter\Model\ResourceModel\Subscriber\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Newsletter\Model\SubscriberFactory;

class SyncSubscribers
{
    public const REGISTRATION_PATH = 'api/mailinglist/add';

    public const WEBSITE = 'WWW.SOLOLUXURY.COM';

    public const CURL_SUCCESS_STATUS = '200';

    public const CURL_SUB_MESSAGE = 'You are already subscribed to our newsletter.';
    
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Api
     */
    protected $api;

    /**
     * @var CollectionFactory
     */
    protected $subscriberCollectionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

   
    /**
     * Constructor
     *
     * @param Config $config
     * @param Logger $logger
     * @param Api $api
     * @param CollectionFactory $subscriberCollectionFactory
     * @param StoreManagerInterface $storeManager
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        Config $config,
        Logger $logger,
        Api $api,
        CollectionFactory $subscriberCollectionFactory,
        StoreManagerInterface $storeManager,
        SubscriberFactory $subscriberFactory
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->api = $api;
        $this->subscriberCollectionFactory = $subscriberCollectionFactory;
        $this->storeManager = $storeManager;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * Cron execute
     */
    public function execute()
    {
        try{
            if ($this->config->getEnabled()) {
                $this->logger->info('-- Subscriber sync cron --');
                //Execute the Subscriber sync
                $subscribers = $this->subscriberCollectionFactory->create()->addFieldToFilter('erp_sync_flag',['eq' => 0]);
                if ($subscribers->count() > 0) {
                    foreach ($subscribers as $subscriber) {
                        $this->pushSubscriber($subscriber->getData());
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->info("ERP Api call---" . $e);
            return [];
        }
        
        return $this;
    }

    /**
     * Push Subscriber
     *
     * @param string $subscriber
     */
    public function pushSubscriber($subscriber)
    {   
        $email = $subscriber['subscriber_email'];
        $website = self::WEBSITE;
        $storeData = $this->storeManager->getStore($subscriber['store_id']);
        $storeCode = (string)$storeData->getCode();
        $params = [
            "website" => $website,
            "email" => $email,
            "lang_code" => $storeCode
        ];
        $path = self::REGISTRATION_PATH;
        $erpResponse = $this->api->call($path, $params, 'POST');
        if ($erpResponse) {
            if($erpResponse['code'] == self::CURL_SUCCESS_STATUS || $erpResponse['message'] == self::CURL_SUB_MESSAGE){
                $emailSubscriber = $this->subscriberFactory->create()->load($subscriber['subscriber_id']);
                $emailSubscriber->setData("erp_sync_flag",1);
                $emailSubscriber->save();
            }
        }else{
            $this->logger->info("ERP Api call Failed for email - ".$email);
        }
    }
}
