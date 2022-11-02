<?php

namespace Meetanshi\SavedCards\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PaymentAction
 * @package Meetanshi\SavedCards\Model\Source
 */
class PaymentAction implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'authorize_capture',
                'label' => __('Authorize and Capture (Payment)')
            ],
            [
                'value' => 'authorize',
                'label' => __('Authorize Only (Deferred)'),
            ],
        ];
    }
}
