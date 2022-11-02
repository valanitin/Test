<?php

namespace Amasty\Checkout\Cron;

use Amasty\Checkout\Api\AdditionalFieldsManagementInterface;
use Amasty\Checkout\Model\Config;
use LuxuryUnlimited\AmastySorting\Model\ReservationsFactory;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class BeforeSubmitObserver
{

    /**
     * @var ReservationsFactory
     */
    protected $reservationsFactory;
    /**
     * @var CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var AdditionalFieldsManagementInterface
     */
    private $fieldsManagement;
    /**
     * @var Config
     */
    private $config;

    /**
     * BeforeSubmitObserver constructor.
     *
     * @param AdditionalFieldsManagementInterface $fieldsManagement
     * @param ReservationsFactory $reservationsFactory
     * @param CollectionFactory $orderCollectionFactory
     * @param Config $config
     */
    public function __construct(
        AdditionalFieldsManagementInterface $fieldsManagement,
        ReservationsFactory $reservationsFactory,
        CollectionFactory $orderCollectionFactory,
        Config $config

    ) {
        $this->fieldsManagement = $fieldsManagement;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->reservationsFactory = $reservationsFactory;
        $this->config = $config;

    }

    /**
     * This function call execute
     *
     * This function Call execute
     */
    public function execute()
    {
        if (!$this->config->isEnabled()) {
            return;
        }
        /** @var Quote $order */

        $data = $this->reservationsFactory->create();
        $firstItem = $data->getCollection()->getLastItem();
        $todayDate = $firstItem->getTimeOfReservations();
        $convertDate = date("Y-m-d h:i:s", strtotime("-1 minutes", strtotime($todayDate)));
        $orders = $this->orderCollectionFactory->create()->addAttributeToSelect('*')->addFieldToFilter('created_at', ['gteq' => $convertDate]);

        foreach ($orders as $order) {
            $quoteId = $order->getQuoteId();
            $fields = $this->fieldsManagement->getByQuoteId($quoteId);
            if ($fields->getComment()) {
                /** @var Order $order */
                $history = $order->addStatusHistoryComment($fields->getComment());
                $history->setIsVisibleOnFront(true);
                $history->setIsCustomerNotified(true);
            }
        }

    }

}
