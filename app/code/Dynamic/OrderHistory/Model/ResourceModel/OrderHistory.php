<?php
namespace Dynamic\OrderHistory\Model\ResourceModel;

/**
 * Orderreturn Resource Model
 */
class OrderHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('sales_order_history', 'entity_id');
    }
}
