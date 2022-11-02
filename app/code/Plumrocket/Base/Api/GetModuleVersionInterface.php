<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api;

/**
 * @since 2.1.6
 */
interface GetModuleVersionInterface
{

    /**
     * Get module version from composer.json.
     *
     * @param string $moduleName
     * @return string
     */
    public function execute($moduleName);
}
