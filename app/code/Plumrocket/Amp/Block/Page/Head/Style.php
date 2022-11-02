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

namespace Plumrocket\Amp\Block\Page\Head;

class Style extends \Magento\Framework\View\Element\Template
{
    /**
     * @deprecated since 2.6.0 - use getAmpDataHelper() instead
     * @var \Plumrocket\Amp\Helper\Data
     */
    public $_dataHelper; // @codingStandardsIgnoreLine

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * @return \Plumrocket\Amp\Helper\Data
     */
    public function getAmpDataHelper() : \Plumrocket\Amp\Helper\Data
    {
        return $this->dataHelper;
    }

    /**
     * Retrieve view url without cdn url
     * @param  string $file
     * @param  array  $params
     * @return string
     */
    public function getAmpSkinUrl($file = null, array $params = [])
    {
        $url = $this->getViewFileUrl($file, $params);
        $fontInfo = parse_url($url);
        $baseInfo = parse_url($this->getUrl());
        $url = str_replace($fontInfo['host'], $baseInfo['host'], $url);

        return $url;
    }

    /**
     * Retrieve minified css
     * @return string
     */
    protected function _toHtml() // @codingStandardsIgnoreLine
    {
        $html = parent::_toHtml();

        if ($html) {
            $html = $this->minimizeCSS($html);
        }

        return $html;
    }

    /**
     * @param string $css
     * @return string
     */
    private function minimizeCSS($css)
    {
        $css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
        $css = preg_replace('/\s{2,}/', ' ', $css);
        $css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
        $css = preg_replace('/;}/', '}', $css);
        return $css;
    }

    /**
     * Get max-width and max-height for category image
     * @return array
     */
    public function getCategoryImageSize()
    {
        $categoryImageBlock = $this->getLayout()->getBlock('amp.category.image');

        return [
            'max-width' => $categoryImageBlock ? $categoryImageBlock->getWidth() : 'none',
            'max-height' => $categoryImageBlock ? $categoryImageBlock->getHeight() : 'none',
        ];
    }

    /**
     * Retrieve setting parameter from helper
     * @param void
     * @return boolean
     */
    public function isRtlEnabled()
    {
        return $this->dataHelper->getRtlEnabled();
    }
}
