<?php

namespace LuxuryUnlimited\AmastySorting\Model;

use Magento\Framework\Model\AbstractModel;
use LuxuryUnlimited\AmastySorting\Model\ResourceModel\Reservations as Model;

class Reservations extends AbstractModel
{
    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Model::class);
    }
}
