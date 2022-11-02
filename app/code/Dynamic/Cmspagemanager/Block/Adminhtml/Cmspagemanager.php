<?php
/**
 * Adminhtml cmspagemanager list block
 *
 */
namespace Dynamic\Cmspagemanager\Block\Adminhtml;

class Cmspagemanager extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_cmspagemanager';
        $this->_blockGroup = 'Dynamic_Cmspagemanager';
        $this->_headerText = __('CMS Manager');
        $this->_addButtonLabel = __('Add New Item');
        parent::_construct();
        if ($this->_isAllowedAction('Dynamic_Cmspagemanager::save')) {
            $this->buttonList->update('add', 'label', __('Add New Item'));
        } else {
            $this->buttonList->remove('add');
        }
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
