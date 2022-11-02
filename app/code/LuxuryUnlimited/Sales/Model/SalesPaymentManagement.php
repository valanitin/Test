<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Sales\Model;

use LuxuryUnlimited\Sales\Api\SalesPaymentManagementInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory as HistoryCollection;
use Magento\Sales\Model\ResourceModel\Order\Payment\CollectionFactory as PaymentCollection;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\CollectionFactory as TransactionCollection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\OrderFactory;

class SalesPaymentManagement implements SalesPaymentManagementInterface
{
    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Json $json
     * @param HistoryCollection $historyCollection
     * @param PaymentCollection $paymentCollection
     * @param TransactionCollection $transactionCollection
     * @param OrderFactory $orderFactory
     * @param CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        LoggerInterface $logger,
        Json $json,
        HistoryCollection $historyCollection,
        PaymentCollection $paymentCollection,
        TransactionCollection $transactionCollection,
        OrderFactory $orderFactory,
        CollectionFactory $orderCollectionFactory
    ) {
        $this->logger = $logger;
        $this->json = $json;
        $this->historyCollection = $historyCollection;
        $this->paymentCollection = $paymentCollection;
        $this->transactionCollection = $transactionCollection;
        $this->orderFactory = $orderFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
    }

    /**
     * Get Sales Payment Data
     *
     * @param string $date
     * @return string
     */
    public function getSalesData($date)
    {
        try {
            $this->logger->info('-- Sales data api call --');
            $orders = $this->getOrder($date);
            if (empty($orders)) {
                return [
                    'message' => 'There is no order for this Date.'
                ];
            }
            foreach($orders as $key => $order){
                $result[$key] = [
                    ['orderStatusHistory' => $this->getOrderStatusHistory($order['entity_id'])],
                    ['orderPayment' => $this->getOrderPayment($order['entity_id'])],
                    ['paymentTransaction' => $this->getPaymentTransaction($order['entity_id'])]
                ];
            }
        } catch (\Exception $e) {
            $this->logger->info("Sales data Api call---" . $e);
            return ['error' => $e->getMessage()];
        }

        return $result;
    }

    /**
     * Get Order Status History
     *
     * @param int $id
     * @return array
     */
    public function getOrderStatusHistory($id)
    {
        $result = [];
        $orderStatusHistory = $this->historyCollection->create();
        $history = $orderStatusHistory->addAttributeToFilter('parent_id', ['eq' => $id]);
        if ($history->getSize() > 0) {
            $result = $history->getData();
        }

        return $result;
    }

    /**
     * Get Order Payment
     *
     * @param int $id
     * @return array
     */
    public function getOrderPayment($id)
    {
        $result = [];
        $paymentCollection = $this->paymentCollection->create();
        $payment = $paymentCollection->addAttributeToFilter('parent_id', ['eq' => $id]);
        if ($payment->getSize() > 0) {
            $result = $payment->getData();
        }

        return $result;
    }

    /**
     * Get Payment Transaction
     *
     * @param int $id
     * @return array
     */
    public function getPaymentTransaction($id)
    {
        $result = [];
        $transactionFactory = $this->transactionCollection->create();
        $transaction = $transactionFactory->addAttributeToFilter('order_id', ['eq' => $id]);
        if ($transaction->getSize() > 0) {
            $result = $transaction->getData();
        }

        return $result;
    }

    /**
     * Get order
     *
     * @param String $date
     * @return bool
     */
    public function getOrder($date)
    {        
        try {
            $Date = date("Y-m-d",strtotime($date));
            $startDate = $Date." 00:00:00";
            $endDate = $Date." 23:59:59";
            $orders = $this->orderCollectionFactory->create()
                        ->addAttributeToFilter('created_at', array('from'=>$startDate, 'to'=>$endDate))
                        ->addFieldToSelect('entity_id')
                        ->getData();
            return $orders;
        } catch (NoSuchEntityException $e) {
            return [];
        }
    }
}
