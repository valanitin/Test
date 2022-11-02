<?php
/**
 * Copyright © Firas Mohammed(firasath90@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Firas\Price\Model;

use Magento\Framework\Exception\LocalizedException;

/**
 * Price calculator interface
 */
interface CalculatorInterface
{
    /**
     * Retrieve the calculated price
     *
     * @param float $price
     * @return float
     */
    public function calculate($price);
}
