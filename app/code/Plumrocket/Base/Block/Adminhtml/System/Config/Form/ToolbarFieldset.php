<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Block\Adminhtml\System\Config\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Toolbar fieldset without any visible elements.
 *
 * @since 2.8.0
 */
class ToolbarFieldset extends \Magento\Config\Block\System\Config\Form\Fieldset
{

    /**
     * Render field group without header and footer
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);
        return $this->_getChildrenElementsHtml($element);
    }
}
