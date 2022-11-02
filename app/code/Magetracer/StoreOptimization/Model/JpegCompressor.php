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
namespace Magetracer\StoreOptimization\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @phpcs:disable
 */
class JpegCompressor
{
    
    /**
     * convert image to jpeg
     *
     * @param string $imagePath
     * @param string $destination
     * @param array $options
     * @return void
     */
    public static function convert($imagePath, $destination, $options = ['quality' => 60])
    {
        $quality = $options['quality'];
        $mimeType = self::getMimeType($imagePath);
        switch ($mimeType) {
            case "image/jpeg":
                self::compressImageJpeg($imagePath, $destination, $quality);
                break;
            case "image/jpg":
                self::compressImageJpeg($imagePath, $destination, $quality);
                break;
            case "image/png":
                //header('Content-Type: image/png');
                self::compressImagePng($imagePath, $destination, $quality);
                break;
            case "image/webp":
                //header('Content-Type: image/png');
                self::compressImageWebp($imagePath, $destination, $quality);
                break;
            case "image/gif":
                self::compressImageGif($imagePath, $destination, $quality);
                break;
            default:
                throw new \Magento\Framework\Exception\LocalizedException(__("invalid mime type"));

        }
    }

    /**
     * get image mime type
     *
     * @param string $image
     * @return void
     */
    public static function getMimeType($image)
    {
        $info = getimagesize($image);
        return $info['mime'];
    }

    /**
     * compress jpeg to low quality to reduce size
     *
     * @param string $source
     * @param string $destination
     * @param integer $quality
     * @return booelan
     */
    public static function compressImageJpeg($source, $destination, $quality = 65)
    {
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $destination, $quality);
        return true;
    }
    
    /**
     * compress png image to jpeg
     *
     * @param string $source
     * @param string $destination
     * @param integer $quality
     * @return boolean
     */
    public static function compressImagePng($source, $destination, $quality = 65)
    {
        $image = imagecreatefrompng($source);
        imagejpeg($image, $destination, $quality);
        return true;
    }
    
    /**
     * compress gif image to jpeg
     *
     * @param string $source
     * @param string $destination
     * @param integer $quality
     * @return boolean
     */
    public static function compressImageGif($source, $destination, $quality = 65)
    {
        $image = imagecreatefromgif($source);
        imagejpeg($image, $destination, $quality);
        return true;
    
    }
    
    /**
     * compress image to webp
     *
     * @param string $source
     * @param string $destination
     * @param integer $quality
     * @return boolean
     */
    public static function compressImageWebp($source, $destination, $quality = 65)
    {
        $image = imagecreatefromwebp($source);
        imagejpeg($image, $destination, $quality);
        return true;
    }
}
