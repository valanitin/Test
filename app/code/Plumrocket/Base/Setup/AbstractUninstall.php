<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

namespace Plumrocket\Base\Setup;

use Magento\Cms\Model\BlockFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\Module\ModuleList\Loader;
use Magento\Framework\Module\Setup\Context as SetupContext;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\ShellInterface;
use Psr\Log\LoggerInterface;

abstract class AbstractUninstall implements UninstallInterface
{
    /**
     * @var string
     */
    protected $_configSectionId;

    /**
     * @var array
     */
    protected $_attributes = [];

    /**
     * @var array
     */
    protected $_tables = [];

    /**
     * List of entity types to uninstall
     *
     * @var int[]|string[]
     */
    protected $eavEntityTypes = [];

    /**
     * @var array
     */
    protected $_tablesFields = [];

    /**
     * @var array
     */
    protected $_pathes = [];

    /**
     * @var array
     */
    protected $_cmsBlocks = [];

    /**
     * @var SetupContext
     */
    protected $context;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $_cmsBlockFactory;

    /**
     * @var \Magento\Framework\ShellInterface
     */
    private $shell;

    /**
     * @var \Magento\Framework\App\DeploymentConfig\Writer
     */
    private $deploymentConfigWriter;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var \Magento\Framework\Module\ModuleList\Loader
     */
    private $moduleListLoader;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * AbstractUninstall constructor.
     *
     * @param \Magento\Framework\Module\Setup\Context        $context
     * @param \Magento\Eav\Setup\EavSetupFactory             $eavSetupFactory
     * @param \Magento\Cms\Model\BlockFactory                $cmsBlockFactory
     * @param \Magento\Framework\ShellInterface              $shell
     * @param \Magento\Framework\App\DeploymentConfig        $deploymentConfig
     * @param \Magento\Framework\App\DeploymentConfig\Writer $deploymentConfigWriter
     * @param \Magento\Framework\Module\ModuleList\Loader    $moduleListLoader
     * @param \Psr\Log\LoggerInterface                       $logger
     */
    public function __construct(
        SetupContext $context,
        EavSetupFactory $eavSetupFactory,
        BlockFactory $cmsBlockFactory,
        ShellInterface $shell,
        DeploymentConfig $deploymentConfig,
        DeploymentConfig\Writer $deploymentConfigWriter,
        Loader $moduleListLoader,
        LoggerInterface $logger
    ) {
        $this->context          = $context;
        $this->eavSetupFactory  = $eavSetupFactory;
        $this->_cmsBlockFactory = $cmsBlockFactory;
        $this->shell = $shell;
        $this->deploymentConfig = $deploymentConfig;
        $this->deploymentConfigWriter = $deploymentConfigWriter;
        $this->moduleListLoader = $moduleListLoader;
        $this->logger = $logger;
    }

    /**
     * Uninstall
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $moduleName = $this->getModuleName();
        $dataSetup = new \Magento\Setup\Module\DataSetup($this->context);
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $dataSetup]);

        //remove attribute
        foreach ($this->_attributes as $entityTypeName => $attributeNames) {
            $entityTypeId = $eavSetup->getEntityTypeId($entityTypeName);
            foreach ($attributeNames as $attributeName) {
                $eavSetup->removeAttribute($entityTypeId, $attributeName);
            }
        }

        //remove tables
        foreach ($this->_tables as $_tableName) {
            $_tableName = $setup->getTable($_tableName);
            $setup->getConnection()->dropTable($_tableName);
        }

        //remove Eav Entity Type
        foreach ($this->eavEntityTypes as $eavEntityType) {
            $eavSetup->removeEntityType($eavEntityType);
        }

        //remove tables fields
        foreach ($this->_tablesFields as $_tableName => $_fields) {
            $_tableName = $setup->getTable($_tableName);
            foreach ($_fields as $_field) {
                try {
                    $setup->getConnection()->dropColumn($_tableName, $_field);
                } catch (\Exception $e) {
                    $this->logger->critical($e->getMessage());
                }
            }
        }

        $this->removeStaticBlocks();

        $this->removeConfigData($setup, $moduleName);

        $this->removeModuleFromConfigFile($moduleName);

        $this->shell->execute('rm -rf ' . BP . '/var/cache');

        foreach ($this->_pathes as $path) {
            $this->shell->execute('rm -rf ' . BP . $path);
        }

        $setup->endSetup();
    }

    /**
     * Get module name.
     *
     * @return string
     */
    public function getModuleName()
    {
        $className = get_class($this);
        $namespace = substr(
            $className,
            0,
            strpos($className, '\\' . 'Setup')
        );

        return str_replace('\\', '_', $namespace);
    }

    /**
     * Remove static blocks.
     *
     * @return void
     * @throws \Exception
     */
    private function removeStaticBlocks(): void
    {
        foreach ($this->_cmsBlocks as $_identifier) {
            /** @var \Magento\Cms\Model\Block $cmsBlock */
            $cmsBlock = $this->_cmsBlockFactory->create();
            $cmsBlock->load($_identifier);
            if ($cmsBlock->getId()) {
                $cmsBlock->delete();
            }
        }
    }

    /**
     * Remove configs.
     *
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param string                                        $moduleName
     * @return void
     */
    private function removeConfigData(SchemaSetupInterface $setup, string $moduleName): void
    {
        if ($this->_configSectionId) {
            $setup->getConnection()->delete(
                $setup->getTable('core_config_data'),
                ['path LIKE ?' => $this->_configSectionId . '/%']
            );

            $setup->getConnection()->delete(
                $setup->getTable('setup_module'),
                ['module = ?' => $moduleName]
            );
        }
    }

    /**
     * Remove from config.php
     *
     * @param string $moduleName
     * @return void
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function removeModuleFromConfigFile(string $moduleName): void
    {
        $configuredModules = $this->deploymentConfig->getConfigData(
            ConfigOptionsListConstants::KEY_MODULES
        );
        $existingModules = $this->moduleListLoader->load([$moduleName]);
        $newModules = [];
        foreach (array_keys($existingModules) as $module) {
            $newModules[$module] = $configuredModules[$module] ?? 0;
        }

        $this->deploymentConfigWriter->saveConfig(
            [
                ConfigFilePool::APP_CONFIG => [ConfigOptionsListConstants::KEY_MODULES => $newModules]
            ],
            true
        );
    }
}
