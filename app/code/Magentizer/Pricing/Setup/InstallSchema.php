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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('magentizer_erp_pricing');

        if (!$installer->tableExists('magentizer_erp_pricing')) {
            $table = $installer->getConnection()
                ->newTable($tableName)
                ->addColumn(
                    'pricing_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'Pricing ID'
                )
                ->addColumn(
                    'pricing_store',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false, 'default' => ''],
                    'Pricing Store'
                )
                ->addColumn(
                    'pricing_sku',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Pricing SKU'
                )
                ->addColumn(
                    'pricing_value',
                    Table::TYPE_DECIMAL,
                    null,
                    ['unsigned' => true, 'nullable' => false, 'length'=> '10,2', 'default' => '0'],
                    'Status'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Created At'
                );
            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
