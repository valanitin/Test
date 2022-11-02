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

namespace Plumrocket\Base\Api;

/**
 * Allow check module status and disable it
 *
 * @since 2.3.7
 */
interface ProductStatusManagerInterface
{
    /**
     * @param string $moduleName can be either "Plumrocket_SocialLoginFree" or "SocialLoginFree"
     * @return bool
     */
    public function isEnabled(string $moduleName): bool;

    /**
     * @param string $moduleName can be either "Plumrocket_SocialLoginFree" or "SocialLoginFree"
     * @return bool
     */
    public function disable(string $moduleName): bool;
}
