<?php

namespace LuxuryUnlimited\MyReturnsApi\Api;

use LuxuryUnlimited\MyReturnsApi\Api\Data\OrderCancelDataInterface;

interface OrderCancelInterface
{
    /**
     * Order-return form.
     *
     * @param mixed $cancelForm
     *
     * @return OrderCancelDataInterface
     */
    public function cancelOrder($cancelForm);

    /**
     * Order-return form.
     *
     * @param mixed $cancelForm
     *
     * @return OrderCancelDataInterface
     */
    public function cancelOrderItem($cancelForm);
}
