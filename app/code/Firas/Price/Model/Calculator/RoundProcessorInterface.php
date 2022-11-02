<?php
/**
 * Copyright © Firas Mohammed(firasath90@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Firas\Price\Model\Calculator;

/**
 * Price round processor interface
 */
interface RoundProcessorInterface
{
    /**
     * Retrieve the rounded price
     *
     * @param float $price
     * @return float
     */
    public function round($price);
}
