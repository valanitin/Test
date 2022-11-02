<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Api;

use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;

/**
 * @since 2.5.0
 */
interface ExtensionAuthorizationRepositoryInterface
{
    /**
     * Get extension authorization by module name.
     *
     * @param string $moduleName
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(string $moduleName): ExtensionAuthorizationInterface;

    /**
     * Save extension authorization.
     *
     * @param \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface $extension
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface
     */
    public function save(ExtensionAuthorizationInterface $extension): ExtensionAuthorizationInterface;
}
