<?php

namespace LuxuryUnlimited\ConvertCurrencyCode\Plugin;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Store\Model\StoreManagerInterface;

class ConvertStoreCurrency
{
    /** @var StoreManagerInterface */
    protected $storeManager;

    /**
     * ConvertStoreCurrency constructor.
     *
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Change price param value to base currency
     *
     * @param ProductRepositoryInterface $subject
     * @param SearchCriteriaInterface $searchCriteria
     * @return array
     */
    public function beforeGetList(ProductRepositoryInterface $subject, SearchCriteriaInterface $searchCriteria)
    {
        $rate = $this->storeManager->getStore()->getCurrentCurrencyRate();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            $filters = $filterGroup->getFilters();
            foreach ($filters as $filter) {
                if ($filter->getField() == "price" && $rate) {
                    $amount = $filter->getValue() / $rate;
                    $filter->setValue($amount);
                }
            }
        }
        return [$searchCriteria];
    }
}
