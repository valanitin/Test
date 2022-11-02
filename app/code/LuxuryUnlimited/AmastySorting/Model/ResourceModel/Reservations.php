<?php

namespace LuxuryUnlimited\AmastySorting\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Reservations extends AbstractDb
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_sorting_reservations', 'id');
    }
}
