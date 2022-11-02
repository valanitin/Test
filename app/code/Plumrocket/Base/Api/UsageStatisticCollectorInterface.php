<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api;

/**
 * Collect usage statistic from all the modules that give it
 *
 * @since 2.3.0
 */
interface UsageStatisticCollectorInterface
{
    /**
     * @return array
     */
    public function collect(): array;
}
