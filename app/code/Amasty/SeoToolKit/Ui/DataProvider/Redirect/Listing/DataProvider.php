<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Ui\DataProvider\Redirect\Listing;

use Magento\Framework\Api\Search\SearchResultInterface;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var string[]
     */
    private $mappedValues = [
        'redirect_id' => 'main_table.redirect_id'
    ];

    /**
     * @param SearchResultInterface $searchResult
     * @return array
     */
    protected function searchResultToOutput(SearchResultInterface $searchResult) : array
    {
        $result = [
            'items' => [],
            'totalRecords' => $searchResult->getTotalCount(),
        ];

        foreach ($searchResult->getItems() as $item) {
            $storeIds = $item->getStoreIds();
            if ($storeIds !== null) {
                $item->setStoreIds(explode(',', $item->getStoreIds()));
            }
            $result['items'][] = $item->getData();
        }

        return $result;
    }

    /**
     * @param \Magento\Framework\Api\Filter $filter
     * @return mixed|void
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        if (array_key_exists($filter->getField(), $this->mappedValues)) {
            $filter->setField($this->mappedValues[$filter->getField()]);
        }

        parent::addFilter($filter);
    }
}
