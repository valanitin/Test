<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


declare(strict_types=1);

namespace Amasty\Sorting\Model\Source;

use Magento\Catalog\Model\Config\Source\ListSort as NativeListSort;

class ListSort extends NativeListSort
{
    const IGNORE_ATTRIBUTES = [
        'price_asc',
        'price_desc'
    ];

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        foreach ($options as $key => $option) {
            if (isset($option['value']) && in_array($option['value'], self::IGNORE_ATTRIBUTES)) {
                unset($options[$key]);
            }
        }

        return $options;
    }
}
