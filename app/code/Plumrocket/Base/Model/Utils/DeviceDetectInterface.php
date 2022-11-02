<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Utils;

use Magento\Framework\App\RequestInterface;

/**
 * @since 2.5.0
 */
interface DeviceDetectInterface
{
    /**
     * Check whether current device is mobile
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function isMobile(RequestInterface $request): bool;

    /**
     * Check whether current device is tablet
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function isTablet(RequestInterface $request): bool;
}
