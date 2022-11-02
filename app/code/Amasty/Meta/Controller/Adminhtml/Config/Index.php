<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Controller\Adminhtml\Config;

class Index extends \Amasty\Meta\Controller\Adminhtml\Config
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend($this->_title);
        $this->_setActiveMenu('cms/amseotoolkit/ammeta');
        $block =  $this->_view->getLayout()->createBlock(
            \Amasty\Meta\Block\Adminhtml\Config::class,
            '',
            [
                'data' =>
                    [
                        'is_custom' => $this->_isCustom,
                        'title' => $this->_title
                    ]
            ]
        );
        $this->_addContent($block);
        $this->_view->renderLayout();
    }
}
