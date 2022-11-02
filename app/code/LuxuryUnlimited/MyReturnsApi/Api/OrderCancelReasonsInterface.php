<?php

namespace LuxuryUnlimited\MyReturnsApi\Api;

interface OrderCancelReasonsInterface
{
    /**
     * Returns Order cancellation reasons
     *
     * @return array
     */
    
    public function getReasons();
}
