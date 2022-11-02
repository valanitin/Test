<?php

namespace Dynamic\Cmspagemanager\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Dynamic_Cmspagemanager::cmspagemanager_manage');
    }

    /**
     * Cmspagemanager List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Dynamic_Cmspagemanager::cmspagemanager_manage'
        )->addBreadcrumb(
            __('CMS Manager'),
            __('CMS Manager')
        )->addBreadcrumb(
            __('Manage CMS Manager'),
            __('Manage CMS Manager')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('CMS Manager'));
        return $resultPage;
    }
}
