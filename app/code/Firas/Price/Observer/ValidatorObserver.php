<?php
/**
 * Copyright © Firas Mohammed(firasath90@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Firas\Price\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Firas\Price\Helper\Data as PriceHelper;
use Firas\Price\Model\CalculatorInterface;

/**
 * SalesRule validator observer
 */
class ValidatorObserver implements ObserverInterface
{
    /**
     * Round price helper
     *
     * @var PriceHelper
     */
    protected $helper;

    /**
     * Price calculator
     *
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * Initialize observer
     *
     * @param CalculatorInterface $calculator
     * @param PriceHelper $helper
     */
    public function __construct(
        CalculatorInterface $calculator,
        PriceHelper $helper
    ) {
        $this->calculator = $calculator;
        $this->helper = $helper;
    }

    /**
     * Rounding calculated discount
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->isRoundEnabled()) {
            /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
            $discountData = $observer->getEvent()->getResult();
            $discountData->setAmount(
                $this->calculator->calculate($discountData->getAmount())
            );
        }
    }

    /**
     * Check round price convert functionality should be enabled
     *
     * @return bool
     */
    private function isRoundEnabled()
    {
        return $this->helper->isEnabled() && $this->helper->isRoundingDiscount();
    }
}
