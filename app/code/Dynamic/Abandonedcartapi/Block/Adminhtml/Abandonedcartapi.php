<?php
/**
 * Adminhtml abandonedcartapi list block
 *
 */
namespace Dynamic\Abandonedcartapi\Block\Adminhtml;

class Abandonedcartapi extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_abandonedcartapi';
        $this->_blockGroup = 'Dynamic_Abandonedcartapi';
        $this->_headerText = __('Abandoned Cart Manager');
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
