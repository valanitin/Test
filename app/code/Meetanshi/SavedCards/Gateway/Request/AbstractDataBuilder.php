<?php

namespace Meetanshi\SavedCards\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Class AbstractDataBuilder
 * @package Meetanshi\SavedCards\Gateway\Request
 */
abstract class AbstractDataBuilder implements BuilderInterface
{
    const PAYMENT = 'Payment';

    const REFUND = 'Refund';
}
