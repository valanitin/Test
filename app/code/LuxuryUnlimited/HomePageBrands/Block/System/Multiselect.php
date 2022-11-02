<?php

declare(strict_types=1);

namespace LuxuryUnlimited\HomePageBrands\Block\System;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Multiselect
 */
class Multiselect extends Field
{
    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        // @codingStandardsIgnoreLine
        return parent::_getElementHtml($element) . "
        <script>
            require([
                'jquery',
                'chosen'
            ], function ($, chosen) {
                $('#" . $element->getId() . "').chosen({
                    width: '100%',
                    placeholder_text: '" . __('Select Options') . "'
                });
            })
        </script>";
    }
}
