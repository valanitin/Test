<?php
/**
 * Adminhtml orderreturn list block
 *
 */
namespace Dynamic\Orderreturn\Block\Adminhtml;

class Orderreturn extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_orderreturn';
        $this->_blockGroup = 'Dynamic_Orderreturn';
        $this->_headerText = __('Homepage Manager');
        $this->_addButtonLabel = __('Add New Item');
        $this->buttonList->remove('add');
    }
    
    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
