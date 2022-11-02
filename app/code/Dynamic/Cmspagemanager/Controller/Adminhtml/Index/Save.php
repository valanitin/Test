<?php

namespace Dynamic\Cmspagemanager\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

class Save extends \Magento\Backend\App\Action
{
    protected $dataProcessor;

    /**
     * @var \Dynamic\Cmspagemanager\Model\Cmspagemanager;
     */
    protected $modelCmspagemanager;


    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     */
    public function __construct(
        Action\Context $context, 
        PostDataProcessor $dataProcessor,
        \Dynamic\Cmspagemanager\Model\Cmspagemanager $modelCmspagemanager
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->modelCmspagemanager = $modelCmspagemanager;
        parent::__construct($context);
        
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dynamic_Cmspagemanager::save');
    }

    /**
     * Save action
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        if ($data) {

            $id = $this->getRequest()->getParam('cmspagemanager_id');

            if(isset($data['cmspage'])) {
                $data['cms_text'] = $this->prepareCmsData($data['cmspage']);
            }
            
            if ($id) {
                $this->modelCmspagemanager->load($id);
            }
            $this->modelCmspagemanager->setData($data);
            try {
                $this->modelCmspagemanager->save();

                $this->messageManager->addSuccess(__('The Item has been Saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('cmspagemanager_id' => $this->modelCmspagemanager->getId(), '_current' => true));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Item.'));
            }

            $this->_getSession()->setFormData($data);
            $this->_redirect('*/*/edit', array('cmspagemanager_id' => $this->getRequest()->getParam('cmspagemanager_id')));
            return;
        }
        $this->_redirect('*/*/');
    }

    public function prepareCmsData($data){

        $arrCmsData = [];
        
        foreach ($data as $code => $values) {
            $cmsCounter = 0;
            foreach($values as $key => $value){
                $arrCmsData[$cmsCounter][$code] = $value;
                $cmsCounter++;
            }
        }

        return json_encode($arrCmsData);
    }
}

