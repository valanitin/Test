<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\Coupon\Model\Checkout;

use Magento\Checkout\Model\ConfigProviderInterface;
use LuxuryUnlimited\Coupon\Model\Rule\Collection;
use LuxuryUnlimited\Coupon\Helper\Data;
use Magento\Checkout\Model\Session;

class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \LuxuryUnlimited\Coupon\Model\Rule\Collection
     */
    protected $_ruleCollection;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \LuxuryUnlimited\Coupon\Helper\Data
     */
    protected $_helperData;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        Collection $ruleCollection,
        Data $helperData,
        Session $checkoutSession
    ) {
        $this->_ruleCollection = $ruleCollection;
        $this->_helperData = $helperData;
        $this->_checkoutSession = $checkoutSession;
    }

    /**
     * Provides checkout configurations for coupon code list.
     */
    public function getConfig()
    {
        if (!$this->_helperData->isEnabled()) {
            return [];
        }

        $couponList['couponList'] = $this->getListArray();

        return $couponList;
    }

    /**
     * get List of valid coupon code for active cart.
     */
    public function getListArray()
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->_checkoutSession->getQuote();

        return $this->_ruleCollection->getValidCouponList($quote);
    }
}
