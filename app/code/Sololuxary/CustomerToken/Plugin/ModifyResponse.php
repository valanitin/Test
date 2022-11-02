<?php

namespace Sololuxary\CustomerToken\Plugin;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Customer\Model\AuthenticationInterface;

class ModifyResponse
{


/**
 * Customer Account Service
 *
 * @var AccountManagementInterface
 */
private $accountManagement;

/**
 * @var \Magento\Integration\Model\CredentialsValidator
 */
private $validatorHelper;

/**
 * @var RequestThrottler
 */
private $requestThrottler;

/**
 * @var AuthenticationInterface
 */
protected $authentication;

/**
 * @var CustomerRepositoryInterface
 */
private $customerRepository;

/**
 * Initialize service
 *
 * @param AccountManagementInterface $accountManagement
 * @param \Magento\Integration\Model\CredentialsValidator $validatorHelper
 * @param CustomerRepositoryInterface $customerRepository
 */
public function __construct(
    AccountManagementInterface $accountManagement,
    CredentialsValidator $validatorHelper,
    CustomerRepositoryInterface $customerRepository
    ) {
    $this->accountManagement = $accountManagement;
    $this->validatorHelper = $validatorHelper;
    $this->customerRepository =$customerRepository;
}

/**
 * Modify customer token rest response
 *
 * @param \Magento\Integration\Model\CustomerTokenService $subject
 * @param String $username
 * @param String $password
 * 
 * @return null
 * @deprecated 100.0.4
 */
public function beforeCreateCustomerAccessToken(\Magento\Integration\Model\CustomerTokenService $subject, $username, $password)
{

    try{
        $customer = $this->customerRepository->get($username);

        }  catch (NoSuchEntityException $e) {
            $this->getRequestThrottler()->logAuthenticationFailure($username, RequestThrottler::USER_TYPE_CUSTOMER);
            throw new AuthenticationException(
                __(
                    'Username invalid.' . 'Please wait and try again later.'
                )
            );        
        }

    $customerId = (int)$customer->getId();

    try {
        $this->getAuthentication()->authenticate($customerId, $password);
        // phpcs:ignore Magento2.Exceptions.ThrowCatch
    } catch (InvalidEmailOrPasswordException $e) {
        $this->getRequestThrottler()->logAuthenticationFailure($username, RequestThrottler::USER_TYPE_CUSTOMER);
        throw new AuthenticationException(
            __(
                'Password invalid.'
                . 'Please wait and try again later.'
            )
        );
    }

    
    return null;
}  

/**
 * Get request throttler instance
 *
 * @return RequestThrottler
 * @deprecated 100.0.4
 */
private function getRequestThrottler()
{
    if (!$this->requestThrottler instanceof RequestThrottler) {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(RequestThrottler::class);
    }
    return $this->requestThrottler;
}

/**
 * Get authentication
 *
 * @return AuthenticationInterface
 */
private function getAuthentication()
{
    if (!($this->authentication instanceof AuthenticationInterface)) {
        return \Magento\Framework\App\ObjectManager::getInstance()->get(
            \Magento\Customer\Model\AuthenticationInterface::class
        );
    } else {
        return $this->authentication;
    }

}

}