<?php

namespace Dynamic\OrderTracking\Api;

interface GetOrderTracking {

	/**
     * Returns OrderTracking data
     *
     * @param int $orderId
     * @return array
     */
    public function getOrderTrackingList($orderId);
}
