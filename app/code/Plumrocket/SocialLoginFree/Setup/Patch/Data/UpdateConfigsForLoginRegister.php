<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Plumrocket\SocialLoginFree\Helper\Config;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @since 3.2.0
 */
class UpdateConfigsForLoginRegister implements DataPatchInterface
{
    /**
     * @var \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory
     */
    private $configDataCollectionFactory;

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory $configDataCollectionFactory
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        CollectionFactory $configDataCollectionFactory,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->configDataCollectionFactory = $configDataCollectionFactory;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $path = 'psloginfree/general/enable_for';

        $isConfigSet = $this->configDataCollectionFactory->create()
            ->addFieldToFilter('path', ['eq' => $path])
            ->getSize();

        /** Copy previous config to the new path */
        if (! $isConfigSet) {
            $configurations = [];
            $fields = [
                'login' => 'psloginfree/general/enable_for_login',
                'register' => 'psloginfree/general/enable_for_register'
            ];

            foreach ($fields as $page => $fieldPath) {
                $values = $this->configDataCollectionFactory->create()
                    ->addFieldToFilter('path', ['eq' => $fieldPath])
                    ->getData();

                foreach ($values as $value) {
                    $configurations[$value['scope_id']]['scope'] = $value['scope'];

                    if ($value['value'] == 1) {
                        if (isset($configurations[$value['scope_id']]['value'])) {
                            $configurations[$value['scope_id']]['value'] .= ',' . $page;
                        } else {
                            $configurations[$value['scope_id']]['value'] = $page;
                        }
                    }
                }
            }

            foreach ($configurations as $scopeId => $configuration) {
                $this->configWriter->save($path, $configuration['value'], $configuration['scope'], $scopeId);
            }

            /** Remove hidden buttons */
            /** @var \Magento\Framework\App\Config\Value[] $sortableConfigs */
            $sortableConfigs = $this->configDataCollectionFactory->create()
                ->addFieldToFilter('path', ['eq' => Config::XML_PATH_BUTTON_SORT])->getItems();
            foreach ($sortableConfigs as $sortableConfig) {
                if ($value = $sortableConfig->getValue()) {
                    parse_str($value, $sortParams);
                    if (is_array($sortParams) && !empty($sortParams['hidden'])) {
                        $this->configWriter->delete(
                            $sortableConfig->getPath(),
                            $sortableConfig->getScope(),
                            $sortableConfig->getScopeId()
                        );
                    }
                }
            }
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
