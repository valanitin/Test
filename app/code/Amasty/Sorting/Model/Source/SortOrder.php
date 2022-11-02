<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class SortOrder implements ArrayInterface
{
    const SORT_ASC = 'asc';

    const SORT_DESC = 'desc';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            [
                'value' => self::SORT_DESC,
                'label' => __('DESC')
            ],
            [
                'value' => self::SORT_ASC,
                'label' => __('ASC')
            ]
        ];

        return $options;
    }
}
