<?php
namespace Dynamic\OrderHistory\Model\ResourceModel;

/**
 * OrderhistoryItem Resource Model
 */
class OrderhistoryItem extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_order_history_item', 'item_id');
    }
}
