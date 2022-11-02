<?php

namespace Dynamic\OrderErpSync\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;

class Collection extends OriginalCollection
{
    /**
     * @return $this|Collection|OriginalCollection|void
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap('order_erp_sync_flag', 'sales_order.' . 'order_erp_sync_flag');
        return $this;
    }

    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('sales_order');
        $this->getSelect()->joinLeft(
            $joinTable,
            'main_table.entity_id = sales_order.entity_id',
            ['order_erp_sync_flag']
        );
        parent::_renderFiltersBefore();
    }
}