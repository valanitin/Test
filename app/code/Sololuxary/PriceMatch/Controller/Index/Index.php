<?php

declare(strict_types=1);

namespace Sololuxary\PriceMatch\Controller\Index;

use Dynamic\Mytickets\Model\Mytickets;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * class Index
 */
class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var Mytickets
     */
    protected $myTickets;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Mytickets $myTickets
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Mytickets $myTickets,
        Session $customerSession
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->myTickets = $myTickets;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $customerId = 0;
        $ticket_type = 1;
        if ($this->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
        }
        $data['customer_id'] = $customerId;
        $data['ticket_type'] = $ticket_type;
        $response = [];

        if ($data) {

            $this->myTickets->setData($data);
            try {
                $this->myTickets->save();
                $response = [
                    'errors' => false,
                    'message' => __('<p>Request Ticket Sent, You will get Ticket ID shortly.</p>')
                ];

            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            }
        }
        $page = $this->_resultPageFactory->create();

        $block = $page->getLayout()->getBlock('tickets-for-pricematch');
        $block->setData('response', $response);

        return $page;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        if ($this->customerSession->isLoggedIn()) {
            return true;
        } else {
            return false;
        }
    }
}
