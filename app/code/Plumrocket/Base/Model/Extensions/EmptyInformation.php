<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extensions;

use Magento\Framework\DataObject;
use Plumrocket\Base\Api\GetModuleVersionInterface;

/**
 * Container for information about extension
 * Use in case when module hasn't \Plumrocket\Base\Api\ModuleInformationInterface realise
 * @since 2.3.0
 * @deprecated 2.5.0
 */
class EmptyInformation extends DataObject
{
    /**
     * @var \Plumrocket\Base\Api\GetModuleVersionInterface
     */
    private $getModuleVersion;

    /**
     * EmptyInformation constructor.
     *
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
        return (bool) $this->_getData('is_service');
    }

    /**
     * @inheritDoc
     */
    public function getTitle(): string
    {
        return (string) $this->_getData('title');
    }

    /**
     * @inheritDoc
     */
    public function getWikiLink(): string
    {
        return (string) $this->_getData('wiki');
    }

    /**
     * @inheritDoc
     */
    public function getConfigSection(): string
    {
        return (string) $this->_getData('config_section');
    }

    /**
     * @inheritDoc
     */
    public function getIsEnabledFieldConfigPath(): string
    {
        return (string) $this->_getData('is_enabled_path');
    }

    /**
     * @inheritDoc
     */
    public function getModuleName(): string
    {
        return (string) $this->_getData('module_name');
    }

    /**
     * @inheritDoc
     */
    public function getVendorAndModuleName(): string
    {
        return (string) $this->_getData('full_module_name') ?: 'Plumrocket_' . $this->getModuleName();
    }

    /**
     * @inheritDoc
     */
    public function getInstalledVersion(): string
    {
        return $this->getModuleVersion->execute($this->getVendorAndModuleName());
    }

    /**
     * @param bool $isService
     * @return \Plumrocket\Base\Model\Extensions\EmptyInformation
     */
    public function setIsService(bool $isService): EmptyInformation
    {
        return $this->setData('is_service', $isService);
    }

    /**
     * @param string $title
     * @return \Plumrocket\Base\Model\Extensions\EmptyInformation
     */
    public function setTitle(string $title): EmptyInformation
    {
        return $this->setData('title', $title);
    }

    /**
     * @param string $configSection
     * @return \Plumrocket\Base\Model\Extensions\EmptyInformation
     */
    public function setConfigSection(string $configSection): EmptyInformation
    {
        return $this->setData('config_section', $configSection);
    }

    /**
     * @param string $wikiLink
     * @return \Plumrocket\Base\Model\Extensions\EmptyInformation
     */
    public function setWikiLink(string $wikiLink): EmptyInformation
    {
        return $this->setData('wiki', $wikiLink);
    }

    /**
     * @param string $moduleName
     * @return \Plumrocket\Base\Model\Extensions\EmptyInformation
     */
    public function setVendorAndModuleName(string $moduleName): EmptyInformation
    {
        return $this->setData('full_module_name', $moduleName);
    }

    /**
     * @param string $moduleName
     * @return \Plumrocket\Base\Model\Extensions\EmptyInformation
     */
    public function setModuleName(string $moduleName): EmptyInformation
    {
        return $this->setData('module_name', $moduleName);
    }

    /**
     * @param string $configPath
     * @return \Plumrocket\Base\Model\Extensions\EmptyInformation
     */
    public function setIsEnabledFieldConfigPath(string $configPath): EmptyInformation
    {
        return $this->setData('is_enabled_path', $configPath);
    }
}
