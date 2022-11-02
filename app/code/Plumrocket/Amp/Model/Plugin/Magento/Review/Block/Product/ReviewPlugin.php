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

namespace Plumrocket\Amp\Model\Plugin\Magento\Review\Block\Product;

/**
 * Plugin for
 */
class ReviewPlugin
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * ReviewPlugin constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Fix for RTL mode
     *
     * @param \Magento\Review\Block\Product\Review $subject
     * @param                                      $result
     * @return mixed
     */
    public function afterSetTabTitle(
        \Magento\Review\Block\Product\Review $subject,
        $result
    ) {
        if ($this->dataHelper->isAmpRequest() && $this->dataHelper->getRtlEnabled()) {
            $title = $subject->getTitle();
            if (false !== strpos($title, '</span>')) {
                $subject->setTitle(str_replace('</span>', '</span>&#x200E', $title));
            }
        }

        return $result;
    }
}
