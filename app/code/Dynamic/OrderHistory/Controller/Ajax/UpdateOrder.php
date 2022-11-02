<?php

namespace Dynamic\OrderHistory\Controller\Ajax;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Model\Order;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Message\ManagerInterface;
use Dynamic\Orderreturn\Model\DeleteOrderItem;
use Dynamic\OrderHistory\Model\OrderHistoryFactory;
use Dynamic\OrderHistory\Model\OrderHistory;
use Dynamic\OrderHistory\Model\OrderhistoryItemFactory;


class UpdateOrder extends \Magento\Framework\App\Action\Action
{   
     
    private $session;
    
    protected $deleteOrderItem;
    
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    /**
     * JsonFactory
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
     * OrderHistoryFactory
     *
     * @var \Dynamic\OrderHistory\Model\OrderHistoryFactory
     */
    protected $orderHistoryFactory;   
    
    /**
     * OrderHistory
     *
     * @var \Dynamic\OrderHistory\Model\OrderHistory
     */
    protected $orderHistory;
    
    /**
     * OrderhistoryItem
     *
     * @var \Dynamic\OrderHistory\Model\OrderhistoryItemFactory
     */
    protected $orderhistoryItemFactory;
    
    private $appContext;	
    
    protected $timezoneInterface;
    
