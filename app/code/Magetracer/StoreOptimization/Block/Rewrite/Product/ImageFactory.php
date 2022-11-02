<?php
/**
 * Mage Tracer.
 *
 * @category  Magetracer
 * @package   Magetracer_StoreOptimization
 * @author    Magetracer
 * @copyright Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license   https://www.magetracer.com/license.html
 */
namespace Magetracer\StoreOptimization\Block\Rewrite\Product;

use Magento\Catalog\Block\Product\Image as ImageBlock;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssetImageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Image as ImageHelper;

/**
 * Overwrite Core ImageFactory File
 */
class ImageFactory extends \Magento\Catalog\Block\Product\ImageFactory
{

    /**
     * @inheritDoc
     */
    public function create(Product $product, string $imageId, array $attributes = null): ImageBlock
    {
        /**
         * accessing parent private properties
         */
        $getPrentationConfig = \Closure::bind(function () {
            return $this->presentationConfig;
        }, $this, \Magento\Catalog\Block\Product\ImageFactory::class);
        
        $getImageBuilder = \Closure::bind(function () {
            return $this->imageParamsBuilder;
        }, $this, \Magento\Catalog\Block\Product\ImageFactory::class);
        
        $getViewAssetPlaceHoder = \Closure::bind(function () {
            return $this->viewAssetPlaceholderFactory;
        }, $this, \Magento\Catalog\Block\Product\ImageFactory::class);

        $getViewAssetImage = \Closure::bind(function () {
            return $this->viewAssetImageFactory;
        }, $this, \Magento\Catalog\Block\Product\ImageFactory::class);

        $viewImageConfig = $getPrentationConfig()->getViewConfig()->getMediaAttributes(
            'Magento_Catalog',
            ImageHelper::MEDIA_TYPE_CONFIG_NODE,
            $imageId
        );
        
        $imageMiscParams = $getImageBuilder()->build($viewImageConfig);
        $originalFilePath = $product->getData($imageMiscParams['image_type']);

        if ($originalFilePath === null || $originalFilePath === 'no_selection') {
            $imageAsset = $getViewAssetPlaceHoder()->create(
                [
                    'type' => $imageMiscParams['image_type']
                ]
            );
        } else {
            $imageAsset = $getViewAssetImage()->create(
                [
                    'miscParams' => $imageMiscParams,
                    'filePath' => $originalFilePath,
                ]
            );
        }
        $block = parent::create($product, $imageId, $attributes);
        if (method_exists($imageAsset, "getOriginalImageUrl")) {
            $block->setOriginalImageUrl($imageAsset->getOriginalImageUrl());
        }
        return $block;
    }
}
