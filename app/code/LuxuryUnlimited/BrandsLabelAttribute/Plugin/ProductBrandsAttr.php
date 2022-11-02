<?php

declare(strict_types=1);

namespace LuxuryUnlimited\BrandsLabelAttribute\Plugin;

use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;

/**
 * class ProductBrandsAttr
 */
class ProductBrandsAttr
{
    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @param Config $eavConfig
     */
    public function __construct(
        Config $eavConfig
    ) {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Framework\Api\SearchResults $searchResults
     * @return \Magento\Framework\Api\SearchResults
     * @throws LocalizedException
     */
    public function afterGetList(\Magento\Catalog\Api\ProductRepositoryInterface $subject, \Magento\Framework\Api\SearchResults $searchResults) {
        $products = [];
        foreach ($searchResults->getItems() as $entity) {
            /** Get Current Extension Attributes from Product */
            $extensionAttributes = $entity->getExtensionAttributes();
            $brandValue = $entity->getCustomAttribute('brands');
            $brandLabel = '';
            if ($brandValue){
                $attribute = $this->eavConfig->getAttribute('catalog_product', 'brands');
                $brandLabel = $attribute->getSource()->getOptionText($brandValue->getValue());
            }
            $colorValue = $entity->getCustomAttribute('color_v2');
            $colorLabel = '';
            if ($colorValue){
                $attributeColor = $this->eavConfig->getAttribute('catalog_product', 'color_v2');
                $colorLabel = $attributeColor->getSource()->getOptionText($colorValue->getValue());
            }

            $extensionAttributes->setBrands($brandLabel);
            $extensionAttributes->setColor($colorLabel);
            $entity->setExtensionAttributes($extensionAttributes);
            $products[] = $entity;
        }
        $searchResults->setItems($products);
        return $searchResults;
    }
}
