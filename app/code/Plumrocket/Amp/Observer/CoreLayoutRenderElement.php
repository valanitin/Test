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
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Observer;
use Magento\Framework\Event\ObserverInterface;

class CoreLayoutRenderElement implements ObserverInterface
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Array of elements names that need to disable for output
     * @var array
     */
    protected $_disabledElements = [];

    /**
     * Name prefix for all price blocks
     * @var string
     */
    protected $_priceElementPrefix = 'amp.product.price';

    /**
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry
    ) {
        $this->_dataHelper = $dataHelper;
        $this->_registry = $registry;
    }

    /**
     * @param  \Magento\Framework\Event\Observer
     * @return \Magento\Framework\Event\Observer this object
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /**
         * Checking module status
         */
        if (!$this->_dataHelper->moduleEnabled()){
            return $this;
        }

        /**
         * Get elemnt html and replace it
         */
        if ($this->_dataHelper->isAmpRequest()) {
            $currentElementName = $observer->getElementName();
            $transport = $observer->getTransport();

            /**
             * Remove price if irame exist
             */
            if (strpos($currentElementName, $this->_priceElementPrefix) === 0) {
                $product = $this->_registry->registry('current_product');

                if ($product && $product->isSaleable()
                    && $product->getTypeInstance()->hasOptions($product)
                    && $product->isInStock()
                    && $this->_dataHelper->getIframeSrc($product)
                ) {
                    $transport->setOutput('');
                    return $this;
                }
            }

            /**
             * Disable output for disallowed elements
             */
            if ($this->_disableOutput($currentElementName)) {
                $html = '';
            } else {
                $html = $transport->getOutput();
                /* moved to ResultInterfacePlugin
                $html = $this->_replaceHtml($html);
                */
            }

            /**
             * Set final Html output
             */
            $transport->setOutput($html);
        }

        return $this;
    }

    /**
     * @param  string $elementName
     * @return boolean
     */
    protected function _disableOutput($elementName)
    {
        return in_array($elementName, $this->_disabledElements) ? true : false;
    }

}
