<?php
/**
 * Copyright © Swarming Technology, LLC. All rights reserved.
 */
namespace Swarming\StoreCredit\Cron\Order;

use Swarming\StoreCredit\Api\Data\TransactionInterfaceFactory;
use Swarming\StoreCredit\Service\CreditsCustomer;
use Magento\Sales\Model\OrderFactory;

class PlaceAfter
{
    private $customerCredit;
    /**
     * @var \Swarming\StoreCredit\Model\Config\General
     */
    private $configGeneral;

    /**
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    private $orderAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Model\Order\Relation\PlaceProcessor
     */
    private $orderRelationPlaceProcessor;

    /**
     * @var \Swarming\StoreCredit\Api\Data\TransactionInterfaceFactory
     */
    private $transactionFactory;

    private $orderFactory;

    /**
     * @param \Swarming\StoreCredit\Model\Config\General $configGeneral
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Model\Order\Relation\PlaceProcessor $orderRelationPlaceProcessor
     * @param TransactionInterfaceFactory $transactionFactory
     */
    public function __construct(
        \Swarming\StoreCredit\Model\Config\General $configGeneral,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Model\Order\Relation\PlaceProcessor $orderRelationPlaceProcessor,
        TransactionInterfaceFactory $transactionFactory,
        CreditsCustomer $customerCredit,
        OrderFactory $orderFactory
    ) {
        $this->configGeneral = $configGeneral;
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->orderRelationPlaceProcessor = $orderRelationPlaceProcessor;
        $this->transactionFactory = $transactionFactory;
        $this->customerCredit = $customerCredit;
        $this->orderFactory = $orderFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute()
    {

        $transaction = $this->transactionFactory->create();

        $lastTransaction = $transaction->getCollection()->getLastItem();

        $lastTransactionData = $lastTransaction->getData();

        $lastProcessedOrderId = $lastTransactionData ? $lastTransactionData['order_id'] : 1;

        $orders = $this->orderFactory->create();

        $orderCollection = $orders->getCollection();

        $limit = count($orderCollection->getData()) - $lastProcessedOrderId;
        $orderCollection->getSelect()->limit($limit,$lastProcessedOrderId);

        foreach($orderCollection as $index => $order){



        if (!$this->configGeneral->isActive($order->getStoreId()) || !$order->getCustomerId()) {

            continue;
        }

        $orderCredits = $this->orderAttributeManagement->getForOrder($order);
        if ($orderCredits->getCredits() < 0.01) {

            continue;
        }

        $this->orderRelationPlaceProcessor->process($order, $orderCredits);

        }
       

       
    }

}
