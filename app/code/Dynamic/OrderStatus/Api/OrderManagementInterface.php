<?php


namespace Dynamic\OrderStatus\Api;


interface OrderManagementInterface
{

    /**
     * Adds a comment to a specified order.
     *
     * @param int $id The order ID.
     * @param string[] $value
     * @return string 
     */
    public function OrderComment($id, $value);
}
