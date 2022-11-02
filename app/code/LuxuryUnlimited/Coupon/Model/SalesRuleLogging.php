<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2021. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Coupon\Model;

use Magento\Framework\Model\AbstractExtensibleModel;

class SalesRuleLogging extends AbstractExtensibleModel
{
    protected function _construct()
    {
        $this->_init(\LuxuryUnlimited\Coupon\Model\ResourceModel\SalesRuleLogging::class);
    }
}
