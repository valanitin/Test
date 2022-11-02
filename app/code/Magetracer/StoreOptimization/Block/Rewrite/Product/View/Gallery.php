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
namespace Magetracer\StoreOptimization\Block\Rewrite\Product\View;

use Magento\Framework\DataObject;

class Gallery extends \Magento\Catalog\Block\Product\View\Gallery
{
    /**
     * @inheritDoc
     */
    public function getGalleryImagesJson()
    {
        $imagesItems = [];
        /** @var DataObject $image */
        foreach ($this->getGalleryImages() as $image) {
            $imageItem = new DataObject(
                [
                    'thumb' => $image->getData('small_image_url'),
                    'img' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url'),
                    'caption' => ($image->getLabel() ?: $this->getProduct()->getName()),
                    'position' => $image->getData('position'),
                    'isMain'   => $this->isMainImage($image),
                    'type' => str_replace('external-', '', $image->getMediaType()),
                    'videoUrl' => $image->getVideoUrl(),
                    'original_image' => $image->getUrl()
                ]
            );
            foreach ($this->getGalleryImagesConfig()->getItems() as $imageConfig) {
                $imageItem->setData(
                    $imageConfig->getData('json_object_key'),
                    $image->getData($imageConfig->getData('data_object_key'))
                );
            }
            $imagesItems[] = $imageItem->toArray();
        }
        if (empty($imagesItems)) {
            $imagesItems[] = [
                'thumb' => $this->_imageHelper->getDefaultPlaceholderUrl('thumbnail'),
                'img' => $this->_imageHelper->getDefaultPlaceholderUrl('image'),
                'full' => $this->_imageHelper->getDefaultPlaceholderUrl('image'),
                'caption' => '',
                'position' => '0',
                'isMain' => true,
                'type' => 'image',
                'videoUrl' => null,
            ];
        }
        return json_encode($imagesItems);
    }
}
