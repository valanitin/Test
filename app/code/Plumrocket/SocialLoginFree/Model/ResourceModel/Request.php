<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/ End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * @since 3.1.0
 */
class Request extends AbstractDb
{
    public const MAIN_TABLE = 'plumrocket_sociallogin_deletion';

    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE, 'id');
    }
}
