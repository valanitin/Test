<?php

/**
 * Abandonedcartapi Resource Collection
 */
namespace Dynamic\Abandonedcartapi\Model\ResourceModel\Abandonedcartapi;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Abandonedcartapi\Model\Abandonedcartapi', 'Dynamic\Abandonedcartapi\Model\ResourceModel\Abandonedcartapi');
    }
}
