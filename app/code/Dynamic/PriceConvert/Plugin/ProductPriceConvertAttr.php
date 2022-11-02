<?php

namespace Dynamic\PriceConvert\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product as ProductModel;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Directory\Model\PriceCurrency;
use Magento\Framework\Webapi\Rest\Request;

/**
 * class ProductPriceConvertAttr
 */
class ProductPriceConvertAttr
{
    /**
     * @var PriceHelper
     */
    private $priceHelper;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PriceCurrency
     */
    protected $priceCurrency;

    /**
     * @param PriceHelper $priceHelper
     * @param Request $request
     * @param PriceCurrency $priceCurrency
     */
    public function __construct(
        PriceHelper $priceHelper,
        Request $request,
        PriceCurrency $priceCurrency
    ) {
        $this->priceHelper = $priceHelper;
        $this->request = $request;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param ProductInterface $entity
     * @return ProductInterface
     */
    public function afterGet(
        \Magento\Catalog\Api\ProductRepositoryInterface $subject,
        \Magento\Catalog\Api\Data\ProductInterface $entity
    )
    {
        $product = $entity;
        /** Get Current Extension Attributes from Product */
        $extensionAttributes = $product->getExtensionAttributes();

        if($entity->getSpecialPrice() != 0) {
            $regularFinalPrice = $this->priceHelper->currency($entity->getFinalPrice(), true, false);
            $specialFinalPrice = $this->priceHelper->currency($entity->getPrice(), true, false);
            $extensionAttributes->setConvertedRegularPrice($regularFinalPrice);
            $extensionAttributes->setConvertedRegularOldPrice($specialFinalPrice);
        } else {
            if ($entity->getFinalPrice() == 0 && $entity->getTypeId() == 'configurable'){
                foreach($entity->getTypeInstance()->getUsedProducts($entity) as $childProduct){
                    $regularFinalPrice = $this->priceHelper->currency($childProduct->getPrice(), true, false);
                    continue;
                }
            }
            else{
                $regularFinalPrice = $this->priceHelper->currency($entity->getFinalPrice(), true, false);
            }
            $specialFinalPrice = $this->priceHelper->currency(0, true, false);
            $extensionAttributes->setConvertedRegularPrice($regularFinalPrice);
            $extensionAttributes->setConvertedRegularOldPrice($specialFinalPrice);
        }

        $product->setExtensionAttributes($extensionAttributes);
        return $product;
    }

    /**
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $subject
     * @param \Magento\Framework\Api\SearchResults $searchResults
     * @return \Magento\Framework\Api\SearchResults
     */
    public function afterGetList(\Magento\Catalog\Api\ProductRepositoryInterface $subject, \Magento\Framework\Api\SearchResults $searchResults) {
        $products = [];
        $currentCurrency = $this->request->getParam('currency');

        foreach ($searchResults->getItems() as $entity) {
            $currency = null;
            if ($currentCurrency) {
                $currency = $currentCurrency;
            }
            /** Get Current Extension Attributes from Product */
            $extensionAttributes = $entity->getExtensionAttributes();
            if($entity->getSpecialPrice() != 0) {
                $regularFinalPrice = $this->priceCurrency->convertAndFormat($entity->getFinalPrice(), false, 2, null, $currency);
                $specialFinalPrice = $this->priceCurrency->convertAndFormat($entity->getPrice(),false, 2, null, $currency);
                $extensionAttributes->setConvertedRegularPrice($regularFinalPrice);
                $extensionAttributes->setConvertedRegularOldPrice($specialFinalPrice);
            } else {
                if ($entity->getFinalPrice() == 0 && $entity->getTypeId() == 'configurable'){
                    $childPrice = 0 ;
                    foreach($entity->getTypeInstance()->getUsedProducts($entity) as $childProduct){
                        $childPrice = $childProduct->getPrice();
                        continue;
                    }
                    $regularFinalPrice = $this->priceCurrency->convertAndFormat($childPrice, false, 2, null, $currency);
                }
                else{
                    if($entity->getTypeId() == 'configurable') {
                        $childPrice = 0 ;
                        foreach($entity->getTypeInstance()->getUsedProducts($entity) as $childProduct){
                            $childPrice = $childProduct->getPrice();
                        }
                        $regularFinalPrice = $this->priceCurrency->convertAndFormat($childPrice,false, 2, null, $currency);
                    } else {
                        $regularFinalPrice = $this->priceCurrency->convertAndFormat($entity->getFinalPrice(),false, 2, null, $currency);
                    }
                }
                $specialFinalPrice = $this->priceCurrency->convertAndFormat(0, false, 2, null, $currency);
                $extensionAttributes->setConvertedRegularPrice($regularFinalPrice);
                $extensionAttributes->setConvertedRegularOldPrice($specialFinalPrice);
            }
            $entity->setExtensionAttributes($extensionAttributes);
            $products[] = $entity;
        }
        $searchResults->setItems($products);
        return $searchResults;
    }
}