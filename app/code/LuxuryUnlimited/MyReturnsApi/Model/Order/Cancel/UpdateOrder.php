<?php

namespace LuxuryUnlimited\MyReturnsApi\Model\Order\Cancel;

use Dynamic\Mytickets\Model\Mytickets;
use Dynamic\OrderHistory\Model\OrderHistory;
use Dynamic\OrderHistory\Model\OrderHistoryFactory;
use Dynamic\OrderHistory\Model\OrderhistoryItemFactory;
use Dynamic\Orderreturn\Model\DeleteOrderItem;
use Exception;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class UpdateOrder
{

    /**
     * @var DeleteOrderItem
     */
    protected $deleteOrderItem;
    /**
     * @var order
     */
    protected $order;
    /**
     * @var OrderHistoryFactory
     */
    protected $orderHistoryFactory;
    /**
     * @var OrderHistory
     */
    protected $orderHistory;
    /**
     * @var OrderhistoryItemFactory
     */
    protected $historyItemFactory;
    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;
    /**
     * @var Mytickets
     */
    protected $mytickets;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItem;

    /**
     * @param DeleteOrderItem              $deleteOrderItem
     * @param SerializerInterface          $serializer
     * @param OrderHistoryFactory          $orderHistoryFactory
     * @param OrderHistory                 $orderHistory
     * @param OrderhistoryItemFactory      $historyItemFactory
     * @param TimezoneInterface            $timezoneInterface
     * @param OrderItemRepositoryInterface $orderItem
     * @param Order                        $order
     * @param Mytickets                    $mytickets
     * @param LoggerInterface              $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        DeleteOrderItem $deleteOrderItem,
        SerializerInterface $serializer,
        OrderHistoryFactory $orderHistoryFactory,
        OrderHistory $orderHistory,
        OrderhistoryItemFactory $historyItemFactory,
        TimezoneInterface $timezoneInterface,
        OrderItemRepositoryInterface $orderItem,
        Order $order,
        Mytickets $mytickets,
        LoggerInterface $logger
    ) {
        $this->deleteOrderItem     = $deleteOrderItem;
        $this->serializer          = $serializer;
        $this->orderHistoryFactory = $orderHistoryFactory;
        $this->orderHistory        = $orderHistory;
        $this->historyItemFactory  = $historyItemFactory;
        $this->timezoneInterface   = $timezoneInterface;
        $this->orderItem           = $orderItem;
        $this->mytickets           = $mytickets;
        $this->logger              = $logger;
        $this->order               = $order;
    }

    /**
     *  Update the Order after Cancelled
     *
     * @param OrderInterface $order
     * @param array          $cancelForm
     *
     * @return array
     */
    public function updateOrder($order, $cancelForm)
    {
        $orderOriginalId = $cancelForm['order_id'];
        $reason          = $cancelForm['reason'];

        try {
            $historyorder = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderOriginalId);

            /**** save Order original Data as history  -Pritam ****/
            if ($historyorder->getSize() <= 0) {
                $this->createOrderHistory($orderOriginalId, 0);
            }

            /**** Update original Order and Order Item -Pritam ****/
            $orderItems = $order->getAllItems();
            if (count($orderItems)) {
                foreach ($orderItems as $orderItem) {
                    $itemId = $orderItem->getItemId();
                    $this->deleteOrderItem->deleteOrderItem($orderOriginalId, $itemId);
                }
            }

            // Add 'order Cancel Comment' to 'sales_order' table
            $currDate      = $this->timezoneInterface->date()->format('d.m.Y @ h:i A');
            $cancelComment = __('Canceled on') . ' ' . $currDate;
            $order->setCancelComment($cancelComment);
            $order->save();

            $orderItems = $order->getAllItems();
            $itemsSkus  = '';
            foreach ($orderItems as $item) {
                $itemsSkus = $itemsSkus . $item->getSKU() . ',';
            }
            $ticketData                = [];
            $ticketData['name']        = $order->getCustomerFirstname();
            $ticketData['last_name']   = $order->getCustomerLastname();
            $ticketData['email']       = $order->getCustomerEmail();
            $ticketData['phone']       = $order->getCustomerFirstname();
            $ticketData['brand']       = __('Order # : %1', $order->getIncrementId());
            $ticketData['style']       = $itemsSkus;
            $ticketData['keyword']     = __('Order Request');
            $ticketData['image']       = '';
            $ticketData['remarks']     = __('Order Request for Order  : #%1', $order->getIncrementId());
            $ticketData['remarks']     = $ticketData['remarks'] . " ," . __('Reason') . ' :' . $reason;
            $ticketData['ticket_code'] = '';
            $ticketData['lang_code']   = $cancelForm['lang_code'];
            $ticketData['status']      = 0;
            $ticketData['customer_id'] = $order->getCustomerId();
            $ticketData['ticket_type'] = 3;
            $this->mytickets->setData($ticketData);
            $this->mytickets->save();

            return [
                'errors'  => false,
                'message' => __(
                    'Order successfully updated and  a Request Ticket has been created, You will get Ticket ID shortly.'
                )
            ];

        } catch (Exception $e) {
            return [
                'errors'  => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     *  Create Order History
     *
     * @param int|string $orderId
     * @param int|string $orderReturnId
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function createOrderHistory($orderId, $orderReturnId)
    {
        try {
            //Check Histiry for order Id already exist or not
            $orderHistoryExist = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderId);
            if ($orderHistoryExist->getSize() <= 0) {
                $order        = $this->order->load($orderId);
                $orderItems   = $order->getAllItems();
                $orderhistory = $this->orderHistoryFactory->create();
                $orderhistory->setData('order_id', $orderId);
                $orderhistory->setData('orderreturn_id', $orderReturnId);
                foreach ($order->getData() as $field => $value) {
                    if ($field == 'entity_id') {
                        continue;
                    }
                    $orderhistory->setData($field, $value);
                }
                $orderhistory->save();
                $historyOrderId = $orderhistory->getEntityId();
                if (count($orderItems)) {
                    foreach ($orderItems as $orderItem) {
                        $orderhistoryItem = $this->historyItemFactory->create();
                        foreach ($orderItem->getData() as $field => $value) {
                            if ($field == 'item_id') {
                                continue;
                            } elseif ($field == 'entity_id'
                                      || $field == 'extension_attributes'
                                      || $field == 'product_image') {
                                continue;
                            } elseif ($field == 'order_id') {
                                $field = 'history_order_id';
                                $value = $historyOrderId;
                            } elseif ($field == 'product_options') {
                                $value = $this->serializer->serialize($value);
                            }
                            $orderhistoryItem->setData($field, $value);
                            if ($field == 'base_discount_refunded') {
                                $orderhistoryItem->setData('base_discount_refunded', $value);
                                break;
                            }
                        }
                        $orderhistoryItem->save();
                    }
                }

            }

            $result = [
                'errors'  => false,
                'message' => __('Order history created successfully.')
            ];

        } catch (Exception $exception) {
            $result = [
                'errors'  => true,
                'message' => $exception->getMessage()
            ];
        }

        return $result;
    }
}
