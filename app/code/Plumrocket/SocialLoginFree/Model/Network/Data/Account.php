<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network\Data;

use Magento\Framework\DataObject;
use Plumrocket\SocialLoginFree\Api\Data\NetworkAccountInterface;

/**
 * @since 3.2.0
 */
class Account extends DataObject implements NetworkAccountInterface
{
    /**
     * @inheritDoc
     */
    public function getNetworkCode(): string
    {
        return (string) $this->_getData('network_code');
    }

    /**
     * @inheritDoc
     */
    public function getId(): string
    {
        return (string) $this->_getData('user_id');
    }

    /**
     * @inheritDoc
     */
    public function getEmail(): string
    {
        return (string) $this->_getData('email');
    }

    /**
     * @inheritDoc
     */
    public function getPhotoUrl(): string
    {
        return (string) $this->_getData('photo');
    }

    /**
     * @inheritDoc
     */
    public function getCustomerData(): array
    {
        $customerData = $this->getData();
        unset($customerData['network_code'], $customerData['user_id'], $customerData['photo']);
        return $customerData;
    }
}
