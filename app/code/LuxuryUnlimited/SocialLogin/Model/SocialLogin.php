<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */
declare(strict_types=1);

namespace LuxuryUnlimited\SocialLogin\Model;

use LuxuryUnlimited\SocialLogin\Api\SocialLoginInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\App\ResourceConnection;
use WeltPixel\SocialLogin\Model\Sociallogin as Social;
use Magento\Integration\Model\Oauth\TokenFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\EmailNotificationInterface;
use Plumrocket\SocialLoginFree\Model\Account;

class SocialLogin implements SocialLoginInterface
{
    /*
	 * @var \Psr\Log\LoggerInterface
	 */
	protected $logger;
	
	/*
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $Json;

    /*
	 * @var \Dynamic\Mytickets\Model\Mytickets
	 */
	protected $myTickets;

    /*
	 * @var \Magento\Framework\Webapi\Rest\Request
	 */
	protected $request;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var Social
     */
    protected $sociallogin;

    /**
     * @var TokenFactory
     */
    protected $tokenModelFactory;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var CustomerInterfaceFactory
     */
    private $customerDataFactory;

    /**
     * @var Random
     */
    private $random;
    
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var Account
     */
    private $account;

    /**
     * Constructor
     * @param LoggerInterface $logger
     * @param Json $json
     * @param WriterInterface $writerInterface
     * @param Request $request
     * @param ResourceConnection $resource 
     * @param Sociallogin $sociallogin
     * @param TokenFactory $tokenModelFactory
     * @param StoreManagerInterface $storeManager
     * @param CustomerFactory $customerRepository
     * @param CustomerInterfaceFactory $customerDataFactory
     * @param Random $random
     * @param EncryptorInterface $encryptor
     * @param CustomerRepositoryInterface $customerRepository
     * @param Account $account
     */
    public function __construct(
        LoggerInterface $logger,
        Json $json,
        Request $request,
        ResourceConnection $resource,
        Social $sociallogin,
        TokenFactory $tokenModelFactory,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        CustomerInterfaceFactory $customerDataFactory,
        Random $random,
        EncryptorInterface $encryptor,
        EmailNotificationInterface $emailNotificationInterface,
        CustomerRepositoryInterface $customerRepository,
        Account $account
    ) {
        $this->logger = $logger;
        $this->json = $json;
        $this->request = $request;
        $this->resource = $resource;
        $this->sociallogin = $sociallogin;
        $this->tokenModelFactory = $tokenModelFactory;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->random = $random;
        $this->encryptor = $encryptor;
        $this->emailNotificationInterface = $emailNotificationInterface;
        $this->customerRepository = $customerRepository;
        $this->account = $account;
    }

    /**
     * Social Login
     *
     * @return string
     */
    public function login()
    {
        try {
            $this->logger->info('-- Social Login Api Call --');
            $result = []; 
            $data = $this->request->getBodyParams();
            if(isset($data['user_id'])&&isset($data['firstname'])
                &&isset($data['lastname'])&&isset($data['email'])&&isset($data['type'])){
                if($data['type'] == "twitter"){
                    $websiteId = $this->storeManager->getStore()->getWebsiteId();
                    $storeId = $this->storeManager->getStore()->getId();
                    $socialData = $this->account->load($data['user_id'],"user_id")->getData();
                    if(!empty($socialData)){
                        $customerId = $socialData['customer_id'];
                        $customerToken = $this->tokenModelFactory->create();
                        $tokenKey = $customerToken->createCustomerToken($customerId)->getToken();
                        return $tokenKey;
                    }
                    else{
                        $customerData = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($data['email']);
                        $customerId = $customerData->getId();
                        if($customerId){
                            $result[] =[
                                'status' => "Error",
                                'message' => "Already an email exists with a different user id"
                            ];
                            return $result;
                        }
                        $customer = $this->customerDataFactory->create();
                        $customer->setWebsiteId($websiteId);
                        $customer->setEmail($data['email']);
                        $customer->setFirstname($data['firstname']);
                        $customer->setLastname("lastname");
                        $customer->setGender($data['gender']);
                        $customer->setTaxvat("taxvat");
                        $hashedPassword = $this->encryptor->getHash($this->_getRandomPassword(), true);
                        $customerData = $this->customerRepository->save($customer, $hashedPassword);
                        $customerId = $customerData->getId();
                        $socialdata = [
                            'type' => $data['type'],
                            'user_id' => $data['user_id'],
                            'customer_id' => $customerId
                        ];
                        $this->account->addData($socialdata)->save();
                        $customerToken = $this->tokenModelFactory->create();
                        $tokenKey = $customerToken->createCustomerToken($customerId)->getToken();
    
                        $this->getEmailNotification()->newAccount($customerData, EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD, '', $storeId);
                        return $tokenKey;
                    }
                }
                else{
                    $websiteId = $this->storeManager->getStore()->getWebsiteId();
                    $storeId = $this->storeManager->getStore()->getId();
                    $socialData = $this->sociallogin->load($data['user_id'],"sociallogin_id")->getData();
                    if(!empty($socialData)){
                        $customerId = $socialData['customer_id'];
                        $customerToken = $this->tokenModelFactory->create();
                        $tokenKey = $customerToken->createCustomerToken($customerId)->getToken();
                        return $tokenKey;
                    }
                    else{
                        $customerData = $this->customerFactory->create()->setWebsiteId($websiteId)->loadByEmail($data['email']);
                        $customerId = $customerData->getId();
                        if($customerId){
                            $result[] =[
                                'status' => "Error",
                                'message' => "Already an email exists with a different user id"
                            ];
                            return $result;
                        }
                        $customer = $this->customerDataFactory->create();
                        $customer->setWebsiteId($websiteId);
                        $customer->setEmail($data['email']);
                        $customer->setFirstname($data['firstname']);
                        $customer->setLastname("lastname");
                        $customer->setGender($data['gender']);
                        $customer->setTaxvat("taxvat");
                        $hashedPassword = $this->encryptor->getHash($this->_getRandomPassword(), true);
                        $customerData = $this->customerRepository->save($customer, $hashedPassword);
                        $customerId = $customerData->getId();
                        $socialdata = [
                            'type' => $data['type'],
                            'sociallogin_id' => $data['user_id'],
                            'customer_id' => $customerId
                        ];
                        $this->sociallogin->addData($socialdata)->save();
                        $customerToken = $this->tokenModelFactory->create();
                        $tokenKey = $customerToken->createCustomerToken($customerId)->getToken();

                        $this->getEmailNotification()->newAccount($customerData, EmailNotificationInterface::NEW_ACCOUNT_EMAIL_REGISTERED_NO_PASSWORD, '', $storeId);
                        return $tokenKey;
                    }
                }
            }
        } catch (\Exception $e) {
            $this->logger->info("-- Social Login Api call---" . $e);
            $result[] = ['status' => 'error','message' => $e->getMessage()];
            return $result;
        }
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getRandomPassword()
    {
        $len = 6;
        return $this->random->getRandomString($len);
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     */
    private function getEmailNotification()
    {
        return $this->emailNotificationInterface;
    }
}
