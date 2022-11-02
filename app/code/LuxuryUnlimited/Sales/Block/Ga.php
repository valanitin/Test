<?php

namespace LuxuryUnlimited\Sales\Block;

use Magento\Cookie\Helper\Cookie;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Template\Context;
use Magento\GoogleAnalytics\Helper\Data;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;

class Ga extends \Magento\GoogleAnalytics\Block\Ga
{
    /**
     * @var ResourceConnection|ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param Context $context
     * @param CollectionFactory $salesOrderCollection
     * @param Data $googleAnalyticsData
     * @param ResourceConnection $resourceConnection
     * @param array $data
     * @param Cookie|null $cookieHelper
     */

    public function __construct(
        Context            $context,
        CollectionFactory  $salesOrderCollection,
        Data               $googleAnalyticsData,
        ResourceConnection $resourceConnection,
        array              $data = [],
        Cookie             $cookieHelper = null
    )
    {
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context, $salesOrderCollection, $googleAnalyticsData, $data, $cookieHelper);
    }

    public function getOrdersTrackingData()
    {
        $result = [];
        $orderIds = $this->getOrderIds();
        if (empty($orderIds) || !is_array($orderIds)) {
            return $result;
        }
        $conn = $this->resourceConnection->getConnection();
        $selectOrder = $conn->select()
            ->from(
                ['main_table' => 'sales_order']
            )->where('entity_id IN (' . implode(",", $orderIds) . ')');
        $collection = $conn->fetchAll($selectOrder);

        foreach ($collection as $order) {
            $select = $conn->select()
                ->from(
                    ['sales_item' => 'sales_order_item']
                )
                ->join(
                    ['main_table' => 'sales_order'],
                    'sales_item.order_id = main_table.entity_id'
                )->where('order_id = ' . $order['entity_id']);

            $items = $conn->fetchAll($select);
            $price = [];
            foreach ($items as $item) {
                if ($item['product_type'] == 'configurable') {
                    $price[$item['item_id']] = $item['price'];
                    continue;
                }

                $result['products'][] = [
                    'id' => $this->escapeJsQuote($item['sku']),
                    'name' => $this->escapeJsQuote($item['name']),
                    'price' => $price[$item['parent_item_id']] ?? $item['price'],
                    'quantity' => $item['qty_ordered'],
                ];
            }
            $result['orders'][] = [
                'id' => $order['increment_id'],
                'affiliation' => $this->escapeJsQuote($this->_storeManager->getStore()->getName()),
                'revenue' => $order['grand_total'],
                'tax' => $order['tax_amount'],
                'shipping' => $order['shipping_amount'],
            ];
            $result['currency'] = $order['order_currency_code'];
        }
        return $result;
    }
}