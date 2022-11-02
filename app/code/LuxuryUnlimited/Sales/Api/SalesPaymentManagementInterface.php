<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\Sales\Api;

interface SalesPaymentManagementInterface
{
    /**
     * Get Payment Data
     *
     * @api
     * @param string $date
     * @return string
     */
    public function getSalesData($date);
}
