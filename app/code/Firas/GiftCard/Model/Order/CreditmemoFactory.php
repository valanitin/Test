<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Model\Order;

class CreditmemoFactory extends \Magento\Sales\Model\Order\CreditmemoFactory
{
    /**
     * Prepare order creditmemo based on order items and requested params
     *
     * @param \Magento\Sales\Model\Order $order
     * @param array $data
     * @return Creditmemo
     */
    public function createByOrder(\Magento\Sales\Model\Order $order, array $data = [])
    {
        $totalQty = 0;
        $creditmemo = $this->convertor->toCreditmemo($order);
        $qtys = isset($data['qtys']) ? $data['qtys'] : [];
        $i = 0;
        foreach ($order->getAllItems() as $orderItem) {
            if (!$this->canRefundItem($orderItem, $qtys)) {
                continue;
            }
            if ($orderItem->getData()['product_type'] == 'giftcard') {
                $i++;
                if ($i == 1) {
                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $messageManger = $objectManager->create('Magento\Framework\Message\ManagerInterface');
                    $messageManger->addWarning(__('We can\'t save the credit memo for giftcard type product.'));
                }
                continue;
            }

            $item = $this->convertor->itemToCreditmemoItem($orderItem);
            if ($orderItem->isDummy()) {
                $qty = 1;
                $orderItem->setLockedDoShip(true);
            } else {
                if (isset($qtys[$orderItem->getId()])) {
                    $qty = (double)$qtys[$orderItem->getId()];
                } elseif (!count($qtys)) {
                    $qty = $orderItem->getQtyToRefund();
                } else {
                    continue;
                }
            }
            $totalQty += $qty;
            $item->setQty($qty);
            $creditmemo->addItem($item);
        }
        $creditmemo->setTotalQty($totalQty);

        $this->initData($creditmemo, $data);

        $creditmemo->collectTotals();
        return $creditmemo;
    }
}
