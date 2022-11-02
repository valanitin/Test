<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoToolKit
 */


declare(strict_types=1);

namespace Amasty\SeoToolKit\Plugin\Catalog\Helper\Product;

use Amasty\SeoToolKit\Model\Source\Eav\Robots;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Product\View;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Page\Config;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Store\Model\StoreManagerInterface;

class ViewPlugin
{
    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Escaper
     */
    private $escaper;

    public function __construct(
        Registry $coreRegistry,
        UrlInterface $urlBuilder,
        Escaper $escaper,
        StoreManagerInterface $storeManager
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
    }

    /**
     * @param View $subject
     * @param callable $proceed
     * @param ResultPage $resultPage
     * @param $productId
     * @param $controller
     * @param null $params
     * @return View
     */
    public function aroundPrepareAndRender(
        View $subject,
        callable $proceed,
        ResultPage $resultPage,
        $productId,
        $controller,
        $params = null
    ) {
        $proceed($resultPage, $productId, $controller, $params);
        $product = $this->coreRegistry->registry('current_product');
        if ($product) {
            $pageConfig = $resultPage->getConfig();
            $this->addCanonical($pageConfig, $product);
            $this->addRobots($pageConfig, $product);
        }

        return $subject;
    }

    private function addCanonical(Config $pageConfig, ProductInterface $product)
    {
        $canonical = $product->getAmtoolkitCanonical();
        if ($pageConfig->getAssetCollection()->getGroupByContentType('canonical') && $canonical) {
            $pageConfig->getAssetCollection()
                ->remove($product->getUrlModel()->getUrl($product, ['_ignore_category' => true]));
            $baseUrl = $this->storeManager->getStore()->getBaseUrl();
            $pageConfig->addRemotePageAsset(
                $this->escaper->escapeUrl($baseUrl . ltrim($canonical, '/')),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }
    }

    private function addRobots(Config $pageConfig, ProductInterface $product)
    {
        $robots = $product->getAmtoolkitRobots();
        if ($robots && $robots !== 'default') {
            $pageConfig->setRobots($robots);
        }
    }
}
