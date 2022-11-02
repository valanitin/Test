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

namespace Plumrocket\Base\Model\Utils;

/**
 * Get name "SocialLoginFree" from "Plumrocket_SocialLoginFree" or "SocialLoginFree"
 *
 * @since 2.3.7
 */
class GetExtensionName
{
    public function execute(string $maybeModuleFullName): string
    {
        if (false === strpos($maybeModuleFullName, '_')) {
            return $maybeModuleFullName;
        }

        return explode('_', $maybeModuleFullName)[1];
    }
}
