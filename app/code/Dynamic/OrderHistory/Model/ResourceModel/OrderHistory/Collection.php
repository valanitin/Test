<?php

/**
 * OrderHistory Resource Collection
 */
namespace Dynamic\OrderHistory\Model\ResourceModel\OrderHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\OrderHistory\Model\OrderHistory', 'Dynamic\OrderHistory\Model\ResourceModel\OrderHistory');
    }
}
