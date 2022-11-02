<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model\Layer;

use Amasty\Shopby\Model\Source\FilterPlacedBlock;
use Magento\Catalog\Model\Layer;
use Magento\Store\Model\ScopeInterface;
use Amasty\Shopby\Model\Source\VisibleInCategory;
use Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionExtendedFactory;

/**
 * Class FilterList
 * @package Amasty\Shopby\Model\Layer
 */
class FilterList extends Layer\FilterList
{
    const PLACE_SIDEBAR = 'sidebar';
    const PLACE_TOP     = 'top';
    const ALL_FILTERS_KEY  = 'amasty_shopby_all_filters';

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting\Proxy
     */
    private $filterSetting;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var string
     */
    private $currentPlace;

    /**
     * @var bool
     */
    private $filtersLoaded  = false;

    /**
     * @var bool
     */
    private $filtersMatched = false;

    /**
     * @var bool
     */
    private $filtersApplied = false;

    /**
     * @var CollectionExtendedFactory
     */
    private $collectionExtendedFactory;

    /**
     * @var  \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $shopbyRequest;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Layer\FilterableAttributeListInterface $filterableAttributes,
        \Amasty\Shopby\Helper\FilterSetting\Proxy $filterSettingHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        CollectionExtendedFactory $collectionExtendedFactory,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        array $filters = [],
        $place = self::PLACE_SIDEBAR
    ) {
        $this->currentPlace = $place;
        $this->filterSetting = $filterSettingHelper;
        $this->scopeConfig = $scopeConfig;
        $this->request = $request;
        $this->registry = $registry;
        $this->collectionExtendedFactory = $collectionExtendedFactory;
        $this->shopbyRequest = $shopbyRequest;
        parent::__construct($objectManager, $filterableAttributes, $filters);
    }

    public function getFilters(Layer $layer)
    {
        if (!$this->filtersLoaded) {
            $filters = $this->getAllFilters($layer);
            $this->filters = $this->filterByPlace($filters, $layer);
            $this->filtersLoaded = true;
        }
        $this->matchFilters($this->filters, $layer);
        return $this->filters;
    }

    /**
     * Get both top and left filters. And keep it in registry.
     *
     * @param Layer $layer
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter[]
     */
    public function getAllFilters(Layer $layer)
    {
        $allFilters = $this->registry->registry(self::ALL_FILTERS_KEY);
        if ($allFilters === null) {
            $filters = parent::getFilters($layer);
            $listAdditionalFilters = $this->getAdditionalFilters($layer);
            $allFilters = $this->insertAdditionalFilters($filters, $listAdditionalFilters);
            usort($allFilters, [$this, 'sortingByPosition']);
            $this->registry->register(self::ALL_FILTERS_KEY, $allFilters);
        }

        if (!$this->scopeConfig->isSetFlag('amshopby/category_filter/enabled', ScopeInterface::SCOPE_STORE)) {
            foreach ($allFilters as $id => $filter) {
                if ($filter instanceof \Amasty\Shopby\Model\Layer\Filter\Category) {
                    unset($allFilters[$id]);
                }
            }
        }

        return $allFilters;
    }

    protected function filterByPlace(array $filters, Layer $layer)
    {
        $isOneColumn = $layer->getCurrentCategory()->getData('page_layout') == '1column';
        $filters = array_filter($filters, function ($filter) use ($isOneColumn) {
            if ($isOneColumn) {
                //Move all filters to open place in one column design
                return $this->currentPlace == self::PLACE_SIDEBAR;
            }

            $position = $this->filterSetting->getSettingByLayerFilter($filter)->getBlockPosition();
            return $position == FilterPlacedBlock::POSITION_BOTH
                || ($position == FilterPlacedBlock::POSITION_SIDEBAR && $this->currentPlace == self::PLACE_SIDEBAR)
                || ($position == FilterPlacedBlock::POSITION_TOP && $this->currentPlace == self::PLACE_TOP);
        });

        return $filters;
    }

