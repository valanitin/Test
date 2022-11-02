<?php

namespace Dynamic\Mytickets\Controller\Ajax;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Http\Context as HTTPContext;
use Magento\Customer\Model\Session;
use Dynamic\Mytickets\Model\Mytickets;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Message extends \Magento\Framework\App\Action\Action
{
    /**
      * @var Session
      */
    private $session;
    
    /**
      * @var StoreManagerInterface
      */
    private $storeManager;
    
    /**
     * @var Curl
     */
    protected $_curl;
    
    /**
     * JsonFactory
     *
     * @var JsonFactory
     */
    protected $resultJsonFactory;
    
    /**
     * @var SerializerInterface
     */
    private $serializer;
    
    /**
     * Mytickets
     *
     * @var Mytickets
    */
    protected $mytickets;
    
    /**
     * HTTPContext
     *
     * @var HTTPContext
    */
    private $appContext;
    
    /**
     * TimezoneInterface
     *
     * @var TimezoneInterface
    */
    protected $timezoneInterface;
    
    /**
     * ScopeConfigInterface
     *
     * @var ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * Constructor
     *
     * @param Context  $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        SerializerInterface $serializer,
        TimezoneInterface $timezoneInterface,
        HTTPContext $appContext,
        Session $session,
        Mytickets $mytickets,
        StoreManagerInterface $storeManager,
        Curl $curl,
        ScopeConfigInterface $scopeConfig,
        DateTime $dateTime
    ) {
        
        $this->resultJsonFactory = $resultJsonFactory;
        $this->serializer = $serializer;
        $this->timezoneInterface = $timezoneInterface;
        $this->appContext = $appContext;
        $this->session = $session;
        $this->mytickets    = $mytickets;
        $this->storeManager = $storeManager;
        $this->_curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->dateTime = $dateTime;
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
        if (!isset($data['tid']) || !isset($data['tcode'])) {
            $result = [
                     'errors' => true,
                     'message' => __('Invalid Parameter to show messages.')
                     ];
              return $this->returnResponse($result);
        }
        
        if (!$this->isLoggedIn()) {
            $result = [
                     'errors' => true,
                     'message' => __('please Login.')
                     ];
            return $this->returnResponse($result);
            
        }
        if ($data['tcode'] != '') {
            $ticketMessages = $this->getErpTicketMessagebyCode($data['tcode']);
        } else {
            $ticketMessages = $this->getTicketMessagebyCode($data['tid']);
        }
        $message = __(' %1 found', count($ticketMessages));
        $result = [
                     'errors' => false,
                     'message' => $message,
                     'ticketMessages' =>$ticketMessages
                     
                     ];
        
        return $this->returnResponse($result);
    }

    /**
     * Return Response
     *
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function returnResponse($response)
    {
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
    
    public function isLoggedIn()
    {
        if (empty($this->session)) {
            return false;
        }
        if ($this->session->isLoggedIn()) {
            return true;
        }
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
    
    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    private function getErpTicketMessagebyCode($ticketCode){
        $apiTicketsData = array();
        $apiTicketsMessage = array();
        $url = "https://erp.theluxuryunlimited.com/api/ticket/send";
        $customer = $this->session->getCustomer();
        $customerEmail  = $customer->getEmail();
        $website = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $website = 'www.sololuxury.com';
        $newjsonData = array(
            'website' => $website,
            'email' => $customerEmail,
            'ticket_id'=> $ticketCode
        );
        $params = json_encode($newjsonData);
        $this->_curl->setOption(CURLOPT_POST, true);
        $this->_curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $headers = ["Content-Type" => "application/json"];
        $this->_curl->setHeaders($headers);
        $this->_curl->post($url,$params );
        $data = json_decode($this->_curl->getBody(), true);
        if(empty($data)){
            $ticketList = "";
        } else {
            $apiStatus = $data['status'];
            $apiTickets = $data['tickets'];
            $apiTicketsData = $apiTickets['data'][0];
            $apiTicketsMessage = $apiTicketsData['messages'];
        }
        // Return response instead of outputting
        return $apiTicketsData;
    }

    private function getTicketMessagebyCode($ticketId){
        $customer = $this->session->getCustomer();
        $customerEmail  = $customer->getEmail();
        $dataTicket = $this->mytickets->load($ticketId,'mytickets_id');

        $data = $dataTicket->getData();
        if(!empty($data['messages'])){
            $messages = $this->serializer->unserialize($data['messages']);
            $data['messages'] = $messages;
        }

        return $data;
    }
}
