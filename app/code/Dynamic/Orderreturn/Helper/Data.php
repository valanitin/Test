<?php

/**
 * Orderreturn data helper
 */
namespace Dynamic\Orderreturn\Helper;

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
     * @var \Swarming\StoreCredit\Api\OrderAttributeManagementInterface
     */
    protected $orderAttributeManagement;

    /**
     * @var \Swarming\StoreCredit\Helper\Currency
     */
    protected $creditsCurrency;
    
    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement
     * @param \Swarming\StoreCredit\Helper\Currency $creditsCurrency
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Swarming\StoreCredit\Api\OrderAttributeManagementInterface $orderAttributeManagement,
        \Swarming\StoreCredit\Helper\Currency $creditsCurrency
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->orderAttributeManagement = $orderAttributeManagement;
        $this->creditsCurrency = $creditsCurrency;
        parent::__construct($context);
    }
 
    public function getStoreManager()
    { 
        return $this->_storeManager;
    }

    public function getScopeConfig()
    { 
        return $this->scopeConfig;
    }

    public function getStoreCredits($order)
    { 
        $storeCreditsValue = 0;
        $orderCredits = $this->orderAttributeManagement->getForOrder($order);

        if ($orderCredits->getCredits() == 0) {
            return $storeCreditsValue;
        }

        $credit = $this->creditsCurrency->format($orderCredits->getCredits(), \Swarming\StoreCredit\Model\Config\Display::FORMAT_TOTAL, $order->getStoreId(), $orderCredits->getAmount());
        $storeCreditsValue = $credit;
        return $credit;
    }
}
