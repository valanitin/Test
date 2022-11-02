<?php

namespace Dynamic\StoreCreditSync\Api;

interface CreditManagerInterface
{
    /**
     * @param int $customerId
     * @return array
     */
    public function getBalance($customerId);
}
