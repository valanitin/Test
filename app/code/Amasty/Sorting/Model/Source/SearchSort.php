<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Model\Source;

use Magento\Catalog\Model\Config\Source\ListSort;

class SearchSort extends ListSort
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_shift($options);
        array_unshift($options, [
            'value' => 'relevance',
            'label' => __('Relevance')
        ]);

        return $options;
    }
}
