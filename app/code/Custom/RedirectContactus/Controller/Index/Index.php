<?php

namespace Custom\RedirectContactus\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;

class Index extends Action
{
    protected $resultPageFactory;


    protected $mytickets;


    protected $customerSession;

    public function __construct(
        Context $context,
        \Dynamic\Mytickets\Model\Mytickets $mytickets,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->mytickets    = $mytickets;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $customerId = 0;
        $ticket_type = 1;
        if ($this->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
        }
        if($data){
            $this->mytickets->setName($data['name']);
            $this->mytickets->setLastName($data['surname']);
            $this->mytickets->setEmail($data['email']);
            $this->mytickets->setRemarks($data['comment']);
            $this->mytickets->setCustomerId($customerId);
            $this->mytickets->setTicketType($ticket_type);
            try {
                $this->mytickets->save();
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
        return $this->resultPageFactory->create();
    }

    public function isLoggedIn()
    {
        if (empty($this->customerSession)) {
            return false;
        }
        if ($this->customerSession->isLoggedIn()) {
            return true;
        }
    }
}
