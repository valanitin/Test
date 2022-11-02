<?php

namespace Dynamic\Mytickets\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Dynamic_Mytickets::mytickets_manage');
    }

    /**
     * Mytickets List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Dynamic_Mytickets::mytickets_manage'
        )->addBreadcrumb(
            __('Ticket Manager'),
            __('Ticket Manager')
        )->addBreadcrumb(
            __('Manage Ticket Manager'),
            __('Manage Ticket Manager')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Ticket Manager'));
        return $resultPage;
    }
}
