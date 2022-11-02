<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\ResourceModel\Account;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Plumrocket\SocialLoginFree\Model\Account;
use Plumrocket\SocialLoginFree\Model\ResourceModel\Account as ResourceAccount;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        parent::_construct();
        $this->_init(Account::class, ResourceAccount::class);
    }
}
