<?php
/**
 * @author Raju Sadadiya
 * @package Dynamic_Notifyme
 */ 

namespace Dynamic\Notifyme\ViewModel;
 
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Catalog\Model\ProductRepository;


class ConfigurableOptions implements ArgumentInterface
{
    const TYPE_CONFIGURABLE = "configurable";

    /**
     * @var ProductRepository
     */
    protected $productRepository;
 
    public function __construct(
        ProductRepository $productRepository
    ) {
        $this->productRepository = $productRepository;
    }

    public function isConfigurable($product)
    {
        return $product->getTypeId() == self::TYPE_CONFIGURABLE ? true : false;
    }

    public function getConfigurableOptions($productId)
    {
        $product = $this->productRepository->getById($productId);
        $productOptions = $product->getTypeInstance()->getConfigurableOptions($product);
        $options = array();
        foreach($productOptions as $attribute){
            foreach($attribute as $childProductItem){
                $options[$childProductItem['attribute_code']]["label"] = $childProductItem["super_attribute_label"];
                $options[$childProductItem['attribute_code']]["values"][] = [
                    "sku" => $childProductItem['sku'],
                    "option_title" => $childProductItem['default_title'],
                    "option_value" => $childProductItem["value_index"]
                ];

            }
        }
        return $options;
    }
}
