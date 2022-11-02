<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\Base\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Message\Manager;
use Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface;
use Plumrocket\Base\Model\Extension\Authorization\Factory;
use Plumrocket\Base\Model\Extension\Authorization\Key;
use Plumrocket\Base\Model\Extension\Authorization\Key\Update;
use Plumrocket\Base\Model\Extension\Authorization\Message;
use Plumrocket\Base\Model\Extension\Authorization\Status\Refresh;
use Plumrocket\Base\Model\Extension\IsPaid;
use Plumrocket\Base\Model\Extension\Status\Disable;
use Plumrocket\Base\Model\System\Config\GetCurrentExtensionName;

/**
 * Base observer
 */
class SystemConfigEditBefore implements ObserverInterface
{
    /**
     * @var \Plumrocket\Base\Model\System\Config\GetCurrentExtensionName
     */
    private $getCurrentExtensionName;

    /**
     * @var \Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface
     */
    private $extensionAuthorizationRepository;

    /**
     * @var \Magento\Framework\Message\Manager
     */
    private $messageManager;

    /**
     * @var \Plumrocket\Base\Model\Extension\IsPaid
     */
    private $isPaidExtension;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key
     */
    private $authorizationKey;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Key\Update
     */
    private $updateAuthorizationKey;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Status\Refresh
     */
    private $refreshAuthorizationStatus;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Message
     */
    private $authorizationMessage;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Factory
     */
    private $extensionAuthorizationFactory;

    /**
     * @var \Plumrocket\Base\Model\Extension\Status\Disable
     */
    private $disableExtension;

    /**
     * @param \Plumrocket\Base\Model\System\Config\GetCurrentExtensionName   $getCurrentExtensionName
     * @param \Plumrocket\Base\Api\ExtensionAuthorizationRepositoryInterface $extensionAuthorizationRepository
     * @param \Magento\Framework\Message\Manager                             $messageManager
     * @param \Plumrocket\Base\Model\Extension\IsPaid                        $isPaidExtension
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key             $authorizationKey
     * @param \Plumrocket\Base\Model\Extension\Authorization\Key\Update      $updateAuthorizationKey
     * @param \Plumrocket\Base\Model\Extension\Authorization\Status\Refresh  $refreshAuthorizationStatus
     * @param \Plumrocket\Base\Model\Extension\Authorization\Message         $authorizationMessage
     * @param \Plumrocket\Base\Model\Extension\Authorization\Factory         $extensionAuthorizationFactory
     * @param \Plumrocket\Base\Model\Extension\Status\Disable                $disableExtension
     */
    public function __construct(
        GetCurrentExtensionName $getCurrentExtensionName,
        ExtensionAuthorizationRepositoryInterface $extensionAuthorizationRepository,
        Manager $messageManager,
        IsPaid $isPaidExtension,
        Key $authorizationKey,
        Update $updateAuthorizationKey,
        Refresh $refreshAuthorizationStatus,
        Message $authorizationMessage,
        Factory $extensionAuthorizationFactory,
        Disable $disableExtension
    ) {
        $this->getCurrentExtensionName = $getCurrentExtensionName;
        $this->extensionAuthorizationRepository = $extensionAuthorizationRepository;
        $this->messageManager = $messageManager;
        $this->isPaidExtension = $isPaidExtension;
        $this->authorizationKey = $authorizationKey;
        $this->updateAuthorizationKey = $updateAuthorizationKey;
        $this->refreshAuthorizationStatus = $refreshAuthorizationStatus;
        $this->authorizationMessage = $authorizationMessage;
        $this->extensionAuthorizationFactory = $extensionAuthorizationFactory;
        $this->disableExtension = $disableExtension;
    }

    /**
     * Predispatch admin action controller
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(Observer $observer)
    {
        try {
            $extensionName = $this->getCurrentExtensionName->execute();
            if ($this->isPaidExtension->execute($extensionName)) {
                try {
                    $authorization = $this->extensionAuthorizationRepository->get($extensionName);
                } catch (NoSuchEntityException $noSuchEntityException) {
                    $authorization = $this->extensionAuthorizationFactory->create($extensionName);
                }

                if (! $this->authorizationKey->get($extensionName)) {
                    $this->updateAuthorizationKey->execute($authorization);
                } elseif (! $authorization->isAuthorized() || ! $authorization->isCached()) {
                    $authorization = $this->refreshAuthorizationStatus->execute($authorization);
                }

                if (! $authorization->isAuthorized()) {
                    $this->disableExtension->execute($authorization->getModuleName());
                    $this->messageManager->addError($this->authorizationMessage->get($authorization));
                }
            }
        } catch (NotFoundException|NoSuchEntityException $e) {
            return;
        }
    }
}
