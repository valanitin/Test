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
 * @package    Bss_SeoCore
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SeoCore\Plugin\Model;

/**
 * Class Product
 * @package Bss\SeoCore\Plugin\Model
 */
class Product
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;
    /**
     * @var \Magento\Cms\Model\Page
     */
    private $page;
    /**
     * @var \Bss\SeoCore\Helper\Data
     */
    private $dataHelper;

    /**
     * Product constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Cms\Model\Page $page
     * @param \Bss\SeoCore\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Cms\Model\Page $page,
        \Bss\SeoCore\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->page = $page;
        $this->coreRegistry = $registry;
        $this->request = $request;
    }

    /**
     * @param \Magento\Theme\Block\Html\Title $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetPageHeading(\Magento\Theme\Block\Html\Title $subject, $result)
    {
        $statusEnable = $this->dataHelper->isEnableH1Title();
        if (!$statusEnable) {
            return $result;
        }
        $fullActionName = $this->getFullActionName();
        if ($fullActionName === 'catalog_product_view') {
            //Get Product from Registry
            $product = $this->getProduct();
            $productH1Tag = $product->getData('seo_h1_title');
            if ($productH1Tag) {
                return $productH1Tag;
            }
        }

        if ($fullActionName === 'catalog_category_view') {
            $category = $this->getCategory();
            $categoryH1Tag = $category->getData('seo_h1_title');
            if ($categoryH1Tag) {
                return $categoryH1Tag;
            }
        }

        if ($fullActionName === 'cms_page_view') {
            $cmsPageH1Tag = $this->page->getData('seo_h1_title');
            if ($cmsPageH1Tag) {
                return $cmsPageH1Tag;
            }
        }
        return $result;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->coreRegistry->registry('current_category');
    }

    /**
     * @return mixed
     */
    public function getCmsPage()
    {
        return $this->coreRegistry->registry('cms_page');
    }

    /**
     * @return string
     */
    public function getFullActionName()
    {
        return $this->request->getFullActionName();
    }
}