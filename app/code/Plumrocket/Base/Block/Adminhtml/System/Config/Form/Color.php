<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

/**
 * @deprecated since 2.3.1
 * @see \Plumrocket\Base\Block\Adminhtml\System\Config\Form\ColorPicker
 */
class Color extends Template implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'Plumrocket_Base::system/config/color.phtml';

    /**
     * @var string
     */
    protected $_pathId;

    /**
     * Render fieldset html
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $this->_pathId = str_replace('__', '_', $element->getId());
        return $this->toHtml();
    }

    /**
     * Receive path id
     *
     * @return string
     */
    public function getPathId()
    {
        return $this->_pathId;
    }
}
