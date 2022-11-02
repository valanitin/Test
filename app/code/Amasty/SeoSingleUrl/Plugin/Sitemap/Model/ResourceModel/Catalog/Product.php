<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_SeoSingleUrl
 */


namespace Amasty\SeoSingleUrl\Plugin\Sitemap\Model\ResourceModel\Catalog;

use Amasty\SeoSingleUrl\Model\Source\Type;
use Magento\Sitemap\Model\ResourceModel\Catalog\Product as ProductResource;
use Magento\Store\Model\Store;

/**
 * Plugin for seofy urls when generating sitemap
 */
class Product
{
    /**
     * @var \Amasty\SeoSingleUrl\Helper\Data
     */
    private $helper;

    public function __construct(
        \Amasty\SeoSingleUrl\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param ProductResource $subject
     * @param array|bool $result
     * @param null|string|bool|int|Store $storeId
     * @return array|bool
     */
    public function afterGetCollection(
        ProductResource $subject,
        $result,
        $storeId
    ) {
        $type = $this->helper->getModuleConfig('general/product_url_type');

        if ($result !== false && $type !== Type::DEFAULT_RULES) {
            foreach ($result as $key => $product) {
                $newUrl = $this->helper->generateSeoUrl((int)$product->getId(), (int)$storeId);
                $product->setData('url', $newUrl);
                $result[$key] = $product;
            }
        }

        return $result;
    }
}
