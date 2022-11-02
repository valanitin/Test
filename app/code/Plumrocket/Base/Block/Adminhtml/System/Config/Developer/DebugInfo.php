<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Block\Adminhtml\System\Config\Developer;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * @since 2.3.0
 */
class DebugInfo extends Field
{
    /**
     * Customize button.
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $element->setData('value', __('Download Technical Report'));
        $element->setData('class', 'action-default');
        $element->setData('onclick', "window.location.assign('{$this->getActionUrl()}')");
        return parent::_getElementHtml($element);
    }

    /**
     * Get download url.
     *
     * @return string
     */
    public function getActionUrl(): string
    {
        return $this->_urlBuilder->getUrl('plumbase/debug/download');
    }

    /**
     * Disable scope label.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _renderScopeLabel(AbstractElement $element): string
    {
        return '';
    }
}
