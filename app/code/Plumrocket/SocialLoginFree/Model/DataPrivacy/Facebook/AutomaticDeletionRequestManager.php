<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/ End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook;

use Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\DeletionRequestFactory;
use Plumrocket\SocialLoginFree\Model\ResourceModel\Account\CollectionFactory;
use Plumrocket\SocialLoginFree\Model\ResourceModel\Request;

/**
 * @since 3.1.0
 */
class AutomaticDeletionRequestManager
{
    /**
     * @var DeletionRequest
     */
    private $deletionRequestFactory;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\ResourceModel\Request
     */
    private $requestResource;

    /**
     * @var \Plumrocket\SocialLoginFree\Model\ResourceModel\Account\CollectionFactory
     */
    private $accountCollectionFactory;

    /**
     * @param \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\DeletionRequestFactory $deletionRequestFactory
     * @param \Plumrocket\SocialLoginFree\Model\ResourceModel\Request                       $requestResource
     * @param \Plumrocket\SocialLoginFree\Model\ResourceModel\Account\CollectionFactory     $accountCollectionFactory
     */
    public function __construct(
        DeletionRequestFactory $deletionRequestFactory,
        Request $requestResource,
        CollectionFactory $accountCollectionFactory
    ) {
        $this->deletionRequestFactory = $deletionRequestFactory;
        $this->requestResource = $requestResource;
        $this->accountCollectionFactory = $accountCollectionFactory;
    }

    /**
     * @param string $confirmationCode
     * @return string
     */
    public function getStatus(string $confirmationCode): string
    {
        /** @var \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\DeletionRequest $deletionModel */
        $deletionModel = $this->deletionRequestFactory->create();
        $this->requestResource->load($deletionModel, $confirmationCode, 'confirmation_code');

        return $deletionModel->getStatus();
    }

    /**
     * @param string $userId
     * @return \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\DeletionRequest
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function register(string $userId): DeletionRequest
    {
        /** @var \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\DeletionRequest $deletionModel */
        $deletionModel = $this->deletionRequestFactory->create();

        $deletionModel->setUserId($userId);

        if (! $deletionModel->getStatus()) {
            $this->removeLinks($deletionModel);
            $deletionModel->setStatus(DeletionRequest::COMPLETE);
        }

        $deletionModel->generateConfirmationCode();
        $this->requestResource->save($deletionModel);

        return $deletionModel;
    }

    /**
     * @param \Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\DeletionRequest $deletionRequest
     */
    public function removeLinks(DeletionRequest $deletionRequest): void
    {
        /** @var \Plumrocket\SocialLoginFree\Model\ResourceModel\Account\Collection $accountCollection */
        $accountCollection = $this->accountCollectionFactory->create();

        $accountCollection->addFieldToFilter('type', 'facebook');
        $accountCollection->addFieldToFilter('user_id', $deletionRequest->getUserId());

        /** @var \Plumrocket\SocialLoginFree\Model\Account $socialAccountLink */
        foreach ($accountCollection->getItems() as $socialAccountLink) {
            $socialAccountLink->delete();
        }
    }
}
