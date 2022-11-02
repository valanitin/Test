<?php

namespace LuxuryUnlimited\ForgotPasswordModification\Plugin\Customer\Password;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\EmailNotificationInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLogger;

class Reset
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var PsrLogger
     */
    protected $logger;
    /**
     * @var AddressRegistry
     */
    private $addressRegistry;
    /**
     * @var Random
     */
    private $mathRandom;
    /**
     * @var EmailNotificationInterface
     */
    private $emailNotification;

    /**
     * @param StoreManagerInterface       $storeManager
     * @param CustomerRepositoryInterface $customerRepository
     * @param AddressRegistry             $addressRegistry
     * @param Random                      $mathRandom
     * @param EmailNotificationInterface  $emailNotification
     * @param PsrLogger                   $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customerRepository,
        AddressRegistry $addressRegistry,
        Random $mathRandom,
        EmailNotificationInterface $emailNotification,
        PsrLogger $logger
    ) {
        $this->storeManager       = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->addressRegistry    = $addressRegistry;
        $this->mathRandom         = $mathRandom;
        $this->emailNotification  = $emailNotification;
        $this->logger             = $logger;
    }

    /**
     *  Around Plugin to handle the forgot password flow
     *
     * @param AccountManagement $subject
     * @param callable          $proceed
     * @param string            $email
     * @param string            $template
     * @param int|null          $websiteId
     *
     * @return bool
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundInitiatePasswordReset(
        AccountManagement $subject,
        callable $proceed,
        $email,
        $template,
        $websiteId = null
    ) {
        if ($websiteId === null) {
            $websiteId = $this->storeManager->getStore()->getWebsiteId();
        }
        // load customer by email
        try {
            $customer = $this->customerRepository->get($email, $websiteId);
        } catch (NoSuchEntityException $exception) {
            throw new LocalizedException(__('User is not available'));
        }

        // No need to validate customer address while saving customer reset password token
        $this->disableAddressValidation($customer);

        $newPasswordToken = $this->mathRandom->getUniqueHash();
        $subject->changeResetPasswordLinkToken($customer, $newPasswordToken);

        try {
            switch ($template) {
                case AccountManagement::EMAIL_REMINDER:
                    $this->getEmailNotification()->passwordReminder($customer);
                    break;
                case AccountManagement::EMAIL_RESET:
                    $this->getEmailNotification()->passwordResetConfirmation($customer);
                    break;
                default:
                    $this->handleUnknownTemplate($template);
                    break;
            }

            return true;
        } catch (MailException $e) {
            // If we are not able to send a reset password email, this should be ignored
            $this->logger->critical($e);
        }

        return false;
    }

    /**
     * Disable Customer Address Validation
     *
     * @param CustomerInterface $customer
     *
     * @throws NoSuchEntityException
     */
    private function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Get email notification
     *
     * @return EmailNotificationInterface
     * @deprecated 100.1.0
     */
    private function getEmailNotification()
    {
        if (!($this->emailNotification instanceof EmailNotificationInterface)) {
            return ObjectManager::getInstance()->get(EmailNotificationInterface::class);
        }

        return $this->emailNotification;

    }

    /**
     * Handle not supported template
     *
     * @param string $template
     *
     * @throws InputException
     */
    private function handleUnknownTemplate($template)
    {
        $errMessage = 'Invalid value of "%value" provided for the %fieldName field. ';
        $errMessage .= 'Possible values: %template1 or %template2.';
        throw new InputException(
            __(
                $errMessage,
                [
                    'value'     => $template,
                    'fieldName' => 'template',
                    'template1' => AccountManagement::EMAIL_REMINDER,
                    'template2' => AccountManagement::EMAIL_RESET
                ]
            )
        );
    }
}
