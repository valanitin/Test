<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Model;

use Amasty\Meta\Api\Data\ConfigInterface;
use Magento\Framework\Exception\AlreadyExistsException;

class Config extends \Magento\Framework\Model\AbstractModel implements ConfigInterface
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_configInheritance = true;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->_init(\Amasty\Meta\Model\ResourceModel\Config::class);
    }

    /**
     * @return object
     */
    public function getCollection()
    {
        $collection = $this->getResourceCollection()->addCategoryFilter();

        return $collection;
    }

    /**
     * @return mixed
     */
    public function getCustomCollection()
    {
        $collection = $this->getResourceCollection()->addCustomFilter();

        return $collection;
    }

    /**
     * @param $url
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigByUrl($url, $storeId = null)
    {
        $collection = $this->getResourceCollection();
        $collection->addUrlFilter($url, $storeId);
        $collection->getSelect()
            ->order("store_id DESC")
            ->order("priority DESC");

        return $collection;
    }

    public function beforeSave()
    {
        if (!$this->getIsCustom()) {
            $this->setIsCustom($this->getCategoryId() === null);
        }

        if ($this->_storeManager->isSingleStoreMode()) {
            $storeId = $this->_storeManager->getStore()->getId();
            $this->setStoreId($storeId);
        }

        if ($this->ifStoreConfigExists($this)) {
            throw new AlreadyExistsException(__('Template already exists in chosen store'));
        }

        return parent::beforeSave();
    }

    public function ifStoreConfigExists(\Amasty\Meta\Model\Config $item)
    {

        $collection = $this->getResourceCollection()
            ->addFieldToFilter('store_id', $item->getStoreId());

        if ($item->getCategoryId()) {
            $collection
                ->addFieldToFilter('category_id', $item->getCategoryId())
                ->addFieldToFilter('is_custom', 0);
        } else {
            $collection
                ->addFieldToFilter('custom_url', $item->getCustomUrl())
                ->addFieldToFilter('is_custom', 1);
        }

        if ($item->getId()) {
            $collection->addFieldToFilter($this->getIdFieldName(), ['neq' => $item->getId()]);
        }

        return $collection->getSize() > 0;
    }

    public function getRecursionConfigData($paths, $storeId)
    {
        if (empty($paths)) {
            $paths = [[ \Magento\Catalog\Model\Category::TREE_ROOT_ID]];
        }

        $distances = [];

        foreach ($paths as $pathIndex => $path) {
            foreach ($path as $categoryIndex => $category) {
                if (isset($distances[$category])) {
                    $distances[$category]['distance'] = min(
                        $categoryIndex,
                        $distances[$category]['distance']
                    );
                } else {
                    $distances[$category] = [
                        'distance' => $categoryIndex,
                        'path'     => $pathIndex
                    ];
                }
            }
        }

        $queryIds = array_keys($distances);

        $configs = $this->getResourceCollection()
            ->addFieldToFilter('store_id', ['in' => [(int)$storeId, 0]])
            ->addFieldToFilter('category_id', ['in' => $queryIds])
            ->addFieldToFilter('is_custom', 0);

        $foundIds = $configs->getColumnValues('category_id');

        if (empty($foundIds)) {
            return [];
        }

        $bestPaths = [];
        $minDistance = $distances[$foundIds[0]]['distance'];

        foreach ($distances as $id => $category) {
            if (in_array($id, $foundIds)) {
                if ($category['distance'] <= $minDistance) {
                    $minDistance = $category['distance'];
                    $bestPaths[] = $paths[$category['path']];
                }
            }
        }

        $result = [];
        foreach ($bestPaths as $bestPath) {
            $orders = array_flip($bestPath);
            foreach ($configs as $config) {
                if ($config->getCategoryId() == \Magento\Catalog\Model\Category::TREE_ROOT_ID) {
                    // Lowest priority for default category
                    $config->setOrder(count($bestPath));
                    $result [] = $config;
                } elseif (in_array($config->getCategoryId(), $bestPath)) {
                    $config->setOrder($orders[$config->getCategoryId()]);
                    $result [] = $config;
                }
            }
        }

        usort($result, [$this, '_compareConfigs']);

        if (isset($result[0]) && is_object($result[0])) {
            $applied = $this->_registry->registry('ammeta_applied_rule');
            if (!is_array($applied)) {
                $applied = [];
            }
            $applied[] = __('Template (%1) #%2', __('Category'), $result[0]->getId());
            $this->_registry->unregister('ammeta_applied_rule');
            $this->_registry->register('ammeta_applied_rule', $applied);
        }

        if (!$this->_configInheritance) {
            return [$result[0]];
        }

        return $result;
    }

    protected function _compareConfigs($a, $b)
    {
        if ($a->getPriority() != $b->getPriority()) {
            $bOrder = $a->getPriority();
            $aOrder = $b->getPriority();
        } else {
            $aOrder = $a->getOrder();
            $bOrder = $b->getOrder();
        }
        if ($aOrder < $bOrder) {
            return -1;
        } elseif ($aOrder > $bOrder) {
            return 1;
        }

        return ($a->getStoreId() > $b->getStoreId()) ? 1 : -1;
    }

    /**
     * @inheritdoc
     */
    public function getConfigId()
    {
        return $this->_getData(ConfigInterface::CONFIG_ID);
    }

    /**
     * @inheritdoc
     */
    public function setConfigId($configId)
    {
        $this->setData(ConfigInterface::CONFIG_ID, $configId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCategoryId()
    {
        return $this->_getData(ConfigInterface::CATEGORY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCategoryId($categoryId)
    {
        $this->setData(ConfigInterface::CATEGORY_ID, $categoryId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getStoreId()
    {
        return $this->_getData(ConfigInterface::STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setStoreId($storeId)
    {
        $this->setData(ConfigInterface::STORE_ID, $storeId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsCustom()
    {
        return $this->_getData(ConfigInterface::IS_CUSTOM);
    }

    /**
     * @inheritdoc
     */
    public function setIsCustom($isCustom)
    {
        $this->setData(ConfigInterface::IS_CUSTOM, $isCustom);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomUrl()
    {
        return $this->_getData(ConfigInterface::CUSTOM_URL);
    }

    /**
     * @inheritdoc
     */
    public function setCustomUrl($customUrl)
    {
        $this->setData(ConfigInterface::CUSTOM_URL, $customUrl);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->_getData(ConfigInterface::PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        $this->setData(ConfigInterface::PRIORITY, $priority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomMetaTitle()
    {
        return $this->_getData(ConfigInterface::CUSTOM_META_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setCustomMetaTitle($customMetaTitle)
    {
        $this->setData(ConfigInterface::CUSTOM_META_TITLE, $customMetaTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomMetaKeywords()
    {
        return $this->_getData(ConfigInterface::CUSTOM_META_KEYWORDS);
    }

    /**
     * @inheritdoc
     */
    public function setCustomMetaKeywords($customMetaKeywords)
    {
        $this->setData(ConfigInterface::CUSTOM_META_KEYWORDS, $customMetaKeywords);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomMetaDescription()
    {
        return $this->_getData(ConfigInterface::CUSTOM_META_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setCustomMetaDescription($customMetaDescription)
    {
        $this->setData(ConfigInterface::CUSTOM_META_DESCRIPTION, $customMetaDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomCanonicalUrl()
    {
        return $this->_getData(ConfigInterface::CUSTOM_CANONICAL_URL);
    }

    /**
     * @inheritdoc
     */
    public function setCustomCanonicalUrl($customCanonicalUrl)
    {
        $this->setData(ConfigInterface::CUSTOM_CANONICAL_URL, $customCanonicalUrl);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomRobots()
    {
        return $this->_getData(ConfigInterface::CUSTOM_ROBOTS);
    }

    /**
     * @inheritdoc
     */
    public function setCustomRobots($customRobots)
    {
        $this->setData(ConfigInterface::CUSTOM_ROBOTS, $customRobots);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomH1Tag()
    {
        return $this->_getData(ConfigInterface::CUSTOM_H1_TAG);
    }

    /**
     * @inheritdoc
     */
    public function setCustomH1Tag($customH1Tag)
    {
        $this->setData(ConfigInterface::CUSTOM_H1_TAG, $customH1Tag);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomInPageText()
    {
        return $this->_getData(ConfigInterface::CUSTOM_IN_PAGE_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setCustomInPageText($customInPageText)
    {
        $this->setData(ConfigInterface::CUSTOM_IN_PAGE_TEXT, $customInPageText);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCatMetaTitle()
    {
        return $this->_getData(ConfigInterface::CAT_META_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setCatMetaTitle($catMetaTitle)
    {
        $this->setData(ConfigInterface::CAT_META_TITLE, $catMetaTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCatMetaDescription()
    {
        return $this->_getData(ConfigInterface::CAT_META_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setCatMetaDescription($catMetaDescription)
    {
        $this->setData(ConfigInterface::CAT_META_DESCRIPTION, $catMetaDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCatMetaKeywords()
    {
        return $this->_getData(ConfigInterface::CAT_META_KEYWORDS);
    }

    /**
     * @inheritdoc
     */
    public function setCatMetaKeywords($catMetaKeywords)
    {
        $this->setData(ConfigInterface::CAT_META_KEYWORDS, $catMetaKeywords);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCatH1Tag()
    {
        return $this->_getData(ConfigInterface::CAT_H1_TAG);
    }

    /**
     * @inheritdoc
     */
    public function setCatH1Tag($catH1Tag)
    {
        $this->setData(ConfigInterface::CAT_H1_TAG, $catH1Tag);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCatDescription()
    {
        return $this->_getData(ConfigInterface::CAT_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setCatDescription($catDescription)
    {
        $this->setData(ConfigInterface::CAT_DESCRIPTION, $catDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCatImageAlt()
    {
        return $this->_getData(ConfigInterface::CAT_IMAGE_ALT);
    }

    /**
     * @inheritdoc
     */
    public function setCatImageAlt($catImageAlt)
    {
        $this->setData(ConfigInterface::CAT_IMAGE_ALT, $catImageAlt);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCatImageTitle()
    {
        return $this->_getData(ConfigInterface::CAT_IMAGE_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setCatImageTitle($catImageTitle)
    {
        $this->setData(ConfigInterface::CAT_IMAGE_TITLE, $catImageTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCatAfterProductText()
    {
        return $this->_getData(ConfigInterface::CAT_AFTER_PRODUCT_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setCatAfterProductText($catAfterProductText)
    {
        $this->setData(ConfigInterface::CAT_AFTER_PRODUCT_TEXT, $catAfterProductText);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductMetaTitle()
    {
        return $this->_getData(ConfigInterface::PRODUCT_META_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setProductMetaTitle($productMetaTitle)
    {
        $this->setData(ConfigInterface::PRODUCT_META_TITLE, $productMetaTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductMetaKeywords()
    {
        return $this->_getData(ConfigInterface::PRODUCT_META_KEYWORDS);
    }

    /**
     * @inheritdoc
     */
    public function setProductMetaKeywords($productMetaKeywords)
    {
        $this->setData(ConfigInterface::PRODUCT_META_KEYWORDS, $productMetaKeywords);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductMetaDescription()
    {
        return $this->_getData(ConfigInterface::PRODUCT_META_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setProductMetaDescription($productMetaDescription)
    {
        $this->setData(ConfigInterface::PRODUCT_META_DESCRIPTION, $productMetaDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductH1Tag()
    {
        return $this->_getData(ConfigInterface::PRODUCT_H1_TAG);
    }

    /**
     * @inheritdoc
     */
    public function setProductH1Tag($productH1Tag)
    {
        $this->setData(ConfigInterface::PRODUCT_H1_TAG, $productH1Tag);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductShortDescription()
    {
        return $this->_getData(ConfigInterface::PRODUCT_SHORT_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setProductShortDescription($productShortDescription)
    {
        $this->setData(ConfigInterface::PRODUCT_SHORT_DESCRIPTION, $productShortDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductDescription()
    {
        return $this->_getData(ConfigInterface::PRODUCT_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setProductDescription($productDescription)
    {
        $this->setData(ConfigInterface::PRODUCT_DESCRIPTION, $productDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubProductMetaTitle()
    {
        return $this->_getData(ConfigInterface::SUB_PRODUCT_META_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setSubProductMetaTitle($subProductMetaTitle)
    {
        $this->setData(ConfigInterface::SUB_PRODUCT_META_TITLE, $subProductMetaTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubProductMetaKeywords()
    {
        return $this->_getData(ConfigInterface::SUB_PRODUCT_META_KEYWORDS);
    }

    /**
     * @inheritdoc
     */
    public function setSubProductMetaKeywords($subProductMetaKeywords)
    {
        $this->setData(ConfigInterface::SUB_PRODUCT_META_KEYWORDS, $subProductMetaKeywords);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubProductMetaDescription()
    {
        return $this->_getData(ConfigInterface::SUB_PRODUCT_META_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setSubProductMetaDescription($subProductMetaDescription)
    {
        $this->setData(ConfigInterface::SUB_PRODUCT_META_DESCRIPTION, $subProductMetaDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubProductH1Tag()
    {
        return $this->_getData(ConfigInterface::SUB_PRODUCT_H1_TAG);
    }

    /**
     * @inheritdoc
     */
    public function setSubProductH1Tag($subProductH1Tag)
    {
        $this->setData(ConfigInterface::SUB_PRODUCT_H1_TAG, $subProductH1Tag);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubProductShortDescription()
    {
        return $this->_getData(ConfigInterface::SUB_PRODUCT_SHORT_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setSubProductShortDescription($subProductShortDescription)
    {
        $this->setData(ConfigInterface::SUB_PRODUCT_SHORT_DESCRIPTION, $subProductShortDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubProductDescription()
    {
        return $this->_getData(ConfigInterface::SUB_PRODUCT_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setSubProductDescription($subProductDescription)
    {
        $this->setData(ConfigInterface::SUB_PRODUCT_DESCRIPTION, $subProductDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsBrandConfig()
    {
        return $this->_getData(ConfigInterface::IS_BRAND_CONFIG);
    }

    /**
     * @inheritdoc
     */
    public function setIsBrandConfig($isBrandConfig)
    {
        $this->setData(ConfigInterface::IS_BRAND_CONFIG, $isBrandConfig);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrandMetaTitle()
    {
        return $this->_getData(ConfigInterface::BRAND_META_TITLE);
    }

    /**
     * @inheritdoc
     */
    public function setBrandMetaTitle($brandMetaTitle)
    {
        $this->setData(ConfigInterface::BRAND_META_TITLE, $brandMetaTitle);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrandMetaDescription()
    {
        return $this->_getData(ConfigInterface::BRAND_META_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setBrandMetaDescription($brandMetaDescription)
    {
        $this->setData(ConfigInterface::BRAND_META_DESCRIPTION, $brandMetaDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrandMetaKeywords()
    {
        return $this->_getData(ConfigInterface::BRAND_META_KEYWORDS);
    }

    /**
     * @inheritdoc
     */
    public function setBrandMetaKeywords($brandMetaKeywords)
    {
        $this->setData(ConfigInterface::BRAND_META_KEYWORDS, $brandMetaKeywords);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrandH1Tag()
    {
        return $this->_getData(ConfigInterface::BRAND_H1_TAG);
    }

    /**
     * @inheritdoc
     */
    public function setBrandH1Tag($brandH1Tag)
    {
        $this->setData(ConfigInterface::BRAND_H1_TAG, $brandH1Tag);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrandDescription()
    {
        return $this->_getData(ConfigInterface::BRAND_DESCRIPTION);
    }

    /**
     * @inheritdoc
     */
    public function setBrandDescription($brandDescription)
    {
        $this->setData(ConfigInterface::BRAND_DESCRIPTION, $brandDescription);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getBrandAfterProductText()
    {
        return $this->_getData(ConfigInterface::BRAND_AFTER_PRODUCT_TEXT);
    }

    /**
     * @inheritdoc
     */
    public function setBrandAfterProductText($brandAfterProductText)
    {
        $this->setData(ConfigInterface::BRAND_AFTER_PRODUCT_TEXT, $brandAfterProductText);

        return $this;
    }
}
