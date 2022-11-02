<?php

namespace Custom\RedirectContactus\Controller\Index;

use Dynamic\Mytickets\Model\Mytickets;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;

class Success extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Mytickets
     */
    protected $mytickets;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param Mytickets $mytickets
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        Mytickets $mytickets,
        PageFactory $resultPageFactory,
        Session $customerSession
    ) {
        $this->mytickets    = $mytickets;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->getLayout()->getBlock('redirectcontactus.success');
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

}
