<?php

/**
 * OrderOrderHistory data helper
 */
namespace Dynamic\OrderHistory\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	
	 /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    
    
	 /**
     * OrderHistory
     *
     * @var \Dynamic\OrderHistory\Model\OrderHistory
     */
    protected $orderHistory;
    
    
     /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Dynamic\OrderHistory\Model\OrderHistory $OrderHistory     
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Dynamic\OrderHistory\Model\OrderHistory $orderHistory
        
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->orderHistory = $orderHistory;        
        parent::__construct($context);
    }
    
    public function isorderHistoryExist($orderId){
		$isExist =  false;		
		$orderHistoryCollection = $this->orderHistory->getCollection()->addfieldtofilter('order_id', $orderId);				
		
		if($orderHistoryCollection->getSize()>0){
			 $isExist =  true;		
		 }
		return $isExist;            
	}
	
}
