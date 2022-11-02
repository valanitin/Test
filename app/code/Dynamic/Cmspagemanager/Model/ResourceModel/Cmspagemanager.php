<?php

namespace Dynamic\Cmspagemanager\Model\ResourceModel;

/**
 * Cmspagemanager Resource Model
 */
class Cmspagemanager extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cmspagemanager', 'cmspagemanager_id');
    }
}
