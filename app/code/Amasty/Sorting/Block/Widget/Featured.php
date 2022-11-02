<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Block\Widget;

use Amasty\Sorting\Model\Source\SortOrder;
use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\DB\Select;

class Featured extends ProductsList
{
    const DEFAULT_COLLECTION_SORT_BY = 'name';
    const DEFAULT_COLLECTION_ORDER = SortOrder::SORT_ASC;

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function createCollection()
    {
        $collection = parent::createCollection();
        $collection->getSelect()->reset(Select::ORDER);
        $collection->setOrder($this->getSortBy(), $this->getSortOrder());
        $this->_eventManager->dispatch(
            'catalog_block_product_list_collection',
            ['collection' => $collection]
        );

        return $collection;
    }

    /**
     * @return string
     */
    public function getSortBy()
    {
        if (!$this->hasData('sort_by')) {
            $this->setData('sort_by', self::DEFAULT_COLLECTION_SORT_BY);
        }
        return $this->getData('sort_by');
    }

    /**
     * @return string
     */
    public function getSortOrder()
    {
        if (!$this->hasData('amsorting_sort_order')) {
            $this->setData('amsorting_sort_order', self::DEFAULT_COLLECTION_ORDER);
        }
        return $this->getData('amsorting_sort_order');
    }
}
