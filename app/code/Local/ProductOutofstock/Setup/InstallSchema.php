<?php

namespace Local\ProductOutofstock\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     *
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        if (!$installer->tableExists('outofstock_detail')) {
            $table = $installer->getConnection()->newTable(
                $installer->getTable('outofstock_detail')
            )->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'nullable' => false,
                    'primary' => true,
                    'unsigned' => true,
                ],
                'ID'
            )->addColumn(
                'product_id',
                Table::TYPE_INTEGER,
                20,
                ['nullable' => false],
                'Product Id'
            )->addColumn(
                'outofstock_date',
                Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true, 'default' => Table::TIMESTAMP_INIT],
                'Out of stock Date'
            )->setComment('Out of stock Detail Table');

            $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();
    }
}
