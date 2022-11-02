<?php

namespace Dynamic\Referfriend\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Dynamic_Referfriend::referfriend_manage');
    }

    /**
     * Referfriend List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Dynamic_Referfriend::referfriend_manage'
        )->addBreadcrumb(
            __('Refer Friend Manager'),
            __('Refer Friend Manager')
        )->addBreadcrumb(
            __('Manage Refer Friend Manager'),
            __('Manage Refer Friend Manager')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Refer Friend Manager'));
        return $resultPage;
    }
}
