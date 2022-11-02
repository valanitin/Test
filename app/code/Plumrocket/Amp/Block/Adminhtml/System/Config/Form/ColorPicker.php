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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Adminhtml\System\Config\Form;

use Magento\Config\Block\System\Config\Form\Field;

class ColorPicker extends Field
{
    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) // @codingStandardsIgnoreLine
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
            require(["jquery", "jquery/colorpicker/js/colorpicker", "domReady"], function ($) {
                var element = $("#'.$element->getHtmlId().'");
                element.css("background-color", "' . $value . '");

                element.ColorPicker({
                    layout:"hex",
                    onChange: function (hsb, hex, rgb) {
                        var colorHash = "#" + hex;
                        element.css("background-color", colorHash);
                        element.val(colorHash);
                    }
                }).keyup(function (event) {
                    var value = element.val();
                    element.css("background-color", value);
                    element.val(value);
                });
            });
            </script>';

        return $html;
    }
}
