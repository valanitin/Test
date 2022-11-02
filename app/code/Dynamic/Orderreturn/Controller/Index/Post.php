<?php

namespace Dynamic\Orderreturn\Controller\Index;

use Magento\Store\Model\StoreManagerInterface;
use Dynamic\Orderreturn\Model\DeleteOrderItem;
use Magento\Sales\Model\Order;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Dynamic\OrderHistory\Model\OrderHistoryFactory;
use Dynamic\OrderHistory\Model\OrderHistory;
use Dynamic\OrderHistory\Model\OrderhistoryItemFactory;

class Post extends \Magento\Framework\App\Action\Action
{   
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    
    protected $deleteOrderItem;
    
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
    private $orderItemRepository;

     /**
     * @var order
     */
     protected $order;
     
     /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItem;

 
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
         OrderItemRepositoryInterface $orderItem,
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
        $this->orderItem = $orderItem;
        $this->mytickets    = $mytickets;
        $this->storeManager = $storeManager;
        //$this->orderItemRepository = $orderItemRepository;        
        
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
        /*echo "<pre>".print_r($data , 1)."</pre>";
        die;*/
        $response = [];        
        $applied = $this->orderreturn->getCollection()->addfieldtofilter('product_sku', $data['product_sku'])->addfieldtofilter('order_id', $data['order_id']);
        if($data && $applied->getSize() <= 0){
            $data['website'] = "www.sololuxury.com";
            $this->orderreturn->setData($data);
            try {
				$item_id = $data['item_id'];
                $orderoriginal_id = $data['orderoriginal_id'];
                $order = $this->order->load($orderoriginal_id);	
                $this->orderreturn->save();                
                $orderreturn_id = $this->orderreturn->getOrderreturnId();
                /**** save Order original Data as history  -Pritam ****/
				$result = $this->createOrderHistory($orderoriginal_id, $orderreturn_id);
				
				/**** Update original Order and Order Item -Pritam ****/
				$this->deleteOrderItem->deleteOrderItem($orderoriginal_id, $item_id);
				
				/**** Create Ticket on Item Cancel Request #16866 *****/
				 $order = $this->order->load($orderoriginal_id);        
				 $orderItem = $this->orderItem->get($item_id);                 
                 $ticketData= array();
		         $ticketData['name']        = $order->getCustomerFirstname();
		         $ticketData['last_name']   = $order->getCustomerLastname();
		         $ticketData['email']       = $order->getCustomerFirstname();
		         $ticketData['phone']       = $order->getCustomerFirstname();
		         $ticketData['brand']       = $orderItem->getName();
		         $ticketData['style']       = $orderItem->getSku();
		         $ticketData['keyword']     = __('Cancel Request for SKU : %1',$orderItem->getSku());
		         $ticketData['image']       = '';
		         $ticketData['remarks']     = __('Cancel Request for SKU : %1', $orderItem->getSku()).__(" of Order # %1",$order->getIncrementId());
		         $ticketData['remarks']     = $ticketData['remarks'] ." ,".__('Reason').' :' .$data['reason'] ;
		         
		         $ticketData['ticket_code'] = '';
		         $ticketData['lang_code']   = $this->getStoreCodeById($order->getStoreId());
		         $ticketData['status']      = 0;		 
		         $ticketData['customer_id']  = $order->getCustomerId();		 
		         $ticketData['ticket_type']  = 1;		
		         $ticketData['orderreturn_id'] = $orderreturn_id;
		          
		         
		         $this->mytickets->setData($ticketData);
		         $this->mytickets->save();			
		         
				
                $response = [
                    'errors' => false,
                    'message' => __('Order return request successfully sent and a Request Ticket has been created, you will get updates soon.')
                ];
            
            } catch (\Exception $e) {
                $response = [
                    'errors' => true,
                    'message' => $e->getMessage()
                ];
            }
        }else{
            $response = [
                    'errors' => true,
                    'message' => __('You have already applied for return.')
                ];
        }

        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
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


