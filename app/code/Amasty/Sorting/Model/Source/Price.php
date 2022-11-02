<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Source;

/**
 * Class Price
 */
class Price implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'min_price',
                'label' => __('Minimal Price'),
            ],
            [
                'value' => 'price',
                'label' => __('Price'),
            ],
            [
                'value' => 'final_price',
                'label' => __('Final Price'),
            ],
        ];
    }
}
