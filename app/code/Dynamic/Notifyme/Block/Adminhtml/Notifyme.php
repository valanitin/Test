<?php
/**
 * Adminhtml notifyme list block
 *
 */
namespace Dynamic\Notifyme\Block\Adminhtml;

class Notifyme extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_notifyme';
        $this->_blockGroup = 'Dynamic_Notifyme';
        $this->_headerText = __('Notifyme Manager');
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
