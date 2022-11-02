<?php

namespace Meetanshi\SavedCards\Model\Source;

use Magento\Payment\Model\Source\Cctype as PaymentCctype;

/**
 * Class Cctype
 * @package Meetanshi\SavedCards\Model\Source
 */
class Cctype extends PaymentCctype
{
    /**
     * @return array
     */
    public function getAllowedTypes()
    {
        return ['VI', 'MC', 'AE', 'DI', 'OT'];
    }
}
