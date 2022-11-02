<?php

namespace Meetanshi\SavedCards\Controller\Adminhtml\Savedcards;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magento\Sales\Model\OrderFactory;

/**
 * Class Index
 * @package Meetanshi\SavedCards\Controller\Adminhtml\Savedcards
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var OrderFactory
     */
    protected $orderFactory;
    /**
     * @var Session
     */
    protected $backendSession;

    /**
     * Index constructor.
     * @param Registry $registry
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param Session $backendSession
     * @param OrderFactory $orderFactory
     */
    public function __construct(Registry $registry, Context $context, ForwardFactory $resultForwardFactory, PageFactory $resultPageFactory, Session $backendSession, OrderFactory $orderFactory)
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->context = $context;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->backendSession = $backendSession;
        $this->orderFactory = $orderFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($orderId) {
            $order = $this->orderFactory->create()->load($orderId);
            $payment = $order->getPayment();
            $payment->getAdditionalInformation();

            $payment->unsAdditionalInformation('cc_cvv');
            $payment->unsAdditionalInformation('card_number');
            $payment->save();
            $order->save();
            $this->messageManager->addSuccessMessage(__('You have successfully wiped credit card information.'));

        }
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
