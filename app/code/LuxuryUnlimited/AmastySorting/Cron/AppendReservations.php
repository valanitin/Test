<?php

declare(strict_types=1);

namespace LuxuryUnlimited\AmastySorting\Cron;

use LuxuryUnlimited\AmastySorting\Model\ReservationsFactory;
use LuxuryUnlimited\AmastySorting\Model\ResourceModel\Reservations\CollectionFactory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\InventoryReservationsApi\Model\AppendReservationsInterface;
use Magento\InventoryReservationsApi\Model\ReservationBuilderInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;

class AppendReservations
{

    /**
     * @var ReservationsFactory
     */
    protected $reservationsFactory;
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollectionFactory;
    /**
     * @var CollectionFactory
     */
    protected $collection;
    /**
     * @var ReservationBuilderInterface
     */
    private $reservationBuilder;
    /**
     * @var AppendReservationsInterface
     */
    private $appendReservations;
    /**
     * @var StockResolverInterface
     */
    private $stockResolver;

    /**
     * Constructor
     *
     * @param ReservationsFactory $reservationsFactory
     * @param ReservationBuilderInterface $reservationBuilder
     * @param AppendReservationsInterface $appendReservations
     * @param StockResolverInterface $stockResolver
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    public function __construct(
        ReservationsFactory $reservationsFactory,
        ReservationBuilderInterface $reservationBuilder,
        AppendReservationsInterface $appendReservations,
        StockResolverInterface $stockResolver,
        CollectionFactory $collectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->reservationBuilder = $reservationBuilder;
        $this->appendReservations = $appendReservations;
        $this->stockResolver = $stockResolver;
        $this->collection = $collectionFactory;
        $this->reservationsFactory = $reservationsFactory;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        $reservations = [];
        $firstItem = $this->reservationsFactory->create()->getCollection()->getFirstItem();
        if ($firstItem && $firstItem->getId()) {
            $firstItem->delete();
        }
        $data = $this->reservationsFactory->create();
        $data->setData(1)->save();
        $firstItem = $data->getCollection()->getFirstItem();

        $todayDate = $firstItem->getTimeOfReservations();

        $convertDate = date("Y-m-d h:i:s", strtotime("-1 minutes", strtotime($todayDate)));

        $orders = $this->orderCollectionFactory
            ->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter(
                'created_at',
                ['gteq' => $convertDate]
            );

        foreach ($orders as $order) {
            foreach ($order->getAllVisibleItems() as $item) {
                if ($item->getParentItemId() 
                // && $orderItem->getSku() == $reservation->getSku() 
                    && $item->getParentItem()
                        ->getProductType() == Configurable::TYPE_CODE) {
                    $salesEvent = [
                        "event_type" => 'order_placed',
                        "object_type" => 'object_type',
                        "object_id" => $item->getOrderId()
                    ];
                    $metadata = (json_encode($salesEvent) === false) ? null : json_encode($salesEvent);
                    $stock = $this->stockResolver
                        ->execute(
                            SalesChannelInterface::TYPE_WEBSITE,
                            $item
                                ->getStore()
                                ->getWebsite()
                                ->getCode()
                        );
                    $stockId = (int)$stock->getStockId();
                    $reservations[] = $this->reservationBuilder
                        ->setSku($item->getSku())
                        ->setQuantity((float)$item->getQtyOrdered())
                        ->setStockId($stockId)
                        ->setMetadata($metadata)
                        ->build();
                }
            }

        }
        if (!empty($reservations)) {
            $this->appendReservations->execute($reservations);
        }
    }
}
