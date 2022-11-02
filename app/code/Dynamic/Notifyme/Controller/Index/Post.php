<?php

namespace Dynamic\Notifyme\Controller\Index;

class Post extends \Magento\Framework\App\Action\Action
{   
    /**
     * Store manager
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Notifyme
     *
     * @var \Dynamic\Notifyme\Model\Notifyme
     */
    protected $notifyme;

    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    protected $mytickets;

    protected $shippingAddress;


    /**
     * Http
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $http;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Dynamic\Notifyme\Model\Notifyme $notifyme
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $http
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Address $shippingAddress,
        \Dynamic\Mytickets\Model\Mytickets $mytickets,
        \Dynamic\Notifyme\Model\Notifyme $notifyme,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $http
    ) { 
        $this->shippingAddress = $shippingAddress;
        $this->mytickets    = $mytickets;
        $this->notifyme    = $notifyme;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->http = $http;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        $customerId = 0;
        $ticket_type = 4;
        $customerName = "";
        $customerLastName = "";
        if(isset($data["phone"])){
            $phone = $data["phone"];
        }

        $isLoggedIn = $this->http->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);

        if($isLoggedIn) {
            $customerId = $this->customerSession->getCustomer()->getId();
            $customerName = $this->customerSession->getCustomer()->getFirstname();
            $customerLastName = $this->customerSession->getCustomer()->getLastname();
            $data["email"] = $this->customerSession->getCustomer()->getEmail();
            $shippingAddressID =  $this->customerSession->getCustomer()->getDefaultBilling();
            $shippingAddresss = $this->shippingAddress->load($shippingAddressID);
            $shippingTelephone = $shippingAddresss->getTelephone();
            $phone =  $shippingTelephone;
        }

        $response = [];

        if(isset($data["email"]) && empty($data["email"])) {
            $response = [
                'errors' => true,
                'message' => __('Please enter email field.') 
            ];
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($response);
        }

        if (false === \strpos($data["email"], '@') || false === \strpos($data["email"], '.com')) {
            $response = [
                'errors' => true,
                'message' => __('The email address is invalid. Verify the email address and try again.')
            ];
            $resultJson = $this->resultJsonFactory->create();
            return $resultJson->setData($response);
        }

        if($data){
            $this->mytickets->setName($customerName);
            $this->mytickets->setLastName($customerLastName);
            $this->mytickets->setEmail($data['email']);
            if(isset($phone)){
                $this->mytickets->setPhone($phone);
            }
            $this->mytickets->setRemarks("sku:".$data['product_sku']);
            $this->mytickets->setCustomerId($customerId);
            $this->mytickets->setTicketType($ticket_type);
            
            // $this->notifyme->setData($data);
            try {
                $this->mytickets->save();
                // $this->notifyme->save();
                $response = [
                    'errors' => false,
                    'message' => __('<div class="cover1"><div class="title">Thank you.</div>
                        <span>We will inform you once the product is in stock.</span></br> 
                        <span class="custom-single-line">You can also view the status of your request on <a href="/tickets/customer/index/">My Tickets</a>.</span>
                        </div>
                        ')
                ];
            
            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            }
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}

