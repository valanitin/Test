<?php

namespace LuxuryUnlimited\MyReturnsApi\Helper;

use Dynamic\Mytickets\Model\Mytickets;
use Dynamic\OrderHistory\Model\OrderHistory;
use Dynamic\OrderHistory\Model\OrderHistoryFactory;
use Dynamic\OrderHistory\Model\OrderhistoryItemFactory;
use Dynamic\Orderreturn\Model\DeleteOrderItem;
use Dynamic\Orderreturn\Model\Orderreturn;
use Exception;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;

class PostReturn
{
    /**
     * @var OrderHistoryFactory
     */
    public $orderHistoryFactory;
    /**
     * @var OrderHistory
     */
    public $orderHistory;
    /**
     * @var OrderhistoryItemFactory
     */
    protected $historyItemFactory;
    /**
     * @var Orderreturn
     */
    protected $orderReturn;
    /**
     * @var DeleteOrderItem
     */
    protected $deleteOrderItem;
    /**
     * @var SerializerInterface
     */
    protected $serializer;
    /**
     * @var Mytickets
     */
    protected $myTickets;

    /**
     * @param Orderreturn             $orderReturn
     * @param DeleteOrderItem         $deleteOrderItem
     * @param SerializerInterface     $serializer
     * @param OrderHistoryFactory     $orderHistoryFactory
     * @param OrderHistory            $orderHistory
     * @param OrderhistoryItemFactory $historyItemFactory
     * @param Mytickets               $myTickets
     */
    public function __construct(
        Orderreturn $orderReturn,
        DeleteOrderItem $deleteOrderItem,
        SerializerInterface $serializer,
        OrderHistoryFactory $orderHistoryFactory,
        OrderHistory $orderHistory,
        OrderhistoryItemFactory $historyItemFactory,
        Mytickets $myTickets
    ) {
        $this->orderReturn     = $orderReturn;
        $this->deleteOrderItem = $deleteOrderItem;

        $this->serializer          = $serializer;
        $this->orderHistoryFactory = $orderHistoryFactory;
        $this->orderHistory        = $orderHistory;
        $this->historyItemFactory  = $historyItemFactory;
        $this->myTickets           = $myTickets;
    }

    /**
     *  Return Order Items
     *
     * @param OrderInterface|Order $order
     * @param array                $returnForm
     *
     * @return array
     */
    public function returnOrderItem($order, $returnForm)
    {
        $orderItem  = $order->getItemById($returnForm['item_id']);
        $productSku = $orderItem->getSku();
        $applied    = $this->orderReturn->getCollection()
                                        ->addfieldtofilter('product_sku', $productSku)
                                        ->addfieldtofilter('order_id', $returnForm['order_id']);

        if ($applied->getSize() <= 0) {
            $returnForm['website']        = "www.sololuxury.com";
            $returnForm['product_sku']    = $productSku;
            $returnForm['type']           = 'return';
            $returnForm['customer_email'] = $order->getCustomerEmail();
            $this->orderReturn->setData($returnForm);
            try {
                $itemId          = $returnForm['item_id'];
                $orderOriginalId = $returnForm['order_id'];
                $this->orderReturn->save();
                $orderReturnId = $this->orderReturn->getOrderreturnId();
                /**** save Order original Data as history  -Pritam ****/
                $this->createOrderHistory($orderOriginalId, $orderReturnId, $order);
                $this->deleteOrderItem->deleteOrderItem($orderOriginalId, $itemId);
                $ticketData                   = [];
                $ticketData['name']           = $order->getCustomerFirstname();
                $ticketData['last_name']      = $order->getCustomerLastname();
                $ticketData['email']          = $order->getCustomerEmail();
                $ticketData['phone']          = $order->getCustomerFirstname();
                $ticketData['brand']          = $orderItem->getName();
                $ticketData['style']          = $orderItem->getSku();
                $ticketData['keyword']        = __('Order Return Request for SKU : %1', $orderItem->getSku());
                $ticketData['image']          = '';
                $ticketData['remarks']        = __('Order Item Return Request for SKU : %1', $orderItem->getSku()) .
                                                __(" of Order # %1", $order->getIncrementId());
                $ticketData['remarks']        = $ticketData['remarks'] . " ," . __('Reason') .
                                                ' :' . $returnForm['reason'];
                $ticketData['ticket_code']    = '';
                $ticketData['lang_code']      = $returnForm['lang_code'];
                $ticketData['status']         = 0;
                $ticketData['customer_id']    = $order->getCustomerId();
                $ticketData['ticket_type']    = 1;
                $ticketData['orderreturn_id'] = $orderReturnId;
                $this->myTickets->setData($ticketData);
                $this->myTickets->save();

                $message = 'Order return request successfully sent and a Request Ticket has been created,'
                           . 'you will get updates soon.';

                return ['errors' => false, 'message' => __($message)];
            } catch (Exception $exception) {
                return [
                    'errors'  => true,
                    'message' => $exception->getMessage()
                ];
            }
        }

        return [
            'errors'  => true,
            'message' => __('You have already applied for return.')
        ];
    }

