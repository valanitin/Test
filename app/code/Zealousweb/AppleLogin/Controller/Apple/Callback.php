<?php
namespace Zealousweb\AppleLogin\Controller\Apple;

use Zealousweb\AppleLogin\Helper\Data as AppleHelper;

class Callback extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Zealousweb\AppleLogin\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $rowFactory;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    protected $objectManager;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Zealousweb\AppleLogin\Helper\Data $helper
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Framework\Controller\Result\RawFactory $rowFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Zealousweb\AppleLogin\Helper\Data $helper,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Framework\Controller\Result\RawFactory $rowFactory
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->rowFactory = $rowFactory;
        $this->customer = $customer;
        $this->objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }
    
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultRaw = $this->rowFactory->create();
        $post = $this->getRequest()->getParams(); 

        try{

            if($this->validateResponse($post)) {
            
                $response = $this->helper->curlCall($this->helper->getTokenUrl(), [
                    'grant_type' => 'authorization_code',
                    'code' => $post['code'],    
                    'client_id' => $this->helper->getClientId(),
                    'client_secret' => $this->helper->generateJWT(),
                    'redirect_uri' => $this->helper->getRedirectUri(),
                    'scope' => 'name email'
                ]);
                
                if(!isset($response->access_token)) {
                    throw new \Exception(__("There was a problem accessing your token. Please contact administrator."));
                }

                if(isset($response->error)) {
                    throw new \Exception(__("There was a error processing your request."));
                }

                $userData = $this->extractUserData($response, $post);            
                $email = isset($userData['email']) ? $userData['email'] : "";
                if($email == '') {
                    throw new \Exception(__('Something went wrong while getting user information from apple store.'));
                }

                $accountManagement = $this->objectManager->create('Magento\Customer\Api\AccountManagementInterface');
                $isEmailAvailable = $accountManagement->isEmailAvailable($email);
                if(!$isEmailAvailable) {                        
                    $customer = $this->customer->getCollection()->addAttributeToFilter('email', $email)->getFirstItem();
                } else {
                    $customer = $this->helper->createCustomer($userData);
                }    
                
                $this->helper->refreshCustomerSession($customer);

                $redirectionUrl = $this->helper->getRedirectionUrl();
                
                $raw = $resultRaw->setContents(
                    "<script>
                        window.opener.location.href='".$redirectionUrl."';
                        window.close();
                    </script>");

                return $raw;

            } else {
                throw new \Exception(__("There was a error processing your request."));
            }
        }
        catch(\Exception $e){

            $message = $e->getMessage();
            $this->messageManager->addError($message);
            $raw = $resultRaw->setContents(
                "<script>
                    window.opener.location.reload(true);
                    window.close();
                </script>");
            return $raw;
        }
    }

    /**
     * Validated response from the apple
     *
     * @return bool
     */
    public function validateResponse($post)
    {
        if( isset($post['code']) && isset($post['state'])
            && !empty($post['code']) && !empty($post['state']) 
        ){
            return true;
        }

        return false;
    }

    /**
     * Extract User data from the response 
     *
     * @return bool
     */
    public function extractUserData($response, $post){

        $userData = [];

        if(isset($post['user'])){
            $info = json_decode($post['user'], true);
            $userData['firstName'] = isset($info['name']['firstName']) ? $info['name']['firstName'] : $this->helper->getDefaultFirstName(); 
            $userData['lastName'] = isset($info['name']['lastName']) ? $info['name']['lastName'] : $this->helper->getDefaultLastname();
            $userData['email'] = isset($info['email']) ? $info['email'] : "";
        }

        $claims = explode('.', $response->id_token)[1];
        $claims = json_decode(base64_decode($claims));
        if(isset($claims->sub)) {
            $userData['appleId'] = $claims->sub;
        }

        if(!isset($userData['email']) || $userData['email'] == ''){
            if(isset($claims->email)) {
                $userData['firstName'] = $this->helper->getDefaultFirstName();
                $userData['lastName'] = $this->helper->getDefaultLastname();
                $userData['email'] = $claims->email;
            }
        }

        return $userData;
    }
}