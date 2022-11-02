<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\System\Config;

/**
 * Retrieve list of subscription lists (categories)
 *
 * @since 2.3.0
 */
class SubscriptionLists extends AbstractSource
{
    const PROMOTIONS_LIST = 'promotions';
    const ANNOUNCEMENTS_LIST = 'announcements';
    const PRODUCT_UPDATES_LIST = 'product_updates';

    /**
     * {@inheritDoc}
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
