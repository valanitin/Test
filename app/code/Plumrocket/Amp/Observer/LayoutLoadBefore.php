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
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Observer;

use Magento\Framework\Event\ObserverInterface;

class LayoutLoadBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $_response;

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Magento\Framework\App\Response\Http $response,
        \Plumrocket\Amp\Helper\Data $dataHelper
    ) {
        $this->_response = $response;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_dataHelper->moduleEnabled()) {
            return;
        }

        /**
         * Get full action name and update object
         */
        $currentFullAction = $this->_dataHelper->getFullActionName();
        /**
         * @var \Magento\Framework\View\Layout\ProcessorInterface $update
         */
        $update = $observer->getEvent()->getLayout()->getUpdate();

        if ($this->_dataHelper->isOnlyOptionsRequest()) {
            $update->addHandle('amp_catalog_product_view_only_options');
            return true;
        }

        /**
         * Check get parameter amp
         */
        if ($this->_dataHelper->isAmpRequest()) {
            /**
             *  Add layout handlers
             */
            foreach ($update->getHandles() as $handleName) {
                $update->addHandle('amp_' . $handleName);
            }
        }

        /**
         * Add layout changes
         */
        if ($this->_dataHelper->isAllowedPage()) {
            $update->addHandle('amp_non_amp_page');
        }
    }
}
