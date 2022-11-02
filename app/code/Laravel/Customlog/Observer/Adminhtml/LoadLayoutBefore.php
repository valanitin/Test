<?php

namespace Laravel\Customlog\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class LoadLayoutBefore implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;
	
	/**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
	
	/**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
	
	const XML_PATH_ENABLED = "custom/general/enabled";
	const XML_PATH_API_URL = "custom/general/api_url";
	const XML_PATH_WEBSITE_NAME = "custom/general/website_name";

    public function __construct(
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\UrlInterface $url,
		\Magento\Backend\Model\Auth\Session $authSession,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->responseFactory = $responseFactory;
        $this->request = $request;
        $this->url = $url;
		$this->authSession = $authSession;
		$this->scopeConfig = $scopeConfig;
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/observer-admin-logs.log');
		$this->logger = new \Zend\Log\Logger();
		$this->logger->addWriter($writer);
    }
	
    public function execute(Observer $observer)
    {
		// $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $log = $_objectManager->create('\Psr\Log\LoggerInterface');
		// try {
		// throw new \Exception('Disallowed file type.');
		// } catch(\Exception $e) {
			// $log->critical('test data');
			// $log->critical($e->getMessage());
		// }
		
		$moduleName = $this->request->getModuleName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
		
		$currentUrl = $this->url->getCurrentUrl();
		$adminUser = $this->getCurrentAdminUser();
		$adminUserName = $adminUserEmail = "";
		if($adminUser) {
			$adminUserName = $adminUser->getUsername();
			$adminUserEmail = $adminUser->getEmail();
		}
		
		$websiteName = $this->getWebsiteName();
		
		$data = [];
		$data['message'] = "Access by username: ".$adminUserName." Email: ".$adminUserEmail;
		$data['website'] = $websiteName ? $websiteName : "";
		$data['url'] = $currentUrl;
		$data['modulename'] = $moduleName;
		$data['controller'] = $controller;
		$data['action'] = $action;
		$this->logger->info("Load layout - ".json_encode($data));
		
		if($this->isEnabled()) {
			$apiUrl = $this->getApiUrl();
			$data_string = json_encode($data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiUrl);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_HTTPHEADER,
				array(
					'Content-Type:application/json',
					'Content-Length: ' . strlen($data_string)
				)
			);
			$server_output = curl_exec($ch);
			$this->logger->info("Api response - ".$server_output);
			curl_close ($ch);
		} else {
			$this->logger->info("Api is disabled");
		}
        return $this;
    }
	
	public function getCurrentAdminUser()
    {
        return $this->authSession->getUser();
    }
	
	public function getConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
	
	public function isEnabled()
    {
        return $this->getConfig(self::XML_PATH_ENABLED);
    }
	
	public function getApiUrl()
    {
        return $this->getConfig(self::XML_PATH_API_URL);
    }
	
	public function getWebsiteName()
    {
        return $this->getConfig(self::XML_PATH_WEBSITE_NAME);
    }
}
