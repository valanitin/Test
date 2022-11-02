<?php
/**
 * Mage Tracer.
 *
 * @category  Magetracer
 * @package   Magetracer_StoreOptimization
 * @author    Magetracer
 * @copyright Copyright (c) Mage Tracer (https://www.magetracer.com)
 * @license   https://www.magetracer.com/license.html
 */

namespace Magetracer\StoreOptimization\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magetracer\StoreOptimization\Helper\Data;

class DeferLoadJs implements ObserverInterface
{
    protected $_helper;

    public function __construct(
        Data $helper
    ) {
        $this->_helper = $helper;
    }

    /**
     * add script tags in the bottom of the page
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getRequest()->isXmlHttpRequest()) {
            return;
        }
        
        if ($this->_helper->getIsDeferLoadingEnable()) {
            $response = $observer->getEvent()->getData('response');

            if (!$response) {
                return;
            }
            $html = $response->getBody();
            if ($html == '') {
                return;
            }

            $findScriptPattern = '@(?:<script type="text/javascript"|<script)(.*)</script>@msU';
            preg_match_all($findScriptPattern, $html, $matches);
            $combinedJs = implode('', $matches[0]);
            $html = preg_replace($findScriptPattern, '', $html);
            $html .= $combinedJs;

            $response->setBody($html);
        }
    }
}
