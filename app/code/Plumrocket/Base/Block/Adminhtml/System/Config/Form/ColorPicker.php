<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Color picker for system settings
 *
 * For using you have to load Plumrocket_Base::css/lib/pickr/monolith.min.css on the page
 *
 * @since 2.3.1
 * @deprecated since 2.5.4
 * @see \Plumrocket\ExtendedAdminUi\Block\Adminhtml\System\Config\Form\ColorPicker
 */
class ColorPicker extends Field
{
    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->addClass('pr-color-picker-value');

        $pickerPlaceholderId = $element->getHtmlId() . '_color_picker';
        $pickerContainerHtml = '<div id="' . $pickerPlaceholderId . '"></div>' ;

        /** @var Template $pickerConfigsBlock */
        $pickerConfigsBlock = $this->getLayout()->createBlock(Template::class);
        $pickerConfigsBlock
            ->setTemplate('Plumrocket_Base::system/config/color_picker.phtml')
            ->setData('elementId', $element->getHtmlId())
            ->setData('pickerId', $pickerPlaceholderId)
            ->setData('currentValue', $element->getEscapedValue())
            ->setData('disabled', $element->getData('disabled'));

        return "{$pickerContainerHtml}{$element->getElementHtml()}{$pickerConfigsBlock->toHtml()}";
    }

    /**
     * Decorate field row html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param string $html
     * @return string
     */
    protected function _decorateRowHtml(AbstractElement $element, $html)
    {
        return '<tr id="row_' . $element->getHtmlId() . '" class="pr-color-picker-field">' . $html . '</tr>';
    }
}
