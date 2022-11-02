<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Plugin\Catalog\Model\ResourceModel\Product\Attribute;

use Amasty\Sorting\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection as AttributeCollection;
use Magento\Framework\DB\Select;

class Collection
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param AttributeCollection $subject
     * @param AttributeCollection $result
     * @return AttributeCollection
     */
    public function afterAddToIndexFilter($subject, $result)
    {
        if ($this->helper->isElasticSort(true)) {
            $parts = $result->getSelect()->getPart(Select::WHERE);
            $conditions = array_pop($parts);
            $newCondition = $result->getConnection()->quoteInto(
                'main_table.attribute_code IN (?)',
                $this->helper->getAmastyAttributesCodes()
            );
            $conditions = str_replace(
                'additional_table.is_searchable',
                $newCondition . ' OR additional_table.is_searchable',
                $conditions
            );
            $parts[] = $conditions;
            $result->getSelect()->setPart(Select::WHERE, $parts);
        }

        return $result;
    }
}
