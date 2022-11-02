<?php

namespace Dynamic\Referfriend\Model\ResourceModel;

/**
 * Referfriend Resource Model
 */
class Referfriend extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('referfriend', 'referfriend_id');
    }
}
