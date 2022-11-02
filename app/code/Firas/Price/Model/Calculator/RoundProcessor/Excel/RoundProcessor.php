<?php
/**
 * Copyright © Firas Mohammed(firasath90@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Firas\Price\Model\Calculator\RoundProcessor\Excel;

use Firas\Price\Model\Calculator\RoundProcessorInterface;

/**
 * Excel round processor
 */
class RoundProcessor extends AbstractProcessor implements RoundProcessorInterface
{
    /**
     * Retrieve the rounded price
     *
     * @param float $price
     * @return float
     */
    public function round($price)
    {
        return $this->getPrecision() < 0
            ? round($price/$this->getMultiplier()) * $this->getMultiplier()
            : round($price * $this->getMultiplier())/$this->getMultiplier();
    }
}
