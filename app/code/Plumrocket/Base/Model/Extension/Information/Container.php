<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Information;

use Magento\Framework\DataObject;
use Plumrocket\Base\Api\Data\ExtensionInformationInterface;
use Plumrocket\Base\Api\GetModuleVersionInterface;

/**
 * Container for information about extension that parsed from "pr_extensions.xml"
 *
 * Can be used for old extension which has not xml file
 * and does not realize \Plumrocket\Base\Api\ModuleInformationInterface
 *
 * @since 2.5.0
 */
class Container extends DataObject implements ExtensionInformationInterface
{
    /**
     * @var \Plumrocket\Base\Api\GetModuleVersionInterface
     */
    private $getModuleVersion;

    /**
     * @param \Plumrocket\Base\Api\GetModuleVersionInterface $getModuleVersion
     * @param array                                          $data
     */
    public function __construct(
        GetModuleVersionInterface $getModuleVersion,
        array $data = []
    ) {
        parent::__construct($data);
        $this->getModuleVersion = $getModuleVersion;
    }

    /**
     * @inheritDoc
     */
    public function isService(): bool
    {
        return (bool) $this->_getData(self::FIELD_IS_SERVICE);
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string) $this->_getData(self::FIELD_TITLE);
    }

    /**
     * @inheritDoc
     */
    public function getDocumentationLink(): string
    {
        return (string) ($this->_getData(self::FIELD_DOCUMENTATION) ?: $this->_getData(self::FIELD_WIKI));
    }

    /**
     * @inheritDoc
     */
    public function getWikiLink(): string
    {
        return $this->getDocumentationLink();
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): string
    {
        return (string) $this->_getData(self::FIELD_URL);
    }

    /**
     * @inheritDoc
     */
    public function getMarketplaceUrl(): string
    {
        return (string) $this->_getData(self::FIELD_MARKETPLACE_URL);
    }

    /**
     * @inheritDoc
     */
    public function getConfigSection(): string
    {
        return (string) $this->_getData(self::FIELD_CONFIG_SECTION);
    }

    /**
     * @inheritDoc
     */
    public function getIsEnabledFieldConfigPath(): string
    {
        return (string) $this->_getData(self::FIELD_IS_ENABLED_PATH);
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return (string) $this->_getData(self::FIELD_MODULE_NAME);
    }

    /**
     * @inheritDoc
     */
    public function getVendorAndModuleName(): string
    {
        return "Plumrocket_{$this->getModuleName()}";
    }

    /**
     * @inheritDoc
     */
    public function getInstalledVersion(): string
    {
        return $this->getModuleVersion->execute($this->getVendorAndModuleName());
    }

    /**
     * @inheritDoc
     */
    public function setIsService(bool $isService): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_IS_SERVICE, $isService);
    }

    /**
     * @inheritDoc
     */
    public function setTitle(string $title): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_TITLE, $title);
    }

    /**
     * @inheritDoc
     */
    public function setConfigSection(string $configSection): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_CONFIG_SECTION, $configSection);
    }

    /**
     * @inheritDoc
     */
    public function setDocumentationLink(string $documentationLink): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_DOCUMENTATION, $documentationLink);
    }

    /**
     * @inheritDoc
     */
    public function setWikiLink(string $wikiLink): ExtensionInformationInterface
    {
        return $this->setDocumentationLink($wikiLink);
    }

    /**
     * @inheritDoc
     */
    public function setUrl(string $extensionUrl): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_URL, $extensionUrl);
    }

    /**
     * @inheritDoc
     */
    public function setMarketplaceUrl(string $extensionMarketplaceUrl): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_MARKETPLACE_URL, $extensionMarketplaceUrl);
    }

    /**
     * @inheritDoc
     */
    public function setModuleName(string $moduleName): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_MODULE_NAME, $moduleName);
    }

    /**
     * @inheritDoc
     */
    public function setIsEnabledFieldConfigPath(string $configPath): ExtensionInformationInterface
    {
        return $this->setData(self::FIELD_IS_ENABLED_PATH, $configPath);
    }
}
