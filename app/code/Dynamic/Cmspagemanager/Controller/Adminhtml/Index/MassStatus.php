<?php

namespace Dynamic\Cmspagemanager\Controller\Adminhtml\Index;

class MassStatus extends \Magento\Backend\App\Action {
     
    public function execute() {
        
        $ids = $this->getRequest()->getParam('cmspagemanager_id');
        
        $status = $this->getRequest()->getParam('status');

        if (!is_array($ids) || empty($ids)) {
            $this->messageManager->addError(__('Please select item(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $row = $this->_objectManager->create('Dynamic\Cmspagemanager\Model\Cmspagemanager')->load($id);
                    $row->setData('status',$status)->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 item(s) have been updated.', count($ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
         $this->_redirect('*/*/');
    }
}
