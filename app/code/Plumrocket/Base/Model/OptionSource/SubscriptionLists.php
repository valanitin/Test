<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\OptionSource;

/**
 * Retrieve list of subscription lists (categories)
 *
 * @since 2.4.1
 */
class SubscriptionLists extends AbstractSource
{
    public const PROMOTIONS_LIST = 'promotions';
    public const ANNOUNCEMENTS_LIST = 'announcements';
    public const PRODUCT_UPDATES_LIST = 'product_updates';

    /**
     * @inheritDoc
     */
    public function toOptionHash(): array
    {
        return [
            self::PRODUCT_UPDATES_LIST => 'Product Updates',
            self::ANNOUNCEMENTS_LIST   => 'Announcements',
            self::PROMOTIONS_LIST      => 'Promotions',
        ];
    }
}
