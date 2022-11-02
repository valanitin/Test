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
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\WeltPixel\Quickview\Plugin;

class BlockProductListPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * BlockProductListPlugin constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Plumrocket\Amp\Helper\Data  $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Remove WeltPixel Quickview button
     *
     * @param \WeltPixel\Quickview\Plugin\BlockProductList $subject
     * @param                                              $result
     * @return string|string[]|null
     */
    public function afterAroundGetProductDetailsHtml(
        \WeltPixel\Quickview\Plugin\BlockProductList $subject,
        $result
    ) {
        $isWpQuickviewEnabled = $this->scopeConfig->getValue(
            'weltpixel_quickview/general/enable_product_listing',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $isAmpEnabled = $this->dataHelper->isAmpRequest();

        if ($isWpQuickviewEnabled && $isAmpEnabled) {
            $result = preg_replace('#<a.*class="weltpixel-quickview.*>.*<\/a>#isU', '', $result);
            return $result;
        }

        return $result;
    }
}
