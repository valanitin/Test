<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Customer;

use Magento\Backend\App\ConfigInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Session;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Stdlib\DateTime;
use Plumrocket\SocialLoginFree\Helper\Config;
use Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail;

class Authentication extends \Magento\Customer\Model\Authentication
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail
     */
    private $fakeEmail;

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config
     */
    private $config;

    /**
     * Authentication constructor.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface        $customerRepository
     * @param \Magento\Customer\Model\CustomerRegistry                 $customerRegistry
     * @param \Magento\Backend\App\ConfigInterface                     $backendConfig
     * @param \Magento\Framework\Stdlib\DateTime                       $dateTime
     * @param \Magento\Framework\Encryption\EncryptorInterface         $encryptor
     * @param \Magento\Customer\Model\Session                          $customerSession
     * @param \Plumrocket\SocialLoginFree\Model\Account\Data\FakeEmail $fakeEmail
     * @param \Plumrocket\SocialLoginFree\Helper\Config                $config
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistry $customerRegistry,
        ConfigInterface $backendConfig,
        DateTime $dateTime,
        Encryptor $encryptor,
        Session $customerSession,
        FakeEmail $fakeEmail,
        Config $config
    ) {
        parent::__construct($customerRepository, $customerRegistry, $backendConfig, $dateTime, $encryptor);
        $this->customerSession = $customerSession;
        $this->fakeEmail = $fakeEmail;
        $this->config = $config;
    }

    public function authenticate($customerId, $password)
    {
        if ($this->config->isModuleEnabled() &&
            $this->customerSession->isLoggedIn() &&
            $this->fakeEmail->detect($this->customerSession->getCustomer()->getEmail())
        ) {
            return true;
        }
        return parent::authenticate($customerId, $password);
    }
}
