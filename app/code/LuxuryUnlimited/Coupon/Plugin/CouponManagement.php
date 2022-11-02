<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\Coupon\Plugin;

use LuxuryUnlimited\Coupon\Helper\Data as ConfigData;
use LuxuryUnlimited\Coupon\Helper\Validator as CouponValidator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use LuxuryUnlimited\Coupon\Model\SalesRuleLoggingFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\SalesRule\Model\CouponFactory;

class CouponManagement
{
    /**
     * Module Helper
     *
     * @var Validator
     */
    protected $_couponValidator;

    /**
     * Module helper
     *
     * @var Data
     */
    protected $_configData;

    /**
     * Sales Rule Logger
     *
     * @var SalesRuleLoggingFactory
     */
    protected $salesRuleLoggerFactory;

    /**
     * Timezone
     *
     * @var TimezoneInterface
     */
    protected $date;

    /**
     * Cart Repository
     *
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @param \LuxuryUnlimited\Coupon\Helper\Validator $couponValidator
     * @param SalesRuleLoggingFactory $salesRuleLoggerFactory
     * @param TimezoneInterface $date
     * @param CartRepositoryInterface $quoteRepository
     * @param CouponFactory $couponFactory
     * @param \LuxuryUnlimited\Coupon\Helper\Data $configData
     */
    public function __construct(
        CouponValidator $couponValidator,
        SalesRuleLoggingFactory $salesRuleLoggerFactory,
        TimezoneInterface $date,
        CartRepositoryInterface $quoteRepository,
        CouponFactory $couponFactory,
        ConfigData $configData
    ) {
        $this->_couponValidator = $couponValidator;
        $this->salesRuleLoggerFactory = $salesRuleLoggerFactory;
        $this->date = $date;
        $this->quoteRepository = $quoteRepository;
        $this->couponFactory = $couponFactory;
        $this->_configData = $configData;
    }

    /**
     * This function runs around the set function for coupon api, once the core function throws
     * exception this function will catch and update the error message.
     *
     * @param \Magento\Quote\Model\CouponManagement $subject
     * @param callable $proceed
     * @param int $cartId
     * @param string $couponCode
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundSet(\Magento\Quote\Model\CouponManagement $subject, callable $proceed, $cartId, $couponCode)
    {
        $result = false;
        try {
            
            $salesRuleLog = $this->salesRuleLoggerFactory->create();
            $cartQuote = $this->quoteRepository->getActive($cartId);
            $subtotal = $cartQuote->getBaseSubtotal();
            $subtotalWithDiscount = $cartQuote->getBaseSubtotalWithDiscount();
            $discount = $subtotal - $subtotalWithDiscount;
            $coupon = $this->couponFactory->create();

            $data = [
                'status' => 'success',
                "date" => $this->date->date()->format('Y-m-d H:i:s'),
                "coupon_code" => $couponCode,
                "store_id" => $cartQuote->getStoreId(),
                "customer_id" => $cartQuote->getCustomerId(),
                "subtotal_amount" => $subtotal,
                "discount_amount" => $discount,
                "total_amount" => $cartQuote->getBaseGrandTotal(),
                "quote_id" => $cartQuote->getId(),
                "coupon_id" => $coupon->loadByCode($couponCode)->getCouponId(),
                "rule_id" => $coupon->loadByCode($couponCode)->getRuleId()
            ];

            $result = $proceed($cartId, $couponCode);
            if ($result) {
                $data['validation'] = 'Coupon Applied Successfully';

            }
        } catch (\Exception $err) {            
            $data['status'] = 'error';
            $data['validation'] = $err;

            if($this->_configData->isEnabled()) {
                $err = $this->_couponValidator->validate($couponCode);
                if(!empty($err)) {
                    $data["validation"] = $err;
                }
            }
            $salesRuleLog->setData($data)->save();
            throw new LocalizedException(__($err));
        }
        $salesRuleLog->setData($data)->save();
        return $result;
    }
}