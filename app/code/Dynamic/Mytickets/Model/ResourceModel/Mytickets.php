<?php

namespace Dynamic\Mytickets\Model\ResourceModel;

/**
 * Mytickets Resource Model
 */
class Mytickets extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mytickets', 'mytickets_id');
    }
}
