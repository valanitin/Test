<?php
/**
 * Copyright © LuxuryUnlimited, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace LuxuryUnlimited\CmsSearch\ViewModel;

use Magento\Cms\Model\PageFactory;
use Magento\Cms\Helper\Page as Helper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Search\Model\QueryFactory;
use Magento\Eav\Model\Config;

/**
 * Product search result block
 */
class CmsSearch implements ArgumentInterface
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var QueryFactory
     */
    private $queryFactory;

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var UrlInterface
     */
    protected $urlInterface;

    /**
     * @param PageFactory $pageFactory
     * @param Helper $helper
     * @param QueryFactory $queryFactory
     * @param Config $eavConfig
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        PageFactory $pageFactory,
        Helper $helper,
        QueryFactory $queryFactory,
        Config $eavConfig,
        UrlInterface $urlInterface
    ) {
        $this->pageFactory = $pageFactory;
        $this->helper = $helper;
        $this->queryFactory = $queryFactory;
        $this->eavConfig = $eavConfig;
        $this->urlInterface = $urlInterface;
    }

    /**
     * Retrieve query model object
     *
     * @return string
     */
    public function _getQuery()
    {
        return $this->queryFactory->get()->getQueryText();
    }

    /**
     * Retrieve cms results
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCmsResults()
    {
        $pageData = "";
        if ($this->_getQuery() != '') {
            $queryText = $this->_getQuery();
            $pagesCollection = $this->pageFactory->create()->getCollection()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('title', ['like' => '%' . $queryText . '%'])
                ->addFieldToFilter('content', ['like' => '%' . $queryText . '%']);

            if ($pagesCollection->count()) {
                $pageData = '<div class="cms-search"> <div class="title"> Page Results </div><ul id="cms" role="listbox"> ';
                foreach ($pagesCollection as $page) {
                    $pageData .= '<li> <a href=' . $this->helper->getPageUrl($page->getPageId()) . '><span>' . $page->getTitle() . '</span></a> </li>';
                }
                $pageData .= '</ul></div>';
            }
            $brands = $this->searchInBrands($queryText);
            if ($brands) {
                $pageData = '<div class="brands-search"> <div class="title"> Brands Results </div><ul id="brands" role="listbox"> ';
                foreach ($brands as $option) {
                    $brandName = $option['label'];
                    $brandLink = preg_replace('/\s+/', '-', $brandName);
                    $brandLink = strtolower($brandLink);
                    $brandLink = 'brand/' . $brandLink . '.html';
                    $pageData .= '<li> <a href=' . $this->getBaseUrl() . $brandLink . '><span>' . ucwords($brandName) . '</span></a> </li>';
                }
                $pageData .= '</ul></div>';
            }
        }
        return $pageData;
    }

    /**
     * @param $search
     * @return array
     * @throws LocalizedException
     */
    public function searchInBrands($search): array
    {
        $allBrands = $this->returnAllOptions();
        $result = [];
        foreach ($allBrands as $option) {
            $brandId = $option['value'];
            $brandName = $option['label'];
            if (stripos($brandName, $search) !== false) {
                $result[] = ['value' => $brandId, 'label' => __($brandName)];
            }
        }
        return $result;
    }

    /**
     * @return Config
     */
    public function getEavConfigManager()
    {
        return $this->eavConfig;
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBrands()
    {
        return $this->getEavConfigManager()->getAttribute('catalog_product', 'brands');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function returnAllOptions(): array
    {
        return $this->getBrands()->getSource()->getAllOptions();
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlInterface->getBaseUrl();
    }
}
