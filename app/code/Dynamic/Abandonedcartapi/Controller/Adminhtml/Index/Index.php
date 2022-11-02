<?php

namespace Dynamic\Abandonedcartapi\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
	
    /**
     * Check the permission to run it
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Dynamic_Abandonedcartapi::abandonedcartapi_manage');
    }

    /**
     * Abandonedcartapi List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Dynamic_Abandonedcartapi::abandonedcartapi_manage'
        )->addBreadcrumb(
            __('Abandoned Cart Manager'),
            __('Abandoned Cart Manager')
        )->addBreadcrumb(
            __('Manage Abandoned Cart Manager'),
            __('Manage Abandoned Cart Manager')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Abandoned Cart Manager'));
        return $resultPage;
    }
}
