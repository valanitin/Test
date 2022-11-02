<?php
/**
 * Copyright © Firas Mohammed(firasath90@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Firas\Price\Model\Calculator\RoundProcessor\Base;

use Firas\Price\Helper\Data as PriceHelper;

/**
 * Base abstract round processor
 */
abstract class AbstractProcessor
{
    /**
     * Round price helper
     *
     * @var PriceHelper
     */
    private $helper;

    /**
     * Precision
     *
     * @var int
     */
    private $precision;

    /**
     * Initialize processor
     *
     * @param PriceHelper $helper
     */
    public function __construct(
        PriceHelper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Retrieve precision
     *
     * @return int
     */
    protected function getPrecision()
    {
        if (null === $this->precision) {
            $this->precision = $this->helper->getPrecision();
        }
        return $this->precision;
    }
}
