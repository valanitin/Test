<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Image;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

class Compressor implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    private $mediaDirectory;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    private $imageFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Media\ConfigInterface
     */
    private $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Catalog\Model\View\Asset\Placeholder
     */
    private $placeholderAsset;

    /**
     * @var \Magento\Framework\Filesystem\DriverInterface
     */
    private $fileDriver;

    /**
     * Compressor constructor.
     *
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Image\AdapterFactory $imageFactory
     * @param \Magento\Catalog\Model\Product\Media\ConfigInterface $config
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\View\Asset\Placeholder $placeholderAsset
     * @param \Magento\Framework\Filesystem\DriverInterface $fileDriver
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Image\AdapterFactory $imageFactory,
        \Magento\Catalog\Model\Product\Media\ConfigInterface $config,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Asset\LocalInterface $placeholderAsset,
        \Magento\Framework\Filesystem\DriverInterface $fileDriver
    ) {
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->config = $config;
        $this->placeholderAsset = $placeholderAsset;
        $this->fileDriver = $fileDriver;
    }

    /**
     * @param      $image
     * @param null $width
     * @param null $height
     * @return array|bool
     */
    public function getHomePageImage($image, $width = null, $height = null)
    {
        return $this->resize($image, $width, $height, 'catalog/category', 'pramp/homepage');
    }

    /**
     * @param      $image
     * @param null $width
     * @param null $height
     * @return bool
     */
    public function getCategoryImage($image, $width = null, $height = null)
    {
        $image = basename($image);
        $imageInfo = $this->resize($image, $width, $height, 'catalog/category', 'pramp');

        if (! $imageInfo) {
            $imageInfo = $this->resize($image, $width, $height, 'catalog/tmp/category', 'pramp');
        }

        return $imageInfo;
    }

    /**
     * @param      $image
     * @param int  $width
     * @param null $height
     * @return array|bool
     */
    public function getProductGalleryPreviewImage($image, $width = 120, $height = null)
    {
        $resizedImage =  $this->resize(
            $image,
            $width,
            $height,
            $this->config->getBaseMediaPath(),
            'pramp',
            false
        );

        if (! $resizedImage) {
            $resizedImage =  $this->resize(
                $this->placeholderAsset->getPath(),
                $width,
                $height,
                null,
                'pramp',
                false
            );
        }

        return $resizedImage;
    }

    /**
     * @param        $image
     * @param        $containerWidth
     * @param        $containerHeight
     * @param        $mediaFolder
     * @param string $additionalPath
     * @param bool   $needCrop
     * @return array|bool
     */
    protected function resize( // @codingStandardsIgnoreLine
        $image,
        $containerWidth,
        $containerHeight,
        $mediaFolder,
        $additionalPath = '',
        $needCrop = true
    ) {
        if (null === $mediaFolder) {
            $absolutePath = $image;
        } else {
            $absolutePath = $this->mediaDirectory->getAbsolutePath($mediaFolder) . DIRECTORY_SEPARATOR . $image;
        }

        if (! $image || ! $this->fileDriver->isExists($absolutePath)) {
            return false;
        }

        $mediaFolder = ltrim($mediaFolder, '/') . '/';
        $path = $mediaFolder . 'cache/' . ($additionalPath ?: '');
        $path = ltrim($path, '/') . '/';
        $imageResized = $this->mediaDirectory->getAbsolutePath($path) . $image;

        if (! $this->mediaDirectory->isFile($path . $image)) {
            /** @var \Magento\Framework\Image\Adapter\Gd2 $imageObject */
            $imageObject = $this->imageFactory->create();
            $imageObject->open($absolutePath);

            $originalWidth =  $imageObject->getOriginalWidth();
            $originalHeight = $imageObject->getOriginalHeight();

            if (null === $containerWidth) {
                $containerWidth = round($containerHeight * ($originalWidth / $originalHeight));
            }

            if (null === $containerHeight) {
                $containerHeight = round($containerWidth * ($originalHeight / $originalWidth));
            }

            if ($needCrop) {
                $sizes = $this->getFillImageSize(
                    $containerWidth,
                    $containerHeight,
                    $originalWidth,
                    $originalHeight
                );

                $fillImageWidth = $sizes['width'];
                $fillImageHeight = $sizes['height'];

                $crop = $this->getFillImageCrop($containerWidth, $containerHeight, $fillImageWidth, $fillImageHeight);

                $cropTop = $crop['top'];
                $cropLeft = $crop['left'];
                $cropRight = $crop['right'];
                $cropBottom = $crop['bottom'];
            }

            $imageObject->constrainOnly(true);
            $imageObject->keepTransparency(true);
            $imageObject->backgroundColor([255, 255, 255]);
            $imageObject->keepFrame(true);
            $imageObject->keepAspectRatio(true);
            $imageObject->resize(
                $needCrop ? $fillImageWidth : $containerWidth,
                $needCrop ? $fillImageHeight : $containerHeight
            );

            if ($needCrop && ($cropTop || $cropLeft || $cropRight || $cropBottom)) {
                $imageObject->crop($cropTop, $cropLeft, $cropRight, $cropBottom);
            }

            $imageObject->save($imageResized);
        } elseif (! $containerWidth || ! $containerHeight) {
            list($containerWidth, $containerHeight) = $this->getSizeFromImage($imageResized);
        }

        $imageUrl = $this->storeManager
            ->getStore()
            ->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $path . $image;

        return [
            'url'    => $imageUrl,
            'width'  => $containerWidth,
            'height' => $containerHeight
        ];
    }

    /**
     * @param int $containerWidth
     * @param int $containerHeight
     * @param int $originWidth
     * @param int $originHeight
     * @return int[]
     */
    public function getFillImageSize($containerWidth, $containerHeight, $originWidth, $originHeight)
    {
        $coefficient = max(
            $containerHeight / $originHeight,
            $containerWidth / $originWidth
        );

        return [
            'width' => (int) round($originWidth * $coefficient),
            'height' => (int) round($originHeight * $coefficient),
        ];
    }

    /**
     * @param $containerWidth
     * @param $containerHeight
     * @param $fillImageWidth
     * @param $fillImageHeight
     * @return array
     */
    public function getFillImageCrop($containerWidth, $containerHeight, $fillImageWidth, $fillImageHeight)
    {
        $result = [
            'top' => 0,
            'left' => 0,
            'right' => 0,
            'bottom' => 0,
        ];

        if ($fillImageWidth > $containerWidth) {
            $cropWidth = ($fillImageWidth - $containerWidth) / 2;

            $result['left'] = $result['right'] = (int) $cropWidth;
        }

        if ($fillImageHeight > $containerHeight) {
            $cropHeight = ($fillImageHeight - $containerHeight) / 2;

            $result['top'] = $result['bottom'] = (int) $cropHeight;
        }

        return $result;
    }

    /**
     * @param $pathToImage
     * @return array
     */
    private function getSizeFromImage($pathToImage)
    {
        /** @var \Magento\Framework\Image\Adapter\Gd2 $imageObject */
        $imageObject = $this->imageFactory->create();
        $imageObject->open($pathToImage);

        return [$imageObject->getOriginalWidth(), $imageObject->getOriginalHeight()];
    }
}
