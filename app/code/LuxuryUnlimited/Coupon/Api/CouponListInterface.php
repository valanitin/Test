<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\Coupon\Api;

/**
 * Coupon list service interface.
 *
 * @api
 *
 * @since 1.0.0
 */
interface CouponListInterface
{
    /**
     * Returns list of valid coupon in a specified cart.
     *
     * @param int $cartId the cart ID
     *
     * @return string[] the coupon list data
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException the specified cart does not exist
     */
    public function get($cartId);
}
