<?php
namespace LuxuryUnlimited\AmastySorting\Model\ResourceModel\Reservations;

use LuxuryUnlimited\AmastySorting\Model\Reservations;
use LuxuryUnlimited\AmastySorting\Model\ResourceModel\Reservations as ResourceModel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Reservations::class, ResourceModel::class);
    }
}
