<?php

namespace LuxuryUnlimited\MyReturnsApi\Plugin\Api;

use Magento\Sales\Api\Data\OrderExtension;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use LuxuryUnlimited\MyReturnsApi\Helper\ReturnAndCancel;

class CancelReturnsToOrders
{
    /**
     * @var OrderExtensionFactory
     */
    protected $orderExtension;

    /**
     * @var ReturnAndCancel
     */
    protected $returnAndCancel;

    /**
     * @param OrderExtensionFactory $orderExtension
     * @param ReturnAndCancel       $returnAndCancel
     */
    public function __construct(
        OrderExtensionFactory $orderExtension,
        ReturnAndCancel $returnAndCancel
    ) {
        $this->orderExtensionFactory = $orderExtension;
        $this->returnAndCancel       = $returnAndCancel;
    }

    /**
     * Adding the 'is_cancellable' attribute in the Order
     *
     * @param OrderRepositoryInterface   $subject
     * @param OrderSearchResultInterface $orderSearchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $orderSearchResult)
    {

        foreach ($orderSearchResult->getItems() as $order) {
            $this->afterGet($subject, $order);
        }

        return $orderSearchResult;
    }

    /**
     *  Adding the 'is_cancellable' attribute in the Order
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface           $order
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        /** @var OrderExtension $extensionAttributes */
        $extensionAttributes = $order->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }
        $extensionAttributes->setIsCancellable($this->returnAndCancel->isCancellable($order));
        $extensionAttributes->setIsReturnable($this->returnAndCancel->isReturnable($order));
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }
}
