<?php
/**
 * Customer attribute index controller
 */

namespace IWD\Opc\Controller\Adminhtml\Attribute;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory) 
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        
    }
    
    /*
     * Index action.
     * 
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /* @var Magento\Backend\Model\View\Result\Page $resultPage*/
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('IWD_Opc::customer_attributes');
        $resultPage->addBreadcrumb(__('Customer Attribute'), __('Opc'));
        $resultPage->addBreadcrumb(__('Manage Customer Attribute'), __('Customer Attributes'));
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Attributes'));
        return $resultPage;
    }
    
    /*
     * Is user allowed to view manage customer attribute grid.
     * 
     * @return bool
     */
    protected function _isAllowed() 
    {
        return $this->_authorization->isAllowed('IWD_Opc::customer_attributes');
    }
}