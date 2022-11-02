<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Coupon\Api;

interface CouponLogsInterface
{
    /**
     * Get Config Data
     *
     * @api
     * @return string[]
     */
    public function getCouponLogs();
}
