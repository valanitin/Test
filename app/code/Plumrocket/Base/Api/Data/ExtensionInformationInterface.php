<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Api\Data;

/**
 * Allows easily retrieve information about plumrocket module
 *
 * @since 2.5.0
 */
interface ExtensionInformationInterface
{
    public const FIELD_IS_SERVICE = 'is_service';
    public const FIELD_TITLE = 'title';
    public const FIELD_DOCUMENTATION = 'documentation';
    /** @deprecated since 2.6.0 */
    public const FIELD_WIKI = 'wiki';
    public const FIELD_CONFIG_SECTION = 'config_section';
    public const FIELD_IS_ENABLED_PATH = 'is_enabled_path';
    public const FIELD_MODULE_NAME = 'module_name';
    public const FIELD_URL = 'url';
    public const FIELD_MARKETPLACE_URL = 'marketplace_url';

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
    public function getTitle(): string;

    /**
     * Retrieve section in system settings, e.g. "pr_social_login"
     *
     * @return string
     */
    public function getConfigSection(): string;

    /**
     * Retrieve section in system settings, e.g. "pr_social_login/general/enabled"
     *
     * @return string
     */
    public function getIsEnabledFieldConfigPath(): string;

    /**
     * Link to documentation
     *
     * @return string
     * @since 2.6.0
     */
    public function getDocumentationLink(): string;

    /**
     * Link to wiki
     *
     * @return string
     * @deprecated since 2.6.0
     * @see getDocumentationLink
     */
    public function getWikiLink(): string;

    /**
     * Get link to extension store page.
     *
     * @return string
     * @since 2.8.0
     */
    public function getUrl(): string;

    /**
     * Get link to extension marketplace page.
     *
     * @return string
     * @since 2.8.0
     */
    public function getMarketplaceUrl(): string;

    /**
     * Retrieve full name of module, e.g. SocialLoginFree
     *
     * @return string
     */
    public function getModuleName(): string;

    /**
     * Retrieve full name of module, e.g. Plumrocket_SocialLoginFree
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

    /**
     * @param bool $isService
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setIsService(bool $isService): ExtensionInformationInterface;

    /**
     * @param string $title
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setTitle(string $title): ExtensionInformationInterface;

    /**
     * @param string $configSection
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setConfigSection(string $configSection): ExtensionInformationInterface;

    /**
     * Set link to extension documentation.
     *
     * @param string $documentationLink
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     * @since 2.6.0
     */
    public function setDocumentationLink(string $documentationLink): ExtensionInformationInterface;

    /**
     * @param string $wikiLink
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     * @deprecated since 2.6.0
     * @see setDocumentationLink
     */
    public function setWikiLink(string $wikiLink): ExtensionInformationInterface;

    /**
     * Set link to extension store page.
     *
     * @param string $extensionUrl
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     * @since 2.8.0
     */
    public function setUrl(string $extensionUrl): ExtensionInformationInterface;

    /**
     * Set link to extension marketplace page.
     *
     * @param string $extensionMarketplaceUrl
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     * @since 2.8.0
     */
    public function setMarketplaceUrl(string $extensionMarketplaceUrl): ExtensionInformationInterface;

    /**
     * @param string $moduleName
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setModuleName(string $moduleName): ExtensionInformationInterface;

    /**
     * @param string $configPath
     * @return \Plumrocket\Base\Api\Data\ExtensionInformationInterface
     */
    public function setIsEnabledFieldConfigPath(string $configPath): ExtensionInformationInterface;
}
