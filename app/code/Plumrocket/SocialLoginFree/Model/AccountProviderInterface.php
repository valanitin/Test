<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model;

interface AccountProviderInterface
{
    /**
     * @param string $type
     * @return \Plumrocket\SocialLoginFree\Model\Account
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByType($type);

    /**
     * @param string $type
     * @return \Plumrocket\SocialLoginFree\Model\Account
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createByType($type);
}
