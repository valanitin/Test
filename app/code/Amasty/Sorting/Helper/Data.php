<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Sorting
 */


namespace Amasty\Sorting\Helper;

use Amasty\Base\Model\Serializer;
use Magento\CatalogInventory\Model\Configuration;
use Magento\CatalogSearch\Model\ResourceModel\EngineInterface;
use Magento\Framework\Registry;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const CONFIG_SORT_ORDER = 'general/sort_order';

    const SEARCH_SORTING = 'amsorting_search';

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var \Amasty\Base\Model\MagentoVersion
     */
    private $magentoVersion;
    
    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    public function __construct(
        \Amasty\Base\Model\Serializer $serializer,
        Registry $registry,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Amasty\Base\Model\MagentoVersion $magentoVersion
    ) {
        parent::__construct($context);
        $this->serializer = $serializer;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->magentoVersion = $magentoVersion;
    }

    /**
     * Get config value for Store
     *
     * @param string  $path
     * @param null|string|bool|int|\Magento\Store\Model\Store $store
     *
     * @return mixed
     */
    public function getScopeValue($path, $store = null)
    {
        return $this->scopeConfig->getValue(
            'amsorting/' . $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Is Sorting Method Disabled
     *
     * @param string $methodCode
     *
     * @return bool
     */
    public function isMethodDisabled($methodCode)
    {
        $result = false;
        if (!$this->registry->registry('sorting_all_attributes')) {
            $disabledMethods = $this->getScopeValue('general/disable_methods');
            if ($disabledMethods && !empty($disabledMethods)) {
                $disabledMethods = explode(',', $disabledMethods);
                foreach ($disabledMethods as $disabledCode) {
                    if (trim($disabledCode) == $methodCode) {
                        $result = true;
                        break;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Getting default sorting on search pages
     *
     * @return array
     */
    public function getSearchSorting()
    {
        $defaultSorting = [];
        foreach (['search_1', 'search_2', 'search_3'] as $path) {
            if ($sort = $this->getScopeValue('default_sorting/' . $path)) {
                $defaultSorting[] = $sort;
            }
        }

        return $defaultSorting;
    }

    /**
     * @return bool
     */
    public function isYotpoEnabled()
    {
        return $this->getScopeValue('rating_summary/yotpo')
            && $this->_moduleManager->isEnabled('Amasty_Yotpo')
            && $this->_moduleManager->isEnabled('Yotpo_Yotpo');
    }

    /**
     * @return int
     */
    public function getQtyOutStock()
    {
        return (int)$this->scopeConfig->getValue(Configuration::XML_PATH_MIN_QTY);
    }

    /**
     * @return array
     */
    public function getSortOrder()
    {
        $value = $this->getScopeValue(self::CONFIG_SORT_ORDER);
        if ($value) {
            $value = $this->serializer->unserialize($value);
        }
        if (!$value) {
            $value = [];
        }

        return $value;
    }

    /**
     * @param null|int $store
     *
     * @return array
     */
    public function getCategorySorting($store = null)
    {
        $defaultSorting = [];
        foreach (['category_1', 'category_2', 'category_3'] as $path) {
            if ($sort = $this->getScopeValue('default_sorting/' . $path, $store)) {
                $defaultSorting[] = $sort;
            }
        }

        return $defaultSorting;
    }

    /**
     * @param bool $skipStoreCheck
     * @return bool
     */
    public function isElasticSort(bool $skipStoreCheck = false)
    {
        return version_compare($this->magentoVersion->get(), '2.3.2', '>=')
            && strpos($this->scopeConfig->getValue(EngineInterface::CONFIG_ENGINE_PATH), 'elast') !== false
            && ($skipStoreCheck || $this->storeManager->getStore()->getId());
    }

    /**
     * @return array
     */
    public function getAmastyAttributesCodes()
    {
        $result = [
            'created_at',
            $this->getScopeValue('bestsellers/best_attr'),
            $this->getScopeValue('most_viewed/viewed_attr'),
            $this->getScopeValue('new/new_attr')
        ];

        return array_filter($result);
    }
}
