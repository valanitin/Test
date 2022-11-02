<?php
/*
 * Magentizer_Pricing

 * @category   SussexDev
 * @package    Magentizer_Pricing
 * @copyright  Copyright (c) 2019 Scott Parsons
 * @license    https://github.com/ScottParsons/module-sampleuicomponent/blob/master/LICENSE.md
 * @version    1.1.2
 */
namespace Magentizer\Pricing\Setup;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;
use Magento\Config\Model\ResourceModel\Config\Data;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;

class Uninstall implements UninstallInterface
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Data
     */
    protected $configResource;

    /**
     * @param CollectionFactory $collectionFactory
     * @param Data $configResource
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        Data $configResource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->configResource    = $configResource;
    }

    /**
     * Drop sample table
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if ($setup->tableExists('magentizer_erp_pricing')) {
            $setup->getConnection()->dropTable('magentizer_erp_pricing');
        }
        $collection = $this->collectionFactory->create()
            ->addPathFilter('sussexdev_sample');
        foreach ($collection as $config) {
            $this->deleteConfig($config);
        }
    }

    /**
     * Delete system config
     *
     * @param AbstractModel $config
     */
    protected function deleteConfig(AbstractModel $config)
    {
        $this->configResource->delete($config);
    }
}
