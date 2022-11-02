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
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Adminhtml\Widget\Slider;

use Magento\Framework\Data\Form\Element\Hidden as Element;

/**
 * Class Js
 *
 * @package Plumrocket\Amp\Block\Adminhtml\Widget\Slider
 */
class Js extends \Magento\Backend\Block\Template
{
    /**
     * @param Element $element
     * @return Element
     */
    public function prepareElementHtml(Element $element)
    {
        $element->setData(
            'after_element_html',
            "<script>require(['pramp'], function (PRAMP) {'use strict'; PRAMP.initSliderConfiguration();});</script>"
        );

        return $element;
    }
}
