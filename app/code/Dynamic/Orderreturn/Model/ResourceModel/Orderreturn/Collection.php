<?php

/**
 * Orderreturn Resource Collection
 */
namespace Dynamic\Orderreturn\Model\ResourceModel\Orderreturn;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Orderreturn\Model\Orderreturn', 'Dynamic\Orderreturn\Model\ResourceModel\Orderreturn');
    }
}
