<?php

namespace Dynamic\Orderreturn\Model\ResourceModel;

/**
 * Orderreturn Resource Model
 */
class Orderreturn extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('orderreturn', 'orderreturn_id');
    }
}
