<?php
namespace Dynamic\OrderTicket\Controller\Ajax;

use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Model\Order;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Controller\ResultFactory;

class Create extends \Magento\Framework\App\Action\Action
{   
     
    private $session;
    
   /**
    * @var \Magento\Framework\Message\ManagerInterface
    */
    protected $messageManager;

    
    /**
     * Store manager
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Orderreturn
     *
     * @var \Dynamic\Orderreturn\Model\Orderreturn
     */
    protected $orderreturn;
    
    
    /**
    * @var SerializerInterface
    */
    private $serializer;   
    
    
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItem;

     /**
     * @var order
     */
     protected $order;    
    
     
     
     
     /**
     * Mytickets
     *
     * @var \Dynamic\Mytickets\Model\Mytickets
     */
    protected $mytickets;  
    protected $_storeManager;
    private $appContext;	
    
    protected $timezoneInterface;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Dynamic\Orderreturn\Model\Orderreturn $orderreturn
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dynamic\Orderreturn\Model\Orderreturn $orderreturn,
		 \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,         
         SerializerInterface $serializer,
         \Dynamic\Mytickets\Model\Mytickets $mytickets,         
         Order $order,
         TimezoneInterface $timezoneInterface,
         OrderItemRepositoryInterface $orderItem,
         \Magento\Framework\App\Http\Context $appContext,
         \Magento\Customer\Model\Session $session,
         ManagerInterface $messageManager
         ) { 
        $this->orderreturn    = $orderreturn;
		$this->_storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->serializer = $serializer;                
        $this->order = $order;
        $this->mytickets    = $mytickets;        
        $this->timezoneInterface = $timezoneInterface;
        $this->orderItem = $orderItem;
        $this->appContext = $appContext;
        $this->session = $session;       
        $this->messageManager       = $messageManager;         
        parent::__construct($context);
    }

    

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {		           
        
        $result = [];		
        $data = $this->getRequest()->getParams();                               
        $orderoriginal_id = $data['orddercancel_order_id'];
        //$orderitem_id = $data['item_id'];
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        try {	
			
		if(!$this->isLoggedIn()){
		   $result = [
				     'errors' => true,
				     'message' => __('please Login.')
				     ];					     			     
		  return $this->returnResponse($result);						
		}
							
			$order = $this->order->load($orderoriginal_id);			
			
			if(!$order->getEntityId()){
				// Through Not Order Found Error
				  $result = [
				     'errors' => true,
				     'message' => __('Order not found.')
				     ];
				     return $this->returnResponse($result);		     
			}
			$customerId =  $this->session->getCustomer()->getId();
			if( $customerId != $order->getCustomerId()){
				// Not Cutrrent Customer Order Error
				$result = [
				     'errors' => true,
				     'message' => __('Order not belong to your account')
				     ];
				     return $this->returnResponse($result);		     
			}
			
		$data = $this->getRequest()->getParams();		
		$ticketData = array();
		
		/*$ticketData['name']      = $data['orddercancel_name'];
		$ticketData['last_name'] = $data['orddercancel_last_name'];
		$ticketData['email']    = $data['orddercancel_email'];
		$ticketData['phone'] = $data['orddercancel_phone'];
		$ticketData['brand'] = $data['orddercancel_brand'];
		$ticketData['style'] = $data['orddercancel_style'];
		$ticketData['keyword'] = $data['orddercancel_keyword'];
		$ticketData['image'] = $data['orddercancel_image'];
		$ticketData['remarks'] = $data['orddercancel_remarks'];
		$ticketData['ticket_code'] = '';
		$ticketData['lang_code'] = $data['orddercancel_ang_code'];
		$ticketData['status'] = 0;*/
		
		$ticketData= array();
		$ticketData['name']      = $data['orddercancel_name'];
		$ticketData['last_name'] = $data['orddercancel_last_name'];
		$ticketData['email']    = $data['orddercancel_email'];
		$ticketData['phone'] = $data['orddercancel_phone'];
		$ticketData['brand'] = $data['orddercancel_brand'];
		$ticketData['style'] = $data['orddercancel_style'];
		$ticketData['keyword'] = $data['orddercancel_keyword'];
		$ticketData['image'] = $data['orddercancel_image'];
		$ticketData['remarks'] = $data['orddercancel_remarks'];
		$ticketData['ticket_code'] = '';
		$ticketData['lang_code'] = $data['orddercancel_lang_code'];
		$ticketData['status'] = 0;
		$ticketData['customer_id']  = $customerId;
		$ticketData['ticket_type']  = $data['orddercancel_tickettype'];				
		$this->mytickets->setData($ticketData);
		$this->mytickets->save();
		$result = [
                    'errors' => false,
                    'message' => __('<p>Request Ticket Sent, You will get Ticket ID shortly.</p>')
                ];
			
			
			
	    } catch (\Exception $e) {
			$result = [
				     'errors' => true,
				     'message' => $e->getMessage()
				     ];
		     return $this->returnResponse($result);
		}		
		//return $this->returnResponse($result);		 
		
		$resultRedirect->setUrl($this->_storeManager->getStore()->getBaseUrl().'createticket/');
        return $resultRedirect;           
       
    }
    
      public function isLoggedIn()
  {
        if (empty($this->session)) { return false; }
        if ($this->session->isLoggedIn()) { return true; }
        return $this->appContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
  }
  
   public function  returnResponse($response){
	    //$resultJson = $this->resultJsonFactory->create();
        //return $resultJson->setData($response);
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if($response['errors']){
			$response['message'] = strip_tags($response['message']);
            $this->messageManager->addErrorMessage ($response['message']);    
		}else{
			$response['message'] = strip_tags($response['message']);
			$this->messageManager->addSuccessMessage ($response['message']);    
		} 
        //return $this->_redirect('sales/order/history'); 
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect; 
   }
    
   
}

