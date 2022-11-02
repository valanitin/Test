<?php

/**
 * Mytickets Resource Collection
 */
namespace Dynamic\Mytickets\Model\ResourceModel\Mytickets;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Mytickets\Model\Mytickets', 'Dynamic\Mytickets\Model\ResourceModel\Mytickets');
    }
}
