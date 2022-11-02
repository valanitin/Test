<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/ End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook;

use Magento\Framework\Model\AbstractModel;
use Plumrocket\SocialLoginFree\Model\ResourceModel\Request;

/**
 * @since 3.1.0
 */
class DeletionRequest extends AbstractModel
{
    public const COMPLETE = 'complete';

    protected function _construct()
    {
        $this->_init(Request::class);
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return (string) $this->_getData('user_id');
    }

    /**
     * @param string $userId
     * @return DeletionRequest
     */
    public function setUserId(string $userId): DeletionRequest
    {
        return $this->setData('user_id', $userId);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return (string) $this->_getData('status');
    }

    /**
     * @param string $status
     * @return DeletionRequest
     */
    public function setStatus(string $status): DeletionRequest
    {
        return $this->setData('status', $status);
    }

    /**
     * @return string
     */
    public function getConfirmationCode(): string
    {
        return (string) $this->_getData('confirmation_code');
    }

    /**
     * @param string $confirmationCode
     * @return DeletionRequest
     */
    public function setConfirmationCode(string $confirmationCode): DeletionRequest
    {
        return $this->setData('confirmation_code', $confirmationCode);
    }

    /**
     * Generate unique code 23 symbols length
     */
    public function generateConfirmationCode(): void
    {
        // Replace "." via "0" to make code easier to copying
        $this->setConfirmationCode(str_replace('.', '0', uniqid('', true)));
    }
}
