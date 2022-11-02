<?php
/**
 * Adminhtml mytickets list block
 *
 */
namespace Dynamic\Mytickets\Block\Adminhtml;

class Mytickets extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_mytickets';
        $this->_blockGroup = 'Dynamic_Mytickets';
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
