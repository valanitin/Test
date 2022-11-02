<?php

/**
 * Customization data helper
 */
namespace Dynamic\Customization\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{	
	/**
     * Get country path
     */
    const COUNTRY_CODE_PATH = 'general/country/default';
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Category manager
     *
     * @var Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * Category manager
     *
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Framework\Registry $registry
     */
    protected $registry;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory $currencyFactory
     */
    protected $currencyFactory;

    /**
     * @var Magento\Customer\Model\Session $customerSession
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Session\Proxy $checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Module\Manager $moduleManager
     */
    protected $moduleManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
    * @var \Magento\Directory\Model\Currency
    */
    private $currency;

    /**
    * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
    */
    private $productTypeInstance;

    /**
    * @var \Magento\Catalog\Model\CategoryFactory
    */
    private $categoryFactory;

    /**
    * @var \Magento\Catalog\Helper\Image
    */
    private $imageHelper;

    /**
    * @var \Magento\Framework\Pricing\Helper\Data
    */
    private $priceHelper;

    /**
    * @var \Magento\Framework\App\Http\Context
    */
    private $http;

    /**
    * @var \Magento\Directory\Model\ResourceModel\Country\CollectionFactory
    */
    private $countryCollectionFactory;

    /**
    * @var \Magento\Directory\Model\CountryFactory
    */
    private $countryFactory;

    /**
    * @var \Magento\Eav\Model\Config
    */
    private $eavConfig;

    /**
    * @var Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
    */
    private $productCollection;

    /**
    * @var \Magento\Sales\Model\Order
    */
    private $order;
    
    /**
    * @var \Magento\Framework\Session\SessionManagerInterface
    */
    
    private $sessionManagerInterface;

    /**
     * @var \Magento\Directory\Model\Config\Source\Country
     */
    private $country;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session\Proxy $checkoutSession
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Psr\Log\LoggerInterface  $logger
     * @param Magento\Framework\Stdlib\DateTime\DateTime  $date
     * @param \Magento\Directory\Model\Currency  $currency
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable  $productTypeInstance
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\App\Http\Context $http
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Magento\Sales\Model\Order $order
     *  @param \Magento\Framework\Session\SessionManagerInterface $sessionManagerInterface
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Category $category,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session\Proxy $checkoutSession,
        \Magento\Framework\Module\Manager $moduleManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Directory\Model\Currency $currency,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $productTypeInstance,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Framework\App\Http\Context $http,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\Session\SessionManagerInterface $sessionManagerInterface,
        \Magento\Directory\Model\Config\Source\Country $country
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->category = $category;
        $this->product = $product;
        $this->registry = $registry;
        $this->currencyFactory = $currencyFactory;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->moduleManager = $moduleManager;
        $this->logger = $logger;
        $this->date = $date;
        $this->currency = $currency;
        $this->productTypeInstance = $productTypeInstance;
        $this->categoryFactory = $categoryFactory;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->http = $http;
        $this->countryCollectionFactory = $countryCollectionFactory;
        $this->countryFactory = $countryFactory;
        $this->eavConfig = $eavConfig;
        $this->productCollection = $productCollection;
        $this->order = $order;
        $this->sessionManagerInterface =  $sessionManagerInterface;
        $this->country = $country;
        parent::__construct($context);
    }
    
    public function getOrderManager()
    { 
        return $this->order;
    }

    public function getProductCollectionManager()
    { 
        return $this->productCollection;
    }

    public function getEavConfigManager()
    { 
        return $this->eavConfig;
    }

    public function getCountryFactoryManager()
    { 
        return $this->countryFactory;
    }

    public function getCountryCollectionManager()
    { 
        return $this->countryCollectionFactory;
    }

    public function getHttpManager()
    { 
        return $this->http;
    }

    public function getStoreManager()
    { 
        return $this->_storeManager;
    }

    public function getProductManager()
    { 
        return $this->product;
    }

    public function getPriceHelperManager()
    { 
        return $this->priceHelper;
    }

    public function getImageHelperManager()
    { 
        return $this->imageHelper;
    }

    public function getCategoryManager()
    { 
        return $this->categoryFactory;
    }

    public function getCurrencyManager()
    { 
        return $this->currencyFactory;
    }

    public function getProductTypeInstanceManager()
    { 
        return $this->productTypeInstance;
    }

    public function getCurrency()
    { 
        return $this->currency;
    }

    public function getCustomerSessionManager()
    { 
        return $this->customerSession;
    }

    public function getCheckoutSessionManager()
    { 
        return $this->checkoutSession;
    }

    public function getModuleManager()
    { 
        return $this->moduleManager;
    }

    public function getDate()
    { 
        return $this->date;
    }

    public function getLogger()
    { 
        return $this->logger;
    }

    public function getScopeConfig()
    { 
        return $this->scopeConfig;
    }

    public function getRegistry()
    { 
        return $this->registry;
    }

    public function getBaseUrl()
    { 
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getMediaBaseUrl()
    { 
        return $this->_storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
    }
    
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
                $config_path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
    }

    public function getCurrentCategory()
    {        
        return $this->registry->registry('current_category');
    }

    public function getConfigurationOptions()
    {
        return $this->getConfig('dynamic_orderstatus/general/oathtoken');
    }
    
    public function getSessionManager(){
		return $this->sessionManagerInterface;
	}
	
	public function getcustomerLoggedIn()
    {
        return (bool)$this->http->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }
	
	public function getcountrycode()
    {
        return  $this->scopeConfig->getValue(self::COUNTRY_CODE_PATH,\Magento\Store\Model\ScopeInterface::SCOPE_STORE );
    }
	
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
    public function getCountry() {
        return $this->country;
    }
}
