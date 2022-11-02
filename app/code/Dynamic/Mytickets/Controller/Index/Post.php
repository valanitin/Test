<?php

namespace Dynamic\Mytickets\Controller\Index;

class Post extends \Magento\Framework\App\Action\Action
{   
    /**
     * Store manager
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Mytickets
     *
     * @var \Dynamic\Mytickets\Model\Mytickets
     */
    protected $mytickets;
    
    /**
     * customerSession
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession; 

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Dynamic\Mytickets\Model\Mytickets $mytickets
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dynamic\Mytickets\Model\Mytickets $mytickets,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession
    ) { 
        $this->mytickets    = $mytickets;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
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
        $ticket_type = 1;
        if($this->isLoggedIn()){
			$customerId = $this->customerSession->getCustomer()->getId();
		}
		$data['customer_id']  = $customerId;
		$data['ticket_type']  = $ticket_type;
        $response = [];

        if($data){
            
            $this->mytickets->setData($data);
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

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
    
   public function isLoggedIn()
  {
        if (empty($this->customerSession)) { return false; }
        if ($this->customerSession->isLoggedIn()) { return true; }        
  }
}

