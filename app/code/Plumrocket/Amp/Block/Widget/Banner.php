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
 * @method string|null getBannerUrl()
 * @method string|null getImageWidth()
 * @method string|null getImageHeight()
 * @method string|null getImageAlt()
 */
class Banner extends \Magento\Framework\View\Element\Template implements
    \Magento\Widget\Block\BlockInterface
{
    /**
     * @var null|\Plumrocket\Amp\Block\Page\Html\Image
     */
    private $image = null;

    /**
     * Banner constructor.
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
     * Create image url from path
     *
     * @return mixed|string
     */
    public function getImageUrl()
    {
        return $this->_storeManager
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $this->getData('image');
    }

    /**
     * Retrieve <amp-img tag
     *
     * @return string
     */
    public function getImageHtml()
    {
        $this->image->createImage(
            $this->getImageUrl(),
            $this->getImageWidth(),
            $this->getImageHeight(),
            $this->getImageAlt()
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
            $this->setTemplate('Plumrocket_Amp::widget/banner.phtml');
        }

        return parent::_beforeToHtml();
    }
}
