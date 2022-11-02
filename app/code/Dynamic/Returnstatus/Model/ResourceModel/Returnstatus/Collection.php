<?php

/**
 * Returnstatus Resource Collection
 */
namespace Dynamic\Returnstatus\Model\ResourceModel\Returnstatus;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Returnstatus\Model\Returnstatus', 'Dynamic\Returnstatus\Model\ResourceModel\Returnstatus');
    }
}
