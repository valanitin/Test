<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2015 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Account\Data;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\State\InputMismatchException;
use Magento\Framework\Math\Random;
use Magento\Store\Model\StoreManagerInterface;

class FakeEmail
{
    const FAKE_EMAIL_PREFIX = 'temp-email-ps';
    const USER_NAME_LENGTH = 10;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Math\Random
     */
    private $random;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * FakeDataGenerator constructor.
     *
     * @param \Magento\Store\Model\StoreManagerInterface        $storeManager
     * @param \Magento\Framework\Math\Random                    $random
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Random $random,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->storeManager = $storeManager;
        $this->random = $random;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return string
     */
    public function generate(): string
    {
        $address = self::FAKE_EMAIL_PREFIX .
            $this->random->getRandomString(self::USER_NAME_LENGTH, Random::CHARS_LOWERS . Random::CHARS_DIGITS);

        $domain = parse_url($this->storeManager->getStore()->getBaseUrl(), PHP_URL_HOST);

        return "{$address}@{$domain}";
    }

    /**
     * @param string $email
     * @return bool
     */
    public function detect(string $email): bool
    {
        return strpos($email, self::FAKE_EMAIL_PREFIX) === 0;
    }

    /**
     * @param int    $customerId
     * @param string $networkEmail
     */
    public function changeToReal(int $customerId, string $networkEmail)
    {
        if (! $networkEmail || $this->detect($networkEmail)) {
            return;
        }

        try {
            $customerDataModel = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return;
        } catch (LocalizedException $e) {
            return;
        }

        if ($networkEmail === $customerDataModel->getEmail()
            || ! $this->detect($customerDataModel->getEmail())
        ) {
            return;
        }

        if (! $this->existsCustomerWithEmail($networkEmail)) {
            $customerDataModel->setEmail($networkEmail);
            try {
                $this->customerRepository->save($customerDataModel);
            } catch (InputException $e) {
                return;
            } catch (InputMismatchException $e) {
                return;
            } catch (LocalizedException $e) {
                return;
            }
        }
    }

    /**
     * @param string $networkEmail
     * @return bool
     */
    private function existsCustomerWithEmail(string $networkEmail): bool
    {
        try {
            $this->customerRepository->get($networkEmail);
        } catch (NoSuchEntityException $e) {
            return false;
        } catch (LocalizedException $e) {
            return false;
        }

        return true;
    }
}
