<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


declare(strict_types=1);

namespace Amasty\XmlSitemap\Model\Source;

class ProductType
{
    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    public function __construct(\Magento\Framework\App\ProductMetadataInterface $productMetadata)
    {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @return array
     */
    public function toOptionArray() : array
    {
        $optionArray = [];
        $arr = [
            '' => __('None'),
            \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE => __('Simple'),
            \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE => __('Configurable'),
            \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE => __('Downloadable'),
            \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE => __('Grouped'),
            \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL => __('Virtual'),
            \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE => __('Bundle'),
        ];

        $edition = $this->productMetadata->getEdition();
        if ($edition != 'Community') {
            $arr[\Magento\GiftCard\Model\Catalog\Product\Type\Giftcard::TYPE_GIFTCARD] = __('Gift Card');
        }

        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $optionArray;
    }
}
