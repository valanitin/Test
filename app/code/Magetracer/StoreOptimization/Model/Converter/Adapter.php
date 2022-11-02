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
namespace Magetracer\StoreOptimization\Model\Converter;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use WebPConvert\WebPConvert;
use Magetracer\StoreOptimization\Model\JpegCompressor;

class Adapter
{
    /**
     * @var  \Magetracer\StoreOptimization\Helper\Image
     */
    protected $imageHelper;

    public function __construct(
        \Magetracer\StoreOptimization\Helper\Image $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
    }
    
    /**
     * convert image to webp
     *
     * @param string $imagePath
     * @param string $destination
     * @param array $options
     * @param string $type
     * @return void
     */
    public function convert($imagePath, $destination, $options, $type = "jpeg")
    {
        if ($type == 'webp') {
            WebPConvert::convert($imagePath, $destination, $options);
        } elseif ($type == 'jpeg') {
            JpegCompressor::convert($imagePath, $destination);
        }
    }
}
