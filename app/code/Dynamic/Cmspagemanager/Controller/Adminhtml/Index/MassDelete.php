<?php 

namespace Dynamic\Cmspagemanager\Controller\Adminhtml\Index;

class MassDelete extends \Magento\Backend\App\Action { 
    
    public function execute() {
        
        $ids = $this->getRequest()->getParam('cmspagemanager_id');
         
        if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $row = $this->_objectManager->create('Dynamic\Cmspagemanager\Model\Cmspagemanager')->load($id);
                    $row->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been item.', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
}
