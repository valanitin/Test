<?php

namespace LuxuryUnlimited\MyReturnsApi\Api;

use LuxuryUnlimited\MyReturnsApi\Api\Data\OrderReturnDataInterface;

interface OrderReturnInterface
{
    /**
     * Order-return form.
     *
     * @param mixed $returnForm
     *
     * @return OrderReturnDataInterface
     */
    public function returnOrder($returnForm);

    /**
     * Order-return form.
     *
     * @param mixed $returnForm
     *
     * @return OrderReturnDataInterface
     */
    public function returnOrderItem($returnForm);
}
