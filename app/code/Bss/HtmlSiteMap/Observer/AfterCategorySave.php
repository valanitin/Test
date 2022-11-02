<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    BSS_HtmlSiteMap
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\HtmlSiteMap\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Cache\TypeListInterface as CacheTypeListInterface;

/**
 * Class AfterCategorySave
 * @package Bss\HtmlSiteMap\Observer
 */
class AfterCategorySave implements ObserverInterface
{
    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    private $resourceConfig;
    /**
     * @var \Bss\HtmlSiteMap\Helper\Data
     */
    private $dataHelper;
    /**
     * @var CacheTypeListInterface
     */
    private $cache;

    /**
     * @var \Bss\SeoCore\Helper\Data
     */
    protected $seoCoreHelper;

    /**
     * AfterCategorySave constructor.
     * @param \Magento\Config\Model\ResourceModel\Config $resourceConfig
     * @param CacheTypeListInterface $cache
     * @param \Bss\HtmlSiteMap\Helper\Data $dataHelper
     * @param \Bss\SeoCore\Helper\Data $seoCoreHelper
     */
    public function __construct(
        \Magento\Config\Model\ResourceModel\Config $resourceConfig,
        CacheTypeListInterface $cache,
        \Bss\HtmlSiteMap\Helper\Data $dataHelper,
        \Bss\SeoCore\Helper\Data $seoCoreHelper
    ) {
        $this->cache = $cache;
        $this->dataHelper = $dataHelper;
        $this->resourceConfig = $resourceConfig;
        $this->seoCoreHelper = $seoCoreHelper;
    }

    /**
     * @param EventObserver $observer
     * @return $this|void
     */
    public function execute(EventObserver $observer)
    {
        $categoryObject = $observer->getEvent()->getCategory();
        $storeId = $categoryObject->getStoreId();
        $categoryId = (string)$categoryObject->getId();
        $statusModule = $this->dataHelper->isEnable();

        $categoryDisableArray = $this->getCategoryDisableArray($storeId);

        $excludedHtmlSiteMap = $categoryObject->getExcludedHtmlSitemap();
        if ($statusModule && (int)$excludedHtmlSiteMap === 1) {
            if (!in_array($categoryId, $categoryDisableArray)) {
                $categoryDisableArray[] = $categoryId;
                $this->cache->invalidate('full_page');
            }
        }
        if ($statusModule && (int)$excludedHtmlSiteMap === 0 && in_array($categoryId, $categoryDisableArray)) {
            $arrayKey = array_search($categoryId, $categoryDisableArray);
            if ($arrayKey !== false) {
                unset($categoryDisableArray[$arrayKey]);
                $this->cache->invalidate('full_page');
            }
        }
        $finalCategoriesDisable = $this->seoCoreHelper->implode(',', $categoryDisableArray);
        $scopeToAdd = ScopeInterface::SCOPE_STORES;
        if ((int)$storeId === 0) {
            $scopeToAdd = 'default';
        }
        $this->saveNewConfig(
            'bss_htmlsitemap/category/id_category',
            $finalCategoriesDisable,
            $scopeToAdd,
            $storeId
        );
        return $this;
    }

    /**
     * @param int $storeId
     * @return array
     */
    public function getCategoryDisableArray($storeId)
    {
        $categoryDisable = $this->dataHelper->getConfigWithoutCache($storeId, 'bss_htmlsitemap/category/id_category');
        if ($categoryDisable) {
            $categoryDisableArray = explode(',', $categoryDisable);
        } else {
            $categoryDisableArray = [];
        }
        return $categoryDisableArray;
    }
    /**
     * @param string $path
     * @param string $value
     * @param string $scope
     * @param string $scopeId
     * @return \Magento\Config\Model\ResourceModel\Config
     */
    protected function saveNewConfig($path, $value, $scope = 'default', $scopeId = '')
    {
        return $this->resourceConfig->saveConfig($path, $value, $scope, $scopeId);
    }
}
