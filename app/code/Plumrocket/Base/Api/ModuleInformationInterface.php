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
 * Allow easily retrieve information about installed modules
 *
 * @since 2.3.0
 */
interface ModuleInformationInterface
{
    /**
     * Some examples of services - Token, AmpEmailApi
     *
     * @return bool
     */
    public function isService(): bool;

    /**
     * Retrieve name of module, e.g. Twitter & Facebook Login
     *
     * @return string
     */
    public function getOfficialName(): string;

    /**
     * Retrieve section in system settings, e.g. "pr_social_login"
     *
     * @return string
     */
    public function getConfigSection(): string;

    /**
     * Retrieve section in system settings, e.g. "pr_social_login/general/enabled"
     *
     * @since 2.3.7
     * @return string
     */
    public function getIsEnabledFieldConfigPath(): string;

    /**
     * Link to wiki
     *
     * @return string
     */
    public function getWikiLink(): string;

    /**
     * Retrieve full name of module, e.g SocialLoginFree
     *
     * @return string
     */
    public function getModuleName(): string;

    /**
     * Retrieve full name of module, e.g Plumrocket_SocialLoginFree
     *
     * @return string
     */
    public function getVendorAndModuleName(): string;

    /**
     * Retrieve installed version by composer.json
     *
     * @return string
     */
    public function getInstalledVersion(): string;
}
