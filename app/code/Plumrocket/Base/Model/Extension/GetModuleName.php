<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension;

/**
 * Get name "SocialLoginFree" from "Plumrocket_SocialLoginFree" or "SocialLoginFree"
 *
 * @since 2.3.7
 */
class GetModuleName
{
    public function execute(string $maybeModuleFullName): string
    {
        if (false === strpos($maybeModuleFullName, '_')) {
            return $maybeModuleFullName;
        }

        return explode('_', $maybeModuleFullName)[1];
    }
}
