<?php

namespace Dynamic\Returnstatus\Model\ResourceModel;

/**
 * Orderreturn Resource Model
 */
class Returnstatus extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('returnstatus', 'returnstatus_id');
    }
}
