<?php

/**
 * Cmspagemanager Resource Collection
 */
namespace Dynamic\Cmspagemanager\Model\ResourceModel\Cmspagemanager;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Cmspagemanager\Model\Cmspagemanager', 'Dynamic\Cmspagemanager\Model\ResourceModel\Cmspagemanager');
    }
}
