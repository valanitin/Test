<?php

namespace Dynamic\Notifyme\Controller\Adminhtml\Index;

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
        return $this->_authorization->isAllowed('Dynamic_Notifyme::notifyme_manage');
    }

    /**
     * Notifyme List action
     *
     * @return void
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
            'Dynamic_Notifyme::notifyme_manage'
        )->addBreadcrumb(
            __('Notifyme Manager'),
            __('Notifyme Manager')
        )->addBreadcrumb(
            __('Manage Notifyme Manager'),
            __('Manage Notifyme Manager')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Notifyme Manager'));
        return $resultPage;
    }
}
