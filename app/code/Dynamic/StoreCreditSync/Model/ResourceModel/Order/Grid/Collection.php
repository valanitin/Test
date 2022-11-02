<?php

namespace Dynamic\StoreCreditSync\Model\ResourceModel\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;

class Collection extends OriginalCollection
{
    /**
     * @return $this|Collection|OriginalCollection|void
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap('store_credit_sync_flag', 'sales_order.' . 'store_credit_sync_flag');
        return $this;
    }

    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('sales_order');
        $this->getSelect()->joinLeft(
            $joinTable,
            'main_table.entity_id = sales_order.entity_id',
            ['store_credit_sync_flag']
        );
        parent::_renderFiltersBefore();
    }
}