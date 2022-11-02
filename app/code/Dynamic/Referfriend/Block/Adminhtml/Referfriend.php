<?php
/**
 * Adminhtml referfriend list block
 *
 */
namespace Dynamic\Referfriend\Block\Adminhtml;

class Referfriend extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_referfriend';
        $this->_blockGroup = 'Dynamic_Referfriend';
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
