<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\Coupon\Model\Rule;

use LuxuryUnlimited\Coupon\Helper\Data;
use Magento\SalesRule\Model\Utility;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class Collection
{
    /**
     * @var \LuxuryUnlimited\Coupon\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $_utility;

    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        Data $helperData,
        Utility $utility,
        CollectionFactory $collectionFactory
    ) {
        $this->_helperData = $helperData;
        $this->_utility = $utility;
        $this->_collectionFactory = $collectionFactory;
    }

    /**
     * Get rules collection for current object state.
     *
     * @return \Magento\SalesRule\Model\ResourceModel\Rule\Collection
     */
    public function getRulesCollection()
    {
        $websiteId = $this->_helperData->getWebsiteId();
        $customerGroupId = $this->_helperData->getCustomerGroupId();

        return $this->_collectionFactory->create()
                ->addWebsiteGroupDateFilter($websiteId, $customerGroupId)
                ->addAllowedSalesRulesFilter()
                ->addFieldToFilter('coupon_type', ['neq' => '1'])
                ->addFieldToFilter('is_visible_in_list', ['eq' => '1']);
    }

    /**
     * Filter Sales rules with condition and return valid coupons only.
     *
     * @return string[] couponCodeArray
     */
    public function getValidCouponList($quote)
    {
        $address = $quote->getShippingAddress();
        $rules = $this->getRulesCollection();
        $ruleArray = [];

        $items = $quote->getAllVisibleItems();

        foreach ($rules as $rule) {
            $validate = $this->_utility->canProcessRule($rule, $address);

            $validAction = false;

            foreach ($items as $item) {
                if ($validAction = $rule->getActions()->validate($item)) {
                    break;
                }
            }

            if ($validate && $validAction) {
                $ruleArray[] = [
                    'name' => $rule->getName(),
                    'description' => $rule->getDescription(),
                    'coupon' => $rule->getCode(),
                ];
            }
        }

        return $ruleArray;
    }
}
