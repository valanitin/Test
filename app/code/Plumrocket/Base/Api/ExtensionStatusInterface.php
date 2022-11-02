<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Api;

/**
 * Allow easily retrieve status of Plumrocket extension
 *
 * @since 2.4.1
 */
interface ExtensionStatusInterface
{
    /**
     * Not installed in magento
     */
    public const NOT_INSTALLED = 0;

    /**
     * Installed but disabled from CLI
     */
    public const DISABLED_FROM_CLI = 3;

    /**
     * Installed but disabled in system config
     */
    public const DISABLED = 1;

    /**
     * Installed, enabled in CLI and system config
     */
    public const ENABLED = 2;

    /**
     * Check whether extension is installed, enabled in CLI and in Store -> Configuration
     *
     * @param string $moduleName either Plumrocket_SocialLoginFree or SocialLoginFree
     * @return bool
     */
    public function isEnabled(string $moduleName): bool;

    /**
     * Check whether module is disabled in Store -> Configuration
     *
     * @param string $moduleName either Plumrocket_SocialLoginFree or SocialLoginFree
     * @return bool
     */
    public function isDisabled(string $moduleName): bool;

    /**
     * Check whether extension is disabled from cli
     *
     * @param string $moduleName either Plumrocket_SocialLoginFree or SocialLoginFree
     * @return bool
     */
    public function isDisabledFromCli(string $moduleName): bool;

    /**
     * Check whether extension is not installed
     *
     * @param string $moduleName either Plumrocket_SocialLoginFree or SocialLoginFree
     * @return bool
     */
    public function isNotInstalled(string $moduleName): bool;
}
