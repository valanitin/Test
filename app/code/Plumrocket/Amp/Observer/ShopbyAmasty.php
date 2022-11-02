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
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Amp\Observer;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Plumrocket\Amp\Helper\Data;

/**
 * @since 2.9.16
 */
class ShopbyAmasty implements ObserverInterface
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Fix for Amasty_Shopby v.2.11.2
     * Do not display widgets in AMP mode
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->dataHelper->isAmpRequest()) {
            $block = $observer->getData('block');
            if ($block instanceof \Amasty\Shopby\Block\Navigation\Widget\WidgetInterface) {
                $transport = $observer->getData('transport');
                $transport->setHtml('');
            }
        }
    }
}