    /**
     * @param array $listFilters
     * @param Layer $layer
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function matchFilters(array $listFilters, Layer $layer)
    {
        if ($this->filtersMatched
            || $layer->getProductCollection()->isLoaded()
        ) {
            return false;
        }

        $matchedFilters = [];
        foreach ($listFilters as $idx => $filter) {
            $setting = $this->filterSetting->getSettingByLayerFilter($filter);

            if ($setting->getVisibleInCategories() === VisibleInCategory::ONLY_IN_SELECTED_CATEGORIES &&
                !in_array($layer->getCurrentCategory()->getId(), $setting->getCategoriesFilter())
            ) {
                continue;
            }
            if ($setting->getVisibleInCategories() === VisibleInCategory::HIDE_IN_SELECTED_CATEGORIES &&
                in_array($layer->getCurrentCategory()->getId(), $setting->getCategoriesFilter())
            ) {
                continue;
            }

            $this->applyFilters($layer);

            if ($attributesFilter = $setting->getAttributesFilter()) {
                $stateAttributes = $this->getStateAttributesIds();
                $intersects = array_intersect($attributesFilter, $stateAttributes);
                if (!$intersects) {
                    continue;
                }
            }

            if ($attributesOptionsFilter = $setting->getAttributesOptionsFilter()) {
                $stateAttributesOptions = $this->getActiveOptionIds();
                $intersects = array_intersect($attributesOptionsFilter, $stateAttributesOptions);
                if (!$intersects) {
                    continue;
                }
            }

            $matchedFilters[] = $filter;
        }

        $this->filtersMatched = true;
        $this->filters = $matchedFilters;
        return true;
    }

    /**
     * At this point filters could not be applied (especially at search page).
     * @param Layer $layer
     */
    private function applyFilters(Layer $layer)
    {
        if ($this->filtersApplied) {
            return;
        }
        foreach ($this->getAllFilters($layer) as $filter) {
            $isAppliedCheckTrait = \Amasty\Shopby\Model\Layer\Filter\Traits\FilterTrait::class;
            if (in_array($isAppliedCheckTrait, class_uses($filter))) {
                //filter has multiply applying prevention mechanism
                $filter->apply($this->request);
            }
        }
        $this->filtersApplied = true;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getStateAttributesIds()
    {
        $ids = [];

        foreach ($this->shopbyRequest->getRequestParams() as $key => $param) {
            $filter = $this->collectionExtendedFactory->get()->getItemByCode('attr_' . $key);
            $filterModel = $filter ? $filter->getAttributeModel() : false;
            if ($filterModel) {
                $ids[] = $filterModel->getId();
            }
        }

        return array_unique($ids);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getActiveOptionIds()
    {
        $ids = [];

        foreach ($this->shopbyRequest->getRequestParams() as $param) {
            if (isset($param[0])) {
                $ids[] = explode(',', $param[0]);
            }
        }

        if (count($ids)) {
            $ids = array_unique(array_merge(...$ids));
        }

        return $ids;
    }

    /**
     * @param Layer $layer
     *
     * @return array
     */
    protected function getAdditionalFilters(Layer $layer)
    {
        $additionalFilters = [];
        if ($this->isCustomFilterEnabled('stock') && $this->isEnabledShowOutOfStock()) {
            $additionalFilters[] = $this->objectManager->create(
                \Amasty\Shopby\Model\Layer\Filter\Stock::class,
                ['layer'=>$layer]
            );
        }
        if ($this->isCustomFilterEnabled('rating')) {
            $additionalFilters[] = $this->objectManager->create(
                \Amasty\Shopby\Model\Layer\Filter\Rating::class,
                ['layer'=>$layer]
            );
        }
        if ($this->isCustomFilterEnabled('am_is_new')) {
            $additionalFilters[] = $this->objectManager->create(
                \Amasty\Shopby\Model\Layer\Filter\IsNew::class,
                ['layer'=>$layer]
            );
        }
        if ($this->isCustomFilterEnabled('am_on_sale')) {
            $additionalFilters[] = $this->objectManager->create(
                \Amasty\Shopby\Model\Layer\Filter\OnSale::class,
                ['layer'=>$layer]
            );
        }

        return $additionalFilters;
    }

    /**
     * @param string $filterKey
     * @return bool
     */
    protected function isCustomFilterEnabled($filterKey)
    {
        return $this->scopeConfig->isSetFlag(
            'amshopby/' . $filterKey . '_filter/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $listStandartFilters
     * @param $listAdditionalFilters
     * @return array
     */
    protected function insertAdditionalFilters($listStandartFilters, $listAdditionalFilters)
    {
        if (count($listAdditionalFilters) == 0) {
            return $listStandartFilters;
        }

        return array_merge($listStandartFilters, $listAdditionalFilters);
    }

    /**
     * @param $first
     * @param $second
     * @return bool
     */
    public function sortingByPosition($first, $second)
    {
        return $this->getFilterPosition($first) > $this->getFilterPosition($second);
    }

    /**
     * @param $filter
     * @return int
     */
    public function getFilterPosition($filter)
    {
        if ($filter->hasAttributeModel()) {
            $position = $filter->getAttributeModel()->getPosition();
        } else {
            $position = $filter->getPosition();
        }

        return $position;
    }

    protected function isEnabledShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
