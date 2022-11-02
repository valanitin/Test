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

namespace Plumrocket\Amp\Block\Widget;

/**
 * Class Banner
 *
 * @method string|null getImageWidth()
 * @method string|null getImageHeight()
 * @method string|null getBannerUrl()
 */
class Slider extends \Magento\Framework\View\Element\Template implements
    \Magento\Widget\Block\BlockInterface
{
    const MAX_SLIDE_COUNT = 5;

    /**
     * @var null|\Plumrocket\Amp\Block\Page\Html\Image
     */
    private $image = null;

    /**
     * Enabled slides
     *
     * @var array|null
     */
    private $slides = null;

    /**
     * Slider constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Amp\Block\Page\Html\Image            $image
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Amp\Block\Page\Html\Image $image,
        array $data = []
    ) {
        $this->image = $image;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve array of slides
     *
     * @return array[]
     */
    public function getSlides()
    {
        if (null === $this->slides) {
            $this->slides = [];
            for ($i = 1; $i <= self::MAX_SLIDE_COUNT; $i++) {
                $enabled = $this->getData('show_slide' . $i);
                if (null === $enabled) {
                    break;
                } elseif (!$enabled) {
                    continue;
                } else {
                    $this->slides[$i] = [
                        'image_path' => $this->getData('image' . $i),
                        'image_alt'  => $this->getData('image_alt' . $i),
                        'banner_url' => $this->getData('banner_url' . $i),
                        'url_blank'  => $this->getData('url_blank' . $i),
                    ];
                }
            }
        }

        return $this->slides;
    }

    /**
     * Make sure at least one slide is enabled
     *
     * @return bool
     */
    public function hasEnabledSlides()
    {
        return (bool)count($this->getSlides());
    }

    /**
     * Create url from path
     *
     * @param string $path
     * @return mixed|string
     */
    public function getImageUrl($path)
    {
        return $this->_storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;
    }

    /**
     * Retrieve <amp-img tag
     *
     * @param array $slideInfo
     * @return string
     */
    public function getImageHtml(array $slideInfo)
    {
        $this->image->createImage(
            $this->getImageUrl($slideInfo['image_path']),
            $this->getImageWidth(),
            $this->getImageHeight(),
            $slideInfo['image_alt']
        );
        return $this->image->toHtml();
    }

    /**
     * Set default template for widget in cms page
     *
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _beforeToHtml()
    {
        if (! $this->getTemplate()) {
            $this->setTemplate('Plumrocket_Amp::widget/banner/slider.phtml');
        }

        return parent::_beforeToHtml();
    }
}
