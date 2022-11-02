<?php

namespace Dynamic\OrderHistory\Block\Order\History;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Dynamic\Orderreturn\Model\Orderreturn;

use Dynamic\OrderHistory\Model\OrderHistoryFactory;
use Dynamic\OrderHistory\Model\OrderHistory;
use Dynamic\OrderHistory\Model\OrderhistoryItem;


use Magento\Customer\Model\Session;

 

class View extends Template
{
	protected $orderReturnCollection;

	public $customerSession;
	
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
     * @var \Magento\Framework\App\Http\Context
     * @since 101.0.0
     */
    protected $httpContext;
	


	public function __construct(Context $context,
	 Orderreturn $orderReturnCollection, 	
	 OrderHistoryFactory $orderHistoryFactory,
	 OrderHistory $orderHistory,
	 OrderhistoryItem $OrderhistoryItem,
	 \Magento\Framework\Registry $coreRegistry,
	 \Magento\Framework\App\Http\Context $httpContext,
	 Session $customerSession
	)
	{
		$this->orderReturnCollection = $orderReturnCollection;
		$this->customerSession = $customerSession;		
		$this->orderHistoryFactory = $orderHistoryFactory;        
        $this->orderHistory = $orderHistory;
        $this->OrderhistoryItem = $OrderhistoryItem;
        $this->httpContext = $httpContext;
        $this->_coreRegistry = $coreRegistry;
        
		parent::__construct($context);
	}

	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$historialOrder = $this->getCurrentHistoricalOrder();
		$originalOrder = $this->getCurrentOriginalOrder();
		$title = __('Actual Items Ordered For Order # %1 ',$originalOrder->getIncrementId());
		$this->pageConfig->getTitle()->set($title );
		$pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle($title );
            }
		
		if ($this->getItemCollection()) {
			$pager = $this->getLayout()->createBlock(
				'Magento\Theme\Block\Html\Pager',
				'item.history.pager'
			)->setAvailableLimit([10 => 10, 20 => 20, 50 => 50])->setShowPerPage(true)->setCollection($this->getItemCollection());
			$this->setChild('pager', $pager);
			$this->getItemCollection()->load();
		}
		return $this;
	}
	
	public function getCurrentHistoricalOrder()
    {
        if (!$this->hasData('current_historicalorder')) {
            $this->setData('current_historicalorder', $this->_coreRegistry->registry('current_historicalorder'));
        }        
        return $this->getData('current_historicalorder');
    }
    
    
    public function getCurrentOriginalOrder()
    {
        if (!$this->hasData('current_originalorder')) {
            $this->setData('current_originalorder', $this->_coreRegistry->registry('current_originalorder'));
        }
        
        return $this->getData('current_originalorder');
    }
    

	public function getPagerHtml()
	{
		return $this->getChildHtml('pager');
	}

	public function getItemCollection()
	{
		$page = ($this->getRequest()->getParam('p')) ? $this->getRequest()->getParam('p') : 1;
		$pageSize = ($this->getRequest()->getParam('limit')) ? $this->getRequest()->getParam('limit') : 5;
		$collection = $this->orderReturnCollection->getCollection();
		$collection->setOrder('orderreturn_id ','DESC');
		$collection->addFieldToFilter('customer_email', ['eq' => $this->customerSession->getCustomer()->getEmail()]);
		$collection->setPageSize($pageSize);
		$collection->setCurPage($page);
		return $collection;
	}
	
	/**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
       return $this->getUrl('sales/order/history');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return \Magento\Framework\Phrase
     */
    public function getBackTitle()
    {
       return __('Back to My Orders');
    }


}
