<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Observer\Catalog\Category;

use Amasty\Meta\Model\Registry;
use Amasty\Meta\Plugin\ShopbyBrand\Controller\RouterPlugin;
use Magento\Framework\Event\ObserverInterface;

class InitAfter implements ObserverInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Catalog\Model\Layer\Category\FilterableAttributeList
     */
    protected $filterableAttributeList;

    /**
     * Request object
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;

    /**
     * @var \Magento\Catalog\Helper\Data
     */
    protected $catalogHelper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $metaHelper;

    /**
     * @var Registry
     */
    private $metaRegistry;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $configInterface,
        \Magento\Catalog\Model\Layer\Category\FilterableAttributeList $filterableAttributeList,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Amasty\Meta\Helper\Data $metaHelper,
        Registry $metaRegistry
    ) {
        $this->_coreRegistry = $registry;
        $this->requestInterface = $requestInterface;
        $this->_scopeConfig = $configInterface;
        $this->catalogHelper = $catalogHelper;
        $this->_storeManager = $storeManagerInterface;
        $this->metaHelper = $metaHelper;
        $this->filterableAttributeList = $filterableAttributeList;
        $this->metaRegistry = $metaRegistry;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $enabled = $this->_scopeConfig->getValue(
            'ammeta/cat/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$enabled) {
            return;
        }

        /**
         * @var $cat \Magento\Catalog\Model\Category
         */
        $cat = $observer->getEvent()->getCategory();

        $pathIds = array_reverse($cat->getPathIds());
        array_shift($pathIds);

        $configFromUrl = $this->metaHelper->getMetaConfigByUrl();
        $configData    = null;

        $replace = [
            'meta_title',
            'meta_keywords',
            'meta_description',
            'description',
            'h1_tag',
            'image_alt',
            'image_title',
            'after_product_text',
        ];

        $forceOverwrite = $this->_scopeConfig->isSetFlag('ammeta/cat/force');
        $replacedData = [];

        foreach ($replace as $key) {
            if (!$forceOverwrite && $this->isCategoryAttribute($key) && trim($cat->getData($key))) {
                continue;
            }

            $pattern = null;
            $isFromUrl = false;
            if (!empty($configFromUrl[$key])) {
                $pattern = $configFromUrl[$key];
                $isFromUrl = true;
            } else {
                if (!$configData) {
                    $prefix = $this->metaRegistry->get(RouterPlugin::IS_BRAND_PAGE) ? 'brand_' : 'cat_';
                    $configData = $this->metaHelper->_getConfigData([$pathIds], $replace, $prefix);
                }

                if (!empty($configData[$key])) {
                    $pattern = $configData[$key];
                }
            }
            if (!$pattern) {
                continue;
            }

            $this->metaHelper->addEntityToCollection($cat);
            $tag = $this->metaHelper->parse($pattern);
            $max = (int) $this->_scopeConfig->getValue(
                'ammeta/general/max_'.$key,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($max) {
                $tag = mb_substr($tag, 0, $max, \Amasty\Meta\Helper\Data::DEFAULT_CHARSET);
            }

            $replacedData[$key] = $tag;
            $replacedData[$key."_from_url"] = $isFromUrl;
        }

        $this->_coreRegistry->register('ammeta_replaced_data', $replacedData);

        // avoid condition which disable print
        if (isset($replacedData['description'])) {
            $description = $this->metaHelper->escapeHtml($replacedData['description']);
            $cat->setDescription($description);
        }
    }

    /**
     * @param string $attr
     *
     * @return bool
     */
    private function isCategoryAttribute($attr)
    {
        return in_array($attr, [
            'description'
        ]);
    }
}
