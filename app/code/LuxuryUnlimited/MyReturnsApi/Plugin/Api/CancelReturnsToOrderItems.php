<?php

namespace LuxuryUnlimited\MyReturnsApi\Plugin\Api;

use LuxuryUnlimited\MyReturnsApi\Helper\ReturnAndCancel;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\OrderItemSearchResultInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Psr\Log\LoggerInterface;

class CancelReturnsToOrderItems
{
    /**
     * @var OrderItemExtensionFactory
     */
    protected $orderItemExtension;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var JsonSerializer
     */
    protected $jsonSerializer;
    /**
     * @var ReturnAndCancel
     */
    protected $returnAndCancel;

    /**
     * @param OrderItemExtensionFactory $orderItemExtension
     * @param ReturnAndCancel           $returnAndCancel
     */
    public function __construct(
        OrderItemExtensionFactory $orderItemExtension,
        ReturnAndCancel $returnAndCancel
    ) {
        $this->orderItemExtensionFactory = $orderItemExtension;
        $this->returnAndCancel           = $returnAndCancel;
    }

    /**
     * Adding the 'is_returnable' attribute in the Order Items
     *
     * @param OrderItemRepositoryInterface   $subject
     * @param OrderItemSearchResultInterface $searchResult
     *
     * @return OrderItemSearchResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(OrderItemRepositoryInterface $subject, OrderItemSearchResultInterface $searchResult)
    {

        foreach ($searchResult->getItems() as $order) {
            $this->afterGet($subject, $order);
        }

        return $searchResult;
    }

    /**
     * Adding the 'is_returnable' attribute in the Order Items
     *
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface           $orderItem
     *
     * @return OrderItemInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(OrderItemRepositoryInterface $subject, OrderItemInterface $orderItem)
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderItemExtensionFactory->create();
        }
        $extensionAttributes->setIsReturnable($this->returnAndCancel->isReturnable($orderItem, true));
        $extensionAttributes->setIsCancellable($this->returnAndCancel->isCancellable($orderItem, true));
        $orderItem->setExtensionAttributes($extensionAttributes);

        return $orderItem;
    }
}
