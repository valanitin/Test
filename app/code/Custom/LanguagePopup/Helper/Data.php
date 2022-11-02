<?php
namespace Custom\LanguagePopup\Helper;

use Magento\Framework\App\Helper\AbstractHelper; 
use Magento\Framework\App\Helper\Context; 
use Magento\Store\Model\StoreManagerInterface; 

class Data extends AbstractHelper { 
	
	/**
     * @var storemanager
    */
	protected $storemanager; 
	
	
	/**
     * @param Magento\Framework\App\Helper\Contex $context
     * @param Magento\Store\Model\StoreManagerInterface $storemanager
     */
	public function __construct( Context $context, 
	    StoreManagerInterface $storemanager 
	) { 
		$this->storemanager = $storemanager;
        parent::__construct($context);
    }

    public function getStoresList($websiteID)
    {
        return $this->storemanager->getWebsite($websiteID)->getStores();        
    }
    
    public function getStoreUrl($storeId)
    {
        return $this->storemanager->getStore($storeId)->getBaseUrl();
    }
    
}
