<?php

namespace Dynamic\PriceComparison\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use \Magento\Framework\Controller\ResultFactory;


class Createticket extends Action
{
    protected $resultPageFactory;


    protected $mytickets;


    protected $customerSession;
    protected $_messageManager;
    protected $shippingAddress;



    public function __construct(
        Context $context,
        \Dynamic\Mytickets\Model\Mytickets $mytickets,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\Address $shippingAddress
    )  {
        $this->mytickets    = $mytickets;
        $this->customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        $this->shippingAddress = $shippingAddress;

        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $customerId = 0;
        # $ticket_type = $data['casetype'];
        $ticket_type = 1;
        $customerName = "";
        $customerLastName = "";
        if(isset($data["phone"])){
            $phone = $data["phone"];
        }

        isset($data['remarks'])?$data['remarks']:"";
        if($this->isLoggedIn()) {
            $customerId = $this->customerSession->getCustomer()->getId();
            $customerName = $this->customerSession->getCustomer()->getFirstname();
            $customerLastName = $this->customerSession->getCustomer()->getLastname();
            $data["email"] = $this->customerSession->getCustomer()->getEmail();
            $shippingAddressID =  $this->customerSession->getCustomer()->getDefaultBilling();
            $shippingAddresss = $this->shippingAddress->load($shippingAddressID);
            $shippingTelephone = $shippingAddresss->getTelephone();
            $phone =  $shippingTelephone;
        }
        if($data){
            $this->mytickets->setName($customerName);
            $this->mytickets->setLastName($customerLastName);
            $this->mytickets->setEmail($data['email']);
            if(isset($phone)){
                $this->mytickets->setPhone($phone);
            }
            $this->mytickets->setRemarks($data['remarks']);
            $this->mytickets->setCustomerId($customerId);
            $this->mytickets->setTicketType($ticket_type);
            try {
                $this->mytickets->save();
                if (empty($data)) {
                    $this->_messageManager->addErrorMessage('error');
                } else {
                    $this->_messageManager->addSuccessMessage('Request Ticket Sent, You will get Ticket ID shortly.');
                }
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                return $resultRedirect;

            } catch (\Exception $e) {

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
