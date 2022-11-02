<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api;

use Plumrocket\Base\Api\Data\ExtensionInformationInterface;

/**
 * Allow easily retrieve information about Plumrocket extension
 *
 * @since 2.3.0
 */
interface GetExtensionInformationInterface
{
    /**
     * @param string $moduleName can be either "Plumrocket_SocialLoginFree" or "SocialLoginFree"
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function execute(string $moduleName): ExtensionInformationInterface;
}
