<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Navigation;

use Amasty\Shopby\Model\Source\FilterPlacedBlock;
use Magento\Framework\View\Element\Template;

class FilterCollapsing extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Catalog\Model\Layer
     */
    private $catalogLayer;

    /**
     * @var \Amasty\Shopby\Model\Layer\FilterList
     */
    private $filterList;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    private $filterSettingHelper;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $shopbyRequest;

    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Amasty\Shopby\Model\Layer\FilterList $filterList,
        \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->catalogLayer = $layerResolver->get();
        $this->filterList = $filterList;
        $this->filterSettingHelper = $filterSettingHelper;
        $this->shopbyRequest = $shopbyRequest;
    }

    /**
     * @return int[]
     */
    public function getFiltersExpanded()
    {
        $listExpandedFilters = [];
        $position = 0;
        foreach ($this->filterList->getFilters($this->catalogLayer) as $filter) {
            if (!$filter->getItemsCount()) {
                continue;
            }
            $filterSetting = $this->filterSettingHelper->getSettingByLayerFilter($filter);
            $isApplyFilter = $this->shopbyRequest->getParam($filter->getRequestVar(), false);
            if ($filterSetting->isExpanded() || $isApplyFilter) {
                $listExpandedFilters[] = $position;
            }

            if ($filterSetting->getBlockPosition() != FilterPlacedBlock::POSITION_TOP) {
                $position++;
            }
        }

        return $listExpandedFilters;
    }
}
