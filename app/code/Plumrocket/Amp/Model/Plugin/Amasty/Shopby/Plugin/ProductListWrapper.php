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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Amasty\Shopby\Plugin;

/* Fix conflict with Amasty\Shopby\Plugin\ProductListWrapper */
class ProductListWrapper
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $dataHelper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->dataHelper = $dataHelper;
        $this->objectManager = $objectManager;
    }

    public function afterToHtml(\Magento\Catalog\Block\Product\ListProduct $subject, $result)
    {
        if ($this->dataHelper->isAmpRequest()) {
            return $result;
        }
        // In order to fix clashes, it is needed to disable following plugin: Amasty\Shopby\Plugin\ProductListWrapper
        // We are using objectManager to prevent any errors that might occur,
        // if there is no Amasty\Shopby\Plugin\ProductListWrapper class available
        return $this->objectManager->get('\Amasty\Shopby\Plugin\ProductListWrapper')
            ->afterToHtml($subject, $result);
    }
}