    /**
     *  Create Order History
     *
     * @param int|string           $orderId
     * @param int|string           $orderReturnId
     * @param OrderInterface|Order $order
     * @suppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @return array
     */
    protected function createOrderHistory($orderId, $orderReturnId, $order)
    {
        try {
            $orderHistoryExist = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderId);
            if ($orderHistoryExist->getSize() <= 0) {
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
                'message' => __('Order history created successfully . ')
            ];

        } catch (Exception $e) {
            $result = [
                'errors'  => true,
                'message' => $e->getMessage()
            ];
        }

        return $result;
    }

    /**
     *  Return the Order
     *
     * @param OrderInterface $order
     * @param array          $returnForm
     *
     * @return array
     */
    public function returnOrder($order, $returnForm)
    {
        $orderItems      = $order->getItems();
        $canCreateTicket = $itemNames = $itemSkus = false;
        foreach ($orderItems as $orderItemId => $orderItem) {
            $productSku = $orderItem->getSku();
            $itemNames  = ($itemNames) ? $itemNames .", " . $orderItem->getName() : $orderItem->getName();
            $itemSkus   = ($itemSkus) ? $itemSkus . ", " . $orderItem->getSku() : $orderItem->getSku();
            $applied    = $this->orderReturn->getCollection()
                                            ->addfieldtofilter('product_sku', $productSku)
                                            ->addfieldtofilter('order_id', $returnForm['order_id']);

            if ($applied->getSize() <= 0) {
                $canCreateTicket              = true;
                $returnForm['website']        = "www.sololuxury.com";
                $returnForm['product_sku']    = $productSku;
                $returnForm['type']           = 'return';
                $returnForm['customer_email'] = $order->getCustomerEmail();
                $this->orderReturn->setData($returnForm);
                try {
                    $orderOriginalId = $returnForm['order_id'];
                    $this->orderReturn->save();
                    $orderReturnId = $this->orderReturn->getOrderreturnId();
                    $this->createOrderHistory($orderOriginalId, $orderReturnId, $order);
                    $this->deleteOrderItem->deleteOrderItem($orderOriginalId, $orderItemId);
                } catch (Exception $exception) {
                    return [
                        'errors'  => true,
                        'message' => $exception->getMessage()
                    ];
                }
            }
        }
        if (!$canCreateTicket) {
            return [
                'errors'  => true,
                'message' => __('You have already applied for return')
            ];
        }
        $ticketData                   = [];
        $ticketData['name']           = $order->getCustomerFirstname();
        $ticketData['last_name']      = $order->getCustomerLastname();
        $ticketData['email']          = $order->getCustomerEmail();
        $ticketData['phone']          = $order->getCustomerFirstname();
        $ticketData['brand']          = $itemNames;
        $ticketData['style']          = $itemSkus;
        $ticketData['keyword']        = __('Order return Request for SKU : %1', $itemSkus);
        $ticketData['image']          = '';
        $ticketData['remarks']        = __('Order Item return Request for SKU : %1', $itemSkus) .
                                        __(" of Order # %1", $order->getIncrementId());
        $ticketData['remarks']        = $ticketData['remarks'] . " ," . __('Reason') . ' :' . $returnForm['reason'];
        $ticketData['ticket_code']    = '';
        $ticketData['lang_code']      = $returnForm['lang_code'];
        $ticketData['status']         = 0;
        $ticketData['customer_id']    = $order->getCustomerId();
        $ticketData['ticket_type']    = 1;
        $ticketData['orderreturn_id'] = $orderReturnId;
        $this->myTickets->setData($ticketData);
        $this->myTickets->save();

        $message = 'Order return request successfully sent and a Request Ticket has been created,';
        $message .= ' you will get updates soon . ';

        return ['errors' => false, 'message' => __($message)];
    }
}
