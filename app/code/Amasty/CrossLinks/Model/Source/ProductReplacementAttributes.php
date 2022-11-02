<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_CrossLinks
 */


namespace Amasty\CrossLinks\Model\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Catalog\Api\Data\EavAttributeInterface;
use Magento\Framework\Api\SortOrder;

class ProductReplacementAttributes implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var AttributeCollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var array
     */
    protected $allowedAttributeCodes = [];

    /**
     * ProductReplacementAttributes constructor.
     *
     * @param AttributeCollectionFactory $collectionFactory
     * @param array $allowedAttributeCodes
     */
    public function __construct(
        AttributeCollectionFactory $collectionFactory,
        array $allowedAttributeCodes = []
    ) {
        $this->attributeCollectionFactory = $collectionFactory;
        $this->allowedAttributeCodes = $allowedAttributeCodes;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $grouped = [];
        foreach ($this->_getOptions() as $optionValue => $optionLabel) {
            if (in_array($optionValue, $this->allowedAttributeCodes)) {
                $options[] = ['value' => $optionValue, 'label' => $optionLabel];
            } else {
                $grouped[] = ['value' => $optionValue, 'label' => $optionLabel];
            }
        }

        if ($grouped) {
            $options[] = ['value' => $grouped, 'label' => __('Attributes Block')];
        }

        return $options;
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $collection = $this->attributeCollectionFactory->create();
        $collection->addVisibleFilter()
            ->removePriceFilter()
            ->addFieldToFilter(
                [EavAttributeInterface::ATTRIBUTE_CODE, EavAttributeInterface::IS_VISIBLE_ON_FRONT],
                [['in' => $this->allowedAttributeCodes], 1]
            );

        $collection->addOrder(EavAttributeInterface::ATTRIBUTE_CODE, SortOrder::SORT_ASC);
        $options = [];
        foreach ($collection->getItems() as $attribute) {
            /** @var Attribute $attribute */
            $options[$attribute->getAttributeCode()] = $attribute->getAttributeCode();
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_getOptions();
    }
}
