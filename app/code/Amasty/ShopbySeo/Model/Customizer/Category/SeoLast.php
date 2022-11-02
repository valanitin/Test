<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Model\Customizer\Category;

use Magento\Catalog\Model\Category;

class SeoLast implements \Amasty\ShopbyBase\Model\Customizer\Category\CustomizerInterface
{
    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $amshopbyRequest;

    /**
     * @var \Amasty\ShopbySeo\Helper\Data
     */
    private  $config;

    /**
     * @var \Amasty\ShopbyBase\Helper\Meta
     */
    private $metaHelper;

    function __construct(
        \Amasty\Shopby\Model\Request $amshopbyRequest,
        \Amasty\ShopbySeo\Helper\Data $config,
        \Amasty\ShopbyBase\Helper\Meta $metaHelper
    ) {
        $this->amshopbyRequest = $amshopbyRequest;
        $this->config = $config;
        $this->metaHelper = $metaHelper;
    }

    /**
     * @param Category $category
     */
    public function prepareData(Category $category)
    {
        $page = $this->amshopbyRequest->getParam('p');
        if ($page) {
            $pageMeta = __(' | Page %1', $page);
            $metaTitle = $this->metaHelper->getOriginPageMetaTitle($category) ?: $category->getName();
            $metaDescription = $this->metaHelper->getOriginPageMetaDescription($category);

            if ($this->config->isAddPageToMetaTitleEnabled() && $metaTitle) {
                $category->setMetaTitle($metaTitle . $pageMeta);
            }

            if ($this->config->isAddPageToMetaDescriprionEnabled() && $metaDescription) {
                $category->setMetaDescription($metaDescription . $pageMeta);
            }
        }
    }
}
