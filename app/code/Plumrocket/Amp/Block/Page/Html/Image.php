<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Page\Html;

/**
 * Class Image
 *
 * @method getImageUrl()
 * @method getWidth()
 * @method getHeight()
 * @method getLabel()
 * @method getClassList()
 *
 * @method $this setImageUrl($image)
 * @method $this setWidth($width)
 * @method $this setHeight($height)
 * @method $this setLabel($alt)
 * @method $this setCustomAttributes($attributeString)
 * @method $this setClassList($classList)
 */
class Image extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $imageRendererTemplate; //@codingStandardsIgnoreLine

    /**
     * Create amp-image tag
     *
     * @param       $src
     * @param       $width
     * @param       $height
     * @param null  $alt
     * @param array $classList
     * @return $this
     */
    public function createImage($src, $width, $height, $alt = null, array $classList = [])
    {
        $this->unsetData();

        $template = $this->imageRendererTemplate ?: 'Plumrocket_Amp::catalog/product/image.phtml';

        $this->setImageUrl($src)
            ->setWidth($width)
            ->setHeight($height)
            ->setLabel($alt)
            ->setTemplate($template)
            ->setCustomAttributes('layout="responsive"')
            ->setClassList($classList ? implode(' ', $classList) : '');

        return $this;
    }
}
