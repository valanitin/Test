<?php

namespace Dynamic\Abandonedcartapi\Model\ResourceModel;

/**
 * Abandonedcartapi Resource Model
 */
class Abandonedcartapi extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('abandonedcartapi', 'abandonedcartapi_id');
    }
}
