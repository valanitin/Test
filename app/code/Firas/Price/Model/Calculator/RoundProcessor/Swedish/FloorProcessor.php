<?php
/**
 * Copyright © Firas Mohammed(firasath90@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Firas\Price\Model\Calculator\RoundProcessor\Swedish;

use Firas\Price\Model\Calculator\RoundProcessorInterface;

/**
 * Swedish floor round processor
 */
class FloorProcessor extends AbstractProcessor implements RoundProcessorInterface
{
    /**
     * Retrieve the rounded price
     *
     * @param float $price
     * @return float
     */
    public function round($price)
    {
        return floor($price/$this->getFraction()) * $this->getFraction();
    }
}
