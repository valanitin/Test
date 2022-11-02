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
 * Class Video
 *
 * @method getVideoWidth()
 * @method getVideoHeight()
 */
class Video extends \Magento\Framework\View\Element\Template implements
    \Magento\Widget\Block\BlockInterface
{
    /**
     * @var null|\Plumrocket\Amp\Helper\Video
     */
    private $videoHelper = null;

    /**
     * Array of properties
     *
     * @var null|array
     */
    private $videoData = null;

    /**
     * Video constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Amp\Helper\Video $videoHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->videoHelper = $videoHelper;
    }

    /**
     * Detect video type and other specification
     *
     * @return $this
     */
    public function parseVideoUrl()
    {
        if (null === $this->videoData) {
            $this->videoData = $this->videoHelper->getVideoData($this->getVideoUrl(), true);
        }

        return $this;
    }

    /**
     * Check if video can be rendered
     *
     * @return bool
     */
    public function isValidVideoData()
    {
        $this->parseVideoUrl();
        if ($this->videoData && ($this->getVideoId() || $this->getVideoSrc())) {
            return true;
        }

        return false;
    }

    /**
     * @return null|string
     */
    public function getVideoType()
    {
        return $this->getVideoData('type');
    }

    /**
     * @return null|string
     */
    public function getVideoId()
    {
        return $this->getVideoData('id');
    }

    /**
     * @return null|string
     */
    public function getVideoSrc()
    {
        return $this->getVideoData('src');
    }

    /**
     * @return null|bool
     */
    public function getVideoAutoPlay()
    {
        return $this->getVideoData('autoplay');
    }

    /**
     * @param $param
     * @return null|string|bool
     */
    private function getVideoData($param)
    {
        if (isset($this->videoData[$param])) {
            return $this->videoData[$param];
        }

        return null;
    }

    /**
     * Parse url before render for getters
     * Set default template for widget in cms page
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        if (! $this->getTemplate()) {
            $this->setTemplate('Plumrocket_Amp::widget/video.phtml');
        }

        $this->parseVideoUrl();
        return parent::_beforeToHtml();
    }

    /**
     * Validate video data before render
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->isValidVideoData()) {
            return '';
        }

        return parent::_toHtml();
    }
}
