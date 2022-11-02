<?php

namespace Dynamic\OrderHistory\Model;

/**
 * OrderHistory Model
 *
 * @method \Dynamic\OrderHistory\Model\Resource\Page _getResource()
 * @method \Dynamic\OrderHistory\Model\Resource\Page getResource()
 */
class OrderhistoryItem extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\OrderHistory\Model\ResourceModel\OrderhistoryItem');
    }

}
