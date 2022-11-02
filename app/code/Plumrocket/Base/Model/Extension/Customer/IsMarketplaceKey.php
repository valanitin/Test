<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Customer;

/**
 * Check whether customer key is marketplace
 *
 * @since 2.5.0
 */
class IsMarketplaceKey
{
    const MARKETPLACE_CUSTOMER_KEY = '532416486b540ea2a1e50c4070b671611b44f52718';

    public function execute(string $customerKey): bool
    {
        return self::MARKETPLACE_CUSTOMER_KEY === $customerKey;
    }
}
