<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Statistic\Usage;

/**
 * @since 2.3.0
 */
interface StatusInterface
{
    /**
     * @return bool
     */
    public function check(): bool;

    /**
     * @return \Plumrocket\Base\Model\Statistic\Usage\StatusInterface
     */
    public function switchToCollect(): StatusInterface;

    /**
     * @return \Plumrocket\Base\Model\Statistic\Usage\StatusInterface
     */
    public function switchToMiss(): StatusInterface;
}
