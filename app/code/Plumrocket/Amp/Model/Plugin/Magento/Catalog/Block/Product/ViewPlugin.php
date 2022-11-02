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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Magento\Catalog\Block\Product;

use Plumrocket\Amp\Helper\Data as DataHelper;
use Plumrocket\Amp\Block\Review\Product\ReviewRenderer as ReviewRenderer;

/**
 * Plugin for
 */
class ViewPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Catalog\Block\Product\ReviewRendererInterface
     */
    protected $_reviewRenderer;

    /**
     * @param DataHelper $dataHelper
     * @param ReviewRenderer $reviewRenderer
     */
    public function __construct(
        DataHelper $dataHelper,
        ReviewRenderer $reviewRenderer
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_reviewRenderer = $reviewRenderer;
    }

    /**
     * Overriding review summary output
     *
     * @param \Magento\Catalog\Block\Product\View $subject
     * @param \Closure                            $proceed
     * @param \Magento\Catalog\Model\Product      $product
     * @param bool                                $templateType
     * @param bool                                $displayIfNoReviews
     * @return mixed|string
     */
    public function aroundGetReviewsSummaryHtml(
        \Magento\Catalog\Block\Product\View $subject,
        \Closure $proceed,
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        /**
         * (Only for amp request)
         */
        if ($this->_dataHelper->isAmpRequest()) {
            return $this->_reviewRenderer->getReviewsSummaryHtml(
                $product,
                $templateType,
                $displayIfNoReviews
            );
        }

        /**
        * Get result by original method
        */
        return $proceed($product, $templateType, $displayIfNoReviews);
    }
}
