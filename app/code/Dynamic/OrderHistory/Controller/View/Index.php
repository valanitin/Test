<?php

namespace Dynamic\OrderHistory\Controller\View;


use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Model\Order;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Registry;
use Magento\Framework\Message\ManagerInterface;
use Dynamic\Orderreturn\Model\DeleteOrderItem;
use Dynamic\OrderHistory\Model\OrderHistoryFactory;
use Dynamic\OrderHistory\Model\OrderHistory;
use Dynamic\OrderHistory\Model\OrderhistoryItem;


class Index extends \Magento\Framework\App\Action\Action
{   
    
    protected $resultPage;
    
    
    protected $deleteOrderItem;
    
    /**
     * Store manager
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    
    
    /**
    * @var SerializerInterface
    */
    private $serializer;

    /**
     * Orderreturn
     *
     * @var \Dynamic\Orderreturn\Model\Orderreturn
     */
    protected $orderreturn;
    
    /**
     * @var OrderItemRepositoryInterface
     */
    private $orderItemRepository;

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
     * @var \Dynamic\OrderHistory\Model\OrderhistoryItem
     */
    protected $OrderhistoryItem;
    
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
    
     /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    
    private $appContext;	
    private $session;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Dynamic\Orderreturn\Model\Orderreturn $orderreturn
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(        
        \Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Dynamic\Orderreturn\Model\Orderreturn $orderreturn,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
         SerializerInterface $serializer,
         OrderHistoryFactory $orderHistoryFactory,
         OrderHistory $orderHistory,
         OrderItemRepositoryInterface $orderItemRepository,
         OrderhistoryItem $OrderhistoryItem,
         Order $order,
         Registry $coreRegistry,
         ManagerInterface $messageManager,
         \Magento\Framework\App\Http\Context $appContext,
         \Magento\Customer\Model\Session $session
    ) { 
        $this->resultPageFactory = $pageFactory;
        $this->orderreturn    = $orderreturn;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->serializer = $serializer;
        $this->orderHistoryFactory = $orderHistoryFactory;        
        $this->orderHistory = $orderHistory;
        $this->OrderhistoryItem = $OrderhistoryItem;
        $this->orderItemRepository = $orderItemRepository;        
        $this->order = $order;
        $this->_coreRegistry = $coreRegistry;
        $this->messageManager       = $messageManager;
        $this->appContext = $appContext;
        $this->session = $session;
        
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {		      
		if(!$this->isLoggedIn()){
			$resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
            
			//return $this->resultRedirectFactory->create()->setUrl('customer/account/login');
			
		}		
        $data = $this->getRequest()->getParams();                               
        $orderoriginal_id = $data['orderoriginal_id'];
        try {						
			//$historyorder = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderoriginal_id)->getFirstItem();			
			$historyorder = $this->orderHistoryFactory->create()->load($orderoriginal_id,'order_id');
			//echo "<pre>".print_r($historyorder->getData(), 1)."</pre>";	
			//die;
			$this->_coreRegistry->register('current_historicalorder', $historyorder);
			$order =  $this->order->load($historyorder->getOrderId());
			$this->_coreRegistry->register('current_originalorder', $order);			
			$this->resultPage = $this->resultPageFactory->create(); 
			return $this->resultPage;
			
			/*echo "<pre>".print_r($historyorder->getData(), 1)."</pre>";	
			$historyorderItems = $this->OrderhistoryItem->getCollection()->addfieldtofilter('history_order_id', $historyorder->getEntityId());			
			if(count($historyorderItems)){
				foreach($historyorderItems as $orderItem){
					echo "<pre>".print_r($orderItem->getData(), 1)."</pre>";	
					
				}
			}*/
	    } catch (\Exception $e) {
			
			$this->messageManager->addErrorMessage ($e->getMessage());     
		}
        
        return $this->_redirect('customer/account/index');         
    }
    
    public function isLoggedIn()
  {
        if (empty($this->session)) { return false; }
        if ($this->session->isLoggedIn()) { return true; }
        return $this->appContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
  }
}

