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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Adminhtml\Widget;

use Plumrocket\Amp\Block\Catalog\Product\Widget\RecentlyViewed;

class Notice extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Notice constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $coreRegistry
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
    }

    public function canShow()
    {
        $this->getBaseUrl();
        /** @var \Magento\Widget\Model\Widget\Instance $widget */
        $widget = $this->coreRegistry->registry('current_widget_instance');

        return RecentlyViewed::class === $widget->getInstanceType();
    }
}
