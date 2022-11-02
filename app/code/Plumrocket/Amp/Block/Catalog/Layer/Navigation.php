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
 * @package     Plumrocket_Amp 2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Catalog\Layer;

class Navigation extends \Magento\LayeredNavigation\Block\Navigation
{
    /**
     * Apply layer
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->renderer = $this->getChildBlock('amp.renderer');
        foreach ($this->filterList->getFilters($this->_catalogLayer) as $filter) {
            $filter->apply($this->getRequest());
        }
        $this->getLayer()->apply();
        return \Magento\Framework\View\Element\Template::_prepareLayout();
    }

    /**
     * Get layered navigation state html
     *
     * @return string
     */
    public function getStateHtml()
    {
        return $this->getChildHtml('amp.state');
    }

    /**
     * Get url for 'Clear All' link
     * @return string
     */
    public function getClearUrl()
    {
        return $this->getChildBlock('amp.state')->getClearUrl();
    }

    /**
     * Format requestVar to code
     *
     * @param string $requestVar
     * @return mixed|null|string|string[]
     */
    public function createFilterCode($requestVar)
    {
        return mb_strtolower(trim($requestVar));
    }
}