    /**
     * Mytickets
     *
     * @var \Dynamic\Mytickets\Model\Mytickets
     */
    protected $mytickets;

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
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
         DeleteOrderItem $deleteOrderItem,         
         SerializerInterface $serializer,
         OrderHistoryFactory $orderHistoryFactory,
         OrderHistory $orderHistory,
         OrderhistoryItemFactory $orderhistoryItemFactory,
         Order $order,
         TimezoneInterface $timezoneInterface,
         OrderItemRepositoryInterface $orderItem,
         \Magento\Framework\App\Http\Context $appContext,
         \Magento\Customer\Model\Session $session,
         \Dynamic\Mytickets\Model\Mytickets $mytickets,
         StoreManagerInterface $storeManager
         ) { 
        $this->orderreturn    = $orderreturn;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->deleteOrderItem = $deleteOrderItem;
        
        $this->serializer = $serializer;        
        $this->orderHistoryFactory = $orderHistoryFactory;        
        $this->orderHistory = $orderHistory;
        $this->orderhistoryItemFactory = $orderhistoryItemFactory;        
        $this->order = $order;
        $this->timezoneInterface = $timezoneInterface;
        $this->orderItem = $orderItem;
        $this->appContext = $appContext;
        $this->session = $session;        
        $this->mytickets    = $mytickets;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {		      
		
		/*if(!$this->isLoggedIn()){
		   $result = [
				     'errors' => true,
				     'message' => __('please Login.')
				     ];				     
		  return $this->returnResponse($result);			
			
		}*/
		$response = [];		
        $data = $this->getRequest()->getParams();               
        $orderoriginal_id = $data['orderoriginal_id'];
        $reason = isset($data['reason'])?$data['reason']:""; 
        
        try {						
			$order = $this->order->load($orderoriginal_id);
			if(!$order->getEntityId()){
				// Through Not Order Found Error
				  $result = [
				     'errors' => true,
				     'message' => __('Order not found.')
				     ];
				     return $this->returnResponse($result);		     
			}
			$customerId=  $this->session->getCustomer()->getId();
			if( $customerId != $order->getCustomerId()){
				// Not Cutrrent Customer Order Error
				$result = [
				     'errors' => true,
				     'message' => __('Order not belong to your account')
				     ];
				     return $this->returnResponse($result);		     
			}
			$historyorder = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderoriginal_id);			
			/*OK if($historyorder->getSize()>=1){
				$result = [
				     'errors' => true,
				     'message' => __('Order history already created')
				     ];
				     return $this->returnResponse($result);		     
				
			}*/
			
				/**** save Order original Data as history  -Pritam ****/
				if($historyorder->getSize()<=0){
				    $result = $this->createOrderHistory($orderoriginal_id, 0);				
				}
						     
				/**** Update original Order and Order Item -Pritam ****/
				$orderItems = $order->getAllItems();
				if(count($orderItems)){
					foreach($orderItems as $orderItem){						
						$item_id = $orderItem->getItemId();
						$this->deleteOrderItem->deleteOrderItem($orderoriginal_id, $item_id);
					}
				}
				
				// Add 'order Cancel Comment' to 'sales_order' table
				$formatDate = $this->timezoneInterface->formatDate();        
                $currDate = $this->timezoneInterface->date()->format('d.m.Y @ h:i A');
				$cancelComment = __('Canceled on').' '.$currDate;		
				$order->setCancelComment($cancelComment);
				$order->save();		
				
				/**** Create Ticket on Item Cancel Request #16866 *****/
				 $order = $this->order->load($orderoriginal_id);     
				 $orderItems = $order->getAllItems();
				 $itemsSkus = '';
				 foreach($orderItems as $item){
					 	$itemsSkus = $itemsSkus.$item->getSKU().',';
				 }   				 
                 $ticketData= array();
		         $ticketData['name']        = $order->getCustomerFirstname();
		         $ticketData['last_name']   = $order->getCustomerLastname();
		         $ticketData['email']       = $order->getCustomerFirstname();
		         $ticketData['phone']       = $order->getCustomerFirstname();
		         $ticketData['brand']       = __('Order # : %1',$order->getIncrementId());
		         $ticketData['style']       = $itemsSkus;
		         $ticketData['keyword']     = __('Order Request');
		         $ticketData['image']       = '';
		         $ticketData['remarks']     = __('Order Request for Order  : #%1',$order->getIncrementId());
		         $ticketData['remarks']     = $ticketData['remarks'] ." ,".__('Reason').' :' .$reason ;
		         $ticketData['ticket_code'] = '';
		         $ticketData['lang_code']   = $this->getStoreCodeById($order->getStoreId());
		         $ticketData['status']      = 0;	
		         $ticketData['customer_id']  = $order->getCustomerId();		 
		         $ticketData['ticket_type']  = 3;		 	         
		         $this->mytickets->setData($ticketData);
		         $this->mytickets->save();			         
				
				$result = [
				     'errors' => false,
				     'message' => __('Order successfully updated and  a Request Ticket has been created, You will get Ticket ID shortly.')
				     ];
				    return $this->returnResponse($result);
			
	    } catch (\Exception $e) {
			$result = [
				     'errors' => true,
				     'message' => $e->getMessage()
				     ];
		     return $this->returnResponse($result);
		}
		
		return $this->returnResponse($result);		            
       
    }
    
   
   public function  returnResponse($response){
	    $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
   }
   
   /**
     * result
     *
     * @return string
     */
    public function getRuleIds(int $itemId) 
    {
        $ruleIds = null;
        try {
            $orderItem = $this->orderItem->get($itemId);
            $ruleIds = $orderItem->getAppliedRuleIds();
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $ruleIds;
    } 
    
   protected function createOrderHistory($orderId, $orderreturn_id){
		
		$orderoriginal_id = $orderId;
        try {
			//Check Histiry for order Id already exist or not			
			$orderHistoryExist = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderoriginal_id);			
            if($orderHistoryExist->getSize() <= 0){			 
		        $order = $this->order->load($orderoriginal_id);		
		        $orderItems = $order->getAllItems();		        
		        $orderhistory = $this->orderHistoryFactory->create();
		        $orderhistory->setData('order_id', $orderoriginal_id);
		        $orderhistory->setData('orderreturn_id', $orderreturn_id);
                foreach($order->getData() as $field=>$value){				
				    if($field=='entity_id') continue;				        
				        $orderhistory->setData($field, $value);
			    }
               $orderhistory->save();
               $history_order_id = $orderhistory->getEntityId();
              if(count($orderItems)){		            
		            foreach($orderItems as $orderItem){					 
						$orderhistoryItem = $this->orderhistoryItemFactory->create();						
						foreach($orderItem->getData() as $field=>$value){							  
						    if($field=='item_id'){
								 continue;	  								 
				            }else if($field == 'entity_id' || $field == 'extension_attributes' || $field == 'product_image'){
								continue;
							} else if($field == 'order_id'){
								$field = 'history_order_id';
								$value = $history_order_id;
							} else if($field == 'product_options'){
								$value = $this->serializer->serialize($value);
							}				            
				            $orderhistoryItem->setData($field, $value);
				            if( $field=='base_discount_refunded'){
								$orderhistoryItem->setData('base_discount_refunded',$value);
								break;
							}
			             }
			            $orderhistoryItem->save(); 
					}
			    }
              
            } 
            
            $result = [
                    'errors' => false,
                    'message' => __('Order history created successfully.')
                ];
            
            
	    }catch (\Exception $e) {			
			$result = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
		}
		
		return $result;
		
	} 
    
  public function isLoggedIn()
  {
        if (empty($this->session)) { return false; }
        if ($this->session->isLoggedIn()) { return true; }
        return $this->appContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
  }
  
  public function getStoreCodeById(int $id): ?string
    {
        try {
            $storeData = $this->storeManager->getStore($id);
            $storeCode = (string)$storeData->getCode();
        } catch (LocalizedException $localizedException) {
            $storeCode = null;
            $this->logger->error($localizedException->getMessage());
        }
        return $storeCode;
    }

}

