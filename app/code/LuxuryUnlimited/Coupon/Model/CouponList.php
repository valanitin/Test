<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\Coupon\Model;

use LuxuryUnlimited\Coupon\Api\CouponListInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use LuxuryUnlimited\Coupon\Model\Rule\Collection;

/**
 * Coupon list object.
 */
class CouponList implements CouponListInterface
{
    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Sales Rules collection.
     *
     * @var \LuxuryUnlimited\Coupon\Model\Rule\Collection
     */
    protected $ruleCollection;

    /**
     * Constructs a coupon read service object.
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        Collection $ruleCollection
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->ruleCollection = $ruleCollection;
    }

    /**
     * {@inheritdoc}
     */
    public function get($cartId)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        return $this->ruleCollection->getValidCouponList($quote);
    }
}
