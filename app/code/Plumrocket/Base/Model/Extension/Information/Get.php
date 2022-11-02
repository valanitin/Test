<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Information;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Plumrocket\Base\Api\Data\ExtensionInformationInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Model\Extension\GetModuleName;
use Plumrocket\Base\Model\Extension\Information\ContainerFactory;

/**
 * @since 2.3.0
 */
class Get implements GetExtensionInformationInterface
{
    /**
     * @var string[]
     */
    private $services = [
        'Base',
        'Token',
        'AmpEmailApi',
    ];

    /**
     * @var \Plumrocket\Base\Api\ModuleInformationInterface[]
     */
    private $extensions;

    /**
     * @var \Plumrocket\Base\Model\Extension\Information\ContainerFactory
     */
    private $informationContainerFactory;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getExtensionName;

    /**
     * @var \Magento\Framework\Config\DataInterface
     */
    private $extensionsConfig;

    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * @param \Plumrocket\Base\Model\Extension\Information\ContainerFactory $informationContainerFactory
     * @param \Plumrocket\Base\Model\Extension\GetModuleName                $getExtensionName
     * @param \Magento\Framework\Config\DataInterface                       $extensionsConfig
     * @param \Magento\Framework\Module\ModuleListInterface                 $moduleList
     * @param array                                                         $extensions
     */
    public function __construct(
        ContainerFactory $informationContainerFactory,
        GetModuleName $getExtensionName,
        DataInterface $extensionsConfig,
        ModuleListInterface $moduleList,
        array $extensions = []
    ) {
        $this->extensions = $extensions;
        $this->informationContainerFactory = $informationContainerFactory;
        $this->getExtensionName = $getExtensionName;
        $this->extensionsConfig = $extensionsConfig;
        $this->moduleList = $moduleList;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $moduleName): ExtensionInformationInterface
    {
        $moduleName = $this->getExtensionName->execute($moduleName);

        if (! isset($this->extensions[$moduleName])) {
            /** @var \Plumrocket\Base\Model\Extension\Information\Container $infoContainer */
            $infoContainer = $this->informationContainerFactory->create();
            $infoContainer->setModuleName($moduleName);

            if ($extensionConfig = $this->extensionsConfig->get($moduleName)) {
                $infoContainer->setIsService($extensionConfig[ExtensionInformationInterface::FIELD_IS_SERVICE])
                    ->setModuleName($extensionConfig['name'])
                    ->setTitle($extensionConfig['title'])
                    ->setConfigSection($extensionConfig['config_section'])
                    ->setIsEnabledFieldConfigPath($extensionConfig['is_enabled_path'])
                    ->setDocumentationLink($extensionConfig['documentation'] ?: $extensionConfig['wiki'])
                    ->setUrl($extensionConfig[ExtensionInformationInterface::FIELD_URL] ?? '')
                    ->setMarketplaceUrl(
                        $extensionConfig[ExtensionInformationInterface::FIELD_MARKETPLACE_URL] ?? ''
                    );
            } else {
                if ($this->moduleList->has("Plumrocket_$moduleName")) {
                    $infoContainer->setIsService(in_array($moduleName, $this->services, true));
                }
            }

            $this->extensions[$moduleName] = $infoContainer;
        }

        return $this->extensions[$moduleName];
    }
}
