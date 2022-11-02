<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\Coupon\Model\GuestCart;

use LuxuryUnlimited\Coupon\Api\CouponListInterface;
use LuxuryUnlimited\Coupon\Api\GuestCouponListInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Coupon list class for guest carts.
 */
class GuestCouponList implements GuestCouponListInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CouponListInterface
     */
    private $couponList;

    /**
     * Constructs a coupon read service object.
     */
    public function __construct(
        CouponListInterface $couponList,
        QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->couponList = $couponList;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->couponList->get($quoteIdMask->getQuoteId());
    }
}
