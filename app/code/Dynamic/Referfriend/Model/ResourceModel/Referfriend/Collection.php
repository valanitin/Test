<?php

/**
 * Referfriend Resource Collection
 */
namespace Dynamic\Referfriend\Model\ResourceModel\Referfriend;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Referfriend\Model\Referfriend', 'Dynamic\Referfriend\Model\ResourceModel\Referfriend');
    }
}
