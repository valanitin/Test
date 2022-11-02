<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxsuite\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Color
 *
 * @package Tigren\Ajaxsuite\Block
 */
class Color extends Field
{
    /**
     * Color constructor.
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    /**
     * @param  AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $value = $element->getData('value');

        $html .= '<script type="text/javascript">
                require(["jquery","jquery/colorpicker/js/colorpicker"], function ($) {
                    $(document).ready(function () {
                        var $el = $("#' . $element->getHtmlId() . '");
                        $el.css("backgroundColor", "' . $value . '");

                        // Attach the color picker
                        $el.ColorPicker({
                            color: "' . $value . '",
                            onChange: function (hsb, hex, rgb) {
                                $el.css("backgroundColor", "#" + hex).val("#" + hex);
                            }
                        });
                    });
                });
                </script>';
        return $html;
    }
}
