<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/ End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\ResourceModel\Request;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Plumrocket\SocialLoginFree\Model\DataPrivacy\Facebook\DeletionRequest;
use Plumrocket\SocialLoginFree\Model\ResourceModel\Request;

/**
 * @since 3.1.0
 */
class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(DeletionRequest::class, Request::class);
    }
}
