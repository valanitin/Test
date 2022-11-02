<?php
/**
 * Copyright © Firas Mohammed(firasath90@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Firas\Price\Plugin\Catalog\Model\Product\Type;

use Magento\Catalog\Model\Product\Type\Price;
use Firas\Price\Helper\Data as PriceHelper;
use Firas\Price\Model\CalculatorInterface;

/**
 * Currency Price Plugin
 */
class PricePlugin
{
    /**
     * Round Price Helper
     *
     * @var PriceHelper
     */
    private $helper;

    /**
     * Price Calculator
     *
     * @var CalculatorInterface
     */
    private $calculator;

    /**
     * Initialize Plugin
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
     * Get base price with apply Group, Tier, Special prises
     *
     * @param Price $subject
     * @param float $price
     * @return float
     */
    public function afterGetBasePrice(
        Price $subject,
        $price
    ) {
        if ($this->isRoundEnabled()) {
            $price = $this->calculator->calculate($price);
        }
        return $price;
    }

    /**
     * Check Round Price Convert Functionality Should be Enabled
     *
     * @return bool
     */
    private function isRoundEnabled()
    {
        return $this->helper->isEnabled() && $this->helper->isRoundingBasePrice();
    }
}
