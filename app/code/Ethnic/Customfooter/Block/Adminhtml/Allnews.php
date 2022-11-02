<?php
namespace Ethnic\Customfooter\Block\Adminhtml;

class Allnews extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_allnews';
        $this->_blockGroup = 'Ethnic_Customfooter';
        $this->_headerText = __('Manage News');

        parent::_construct();

        if ($this->_isAllowedAction('Ethnic_Customfooter::save')) {
            $this->buttonList->update('add', 'label', __('Add News'));
        } else {
            $this->buttonList->remove('add');
        }
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
?>
