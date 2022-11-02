<?php

namespace LuxuryUnlimited\Coupon\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use LuxuryUnlimited\Coupon\Model\Rule\Collection;

class CouponList extends Template
{
    /**
     * @codeCoverageIgnore
     */
    public function __construct(
        Context $context,
        Collection $collection,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->collection = $collection;
    }
    
    public function getCoupons()
    {
        $rules = $this->collection->getRulesCollection();
        foreach ($rules as $rule) {
           if ($rule->getIsActive()) {
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