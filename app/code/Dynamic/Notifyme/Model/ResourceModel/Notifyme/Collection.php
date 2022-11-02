<?php

/**
 * Notifyme Resource Collection
 */
namespace Dynamic\Notifyme\Model\ResourceModel\Notifyme;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dynamic\Notifyme\Model\Notifyme', 'Dynamic\Notifyme\Model\ResourceModel\Notifyme');
    }
}
