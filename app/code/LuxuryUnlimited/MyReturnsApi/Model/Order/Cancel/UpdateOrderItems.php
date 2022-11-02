<?php

namespace LuxuryUnlimited\MyReturnsApi\Model\Order\Cancel;

use Dynamic\Mytickets\Model\Mytickets;
use Dynamic\OrderHistory\Model\OrderHistory;
use Dynamic\OrderHistory\Model\OrderHistoryFactory;
use Dynamic\OrderHistory\Model\OrderhistoryItemFactory;
use Dynamic\Orderreturn\Model\DeleteOrderItem;
use Exception;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class UpdateOrderItems
{

    /**
     * @var DeleteOrderItem
     */
    protected $deleteOrderItem;
    /**
     * JsonFactory
     *
     * @var JsonFactory
     */
    protected $resultJsonFactory;
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
     * @var Mytickets
     */
    protected $myTickets;
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
     * @var TimezoneInterface
     */
    private $timezoneInterface;

    /**
     * @param DeleteOrderItem              $deleteOrderItem
     * @param SerializerInterface          $serializer
     * @param OrderHistoryFactory          $orderHistoryFactory
     * @param OrderHistory                 $orderHistory
     * @param OrderhistoryItemFactory      $historyItemFactory
     * @param Order                        $order
     * @param TimezoneInterface            $timezoneInterface
     * @param OrderItemRepositoryInterface $orderItem
     * @param Mytickets                    $myTickets
     * @param LoggerInterface              $logger
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        DeleteOrderItem $deleteOrderItem,
        SerializerInterface $serializer,
        OrderHistoryFactory $orderHistoryFactory,
        OrderHistory $orderHistory,
        OrderhistoryItemFactory $historyItemFactory,
        Order $order,
        TimezoneInterface $timezoneInterface,
        OrderItemRepositoryInterface $orderItem,
        Mytickets $myTickets,
        LoggerInterface $logger
    ) {
        $this->deleteOrderItem     = $deleteOrderItem;
        $this->serializer          = $serializer;
        $this->orderHistoryFactory = $orderHistoryFactory;
        $this->orderHistory        = $orderHistory;
        $this->historyItemFactory  = $historyItemFactory;
        $this->order               = $order;
        $this->timezoneInterface   = $timezoneInterface;
        $this->orderItem           = $orderItem;
        $this->myTickets           = $myTickets;
        $this->logger              = $logger;
    }

    /**
     * Update the order Items
     *
     * @param OrderInterface $order
     * @param array          $cancelForm
     *
     * @return array
     */
    public function updateOrderItems($order, $cancelForm)
    {
        $orderId     = $cancelForm['order_id'];
        $orderItemId = $cancelForm['item_id'];
        $reason      = $cancelForm['reason'];
        try {
            $historyOrder = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderId);
            /**** save Order original Data as history  -Pritam ****/
            if ($historyOrder->getSize() <= 0) {
                $this->createOrderHistory($orderId, 0);
            }
            /**** Update original Order and Order Item -Pritam ****/

            $orderItems = $order->getAllItems();
            if (count($orderItems)) {
                foreach ($orderItems as $orderItem) {
                    if ($orderItem->getItemId() == $orderItemId) {
                        $this->deleteOrderItem->deleteOrderItem($orderItemId, $orderItemId);
                        break;
                    }
                }
            }

            $currDate      = $this->timezoneInterface->date()->format('d.m.Y @ h:i A');
            $cancelComment = __('Canceled on') . ' ' . $currDate;
            $order->setCancelComment($cancelComment);
            $order->save();

            /**** Create Ticket on Item Cancel Request #16866 *****/
            $orderItem                 = $this->orderItem->get($orderItemId);
            $ticketData                = [];
            $ticketData['name']        = $order->getCustomerFirstname();
            $ticketData['last_name']   = $order->getCustomerLastname();
            $ticketData['email']       = $order->getCustomerEmail();
            $ticketData['phone']       = $order->getCustomerFirstname();
            $ticketData['brand']       = $orderItem->getName();
            $ticketData['style']       = $orderItem->getSku();
            $ticketData['keyword']     = __('Cancel Request for SKU : %1', $orderItem->getSku());
            $ticketData['image']       = '';
            $ticketData['remarks']     = __('Cancel Request for SKU : %1', $orderItem->getSku()) .
                                         __(" of Order # %1", $order->getIncrementId());
            $ticketData['remarks']     = $ticketData['remarks'] . " ," . __('Reason') . ' :' . $reason;
            $ticketData['ticket_code'] = '';
            $ticketData['lang_code']   = $cancelForm['lang_code'];
            $ticketData['status']      = 0;
            $ticketData['customer_id'] = $order->getCustomerId();
            $ticketData['ticket_type'] = 2;
            $this->myTickets->setData($ticketData);
            $this->myTickets->save();

            $result = [
                'errors'  => false,
                'message' => __(
                    'Order successfully updated and  a Request Ticket has been created, You will get Ticket ID shortly.'
                )
            ];

        } catch (Exception $exception) {
            $result = [
                'errors'  => true,
                'message' => $exception->getMessage()
            ];
        }

        return $result;
    }

    /**
     * Creating the Order History
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
            //Check History for order id already exist or not
            $orderHistoryExist = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderId);
            if ($orderHistoryExist->getSize() <= 0) {
                $order        = $this->order->load($orderId);
                $orderItems   = $order->getAllItems();
                $orderHistory = $this->orderHistoryFactory->create();
                $orderHistory->setData('order_id', $orderId);
                $orderHistory->setData('orderreturn_id', $orderReturnId);
                foreach ($order->getData() as $field => $value) {
                    if ($field == 'entity_id') {
                        continue;
                    }
                    $orderHistory->setData($field, $value);
                }
                $orderHistory->save();
                $historyOrderId = $orderHistory->getEntityId();
                if (count($orderItems)) {
                    foreach ($orderItems as $orderItem) {
                        $orderHistoryItem = $this->historyItemFactory->create();
                        foreach ($orderItem->getData() as $field => $value) {
                            if ($field == 'item_id') {
                                continue;
                            } elseif ($field == 'entity_id' ||
                                      $field == 'extension_attributes' ||
                                      $field == 'product_image') {
                                continue;
                            } elseif ($field == 'order_id') {
                                $field = 'history_order_id';
                                $value = $historyOrderId;
                            } elseif ($field == 'product_options') {
                                $value = $this->serializer->serialize($value);
                            }
                            $orderHistoryItem->setData($field, $value);
                            if ($field == 'base_discount_refunded') {
                                $orderHistoryItem->setData('base_discount_refunded', $value);
                                break;
                            }
                        }
                        $orderHistoryItem->save();
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
