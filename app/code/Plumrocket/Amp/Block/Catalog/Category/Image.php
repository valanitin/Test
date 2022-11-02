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

namespace Plumrocket\Amp\Block\Catalog\Category;

class Image extends \Plumrocket\Amp\Block\Page\Html\Image
{
    /**
     * @var \Plumrocket\Amp\Model\Image\Compressor
     */
    private $imageCompressor;

    /**
     * Provided from layout
     *
     * @var int
     */
    private $imageWidth;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Image constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Amp\Model\Image\Compressor           $imageCompressor
     * @param string                                           $imageRendererTemplate
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Amp\Model\Image\Compressor $imageCompressor,
        \Magento\Framework\Registry $registry,
        $imageRendererTemplate = 'Plumrocket_Amp::html/image.phtml',
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->imageCompressor = $imageCompressor;
        $this->imageRendererTemplate = $imageRendererTemplate;
        $this->imageWidth = $data['width'] ?? 500;
        $this->coreRegistry = $registry;
    }

    /**
     * @return bool|Image
     */
    public function createCategoryImage()
    {
        $category = $this->getCurrentCategory();
        $imageInfo = $this->imageCompressor->getCategoryImage($category->getImage(), $this->imageWidth);

        if (! $imageInfo) {
            return false;
        }

        return $this->createImage($imageInfo['url'], $imageInfo['width'], $imageInfo['height'], $category->getName());
    }

    /**
     * Retrieve current category model object
     *
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        if (! $this->hasData('current_category')) {
            $this->setData('current_category', $this->coreRegistry->registry('current_category'));
        }

        return $this->getData('current_category');
    }
}
