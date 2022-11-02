<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Coupon\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Exception\LocalizedException;

class SalesRuleLogging extends AbstractDb
{
    /**
     * Define the main table
     */
    protected function _construct()
    {
        $this->_init('salesrule_coupon_actions_log','id');
    }
}
