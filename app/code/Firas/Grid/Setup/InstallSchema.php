<?php
/**
 * Grid Schema Setup.
 * @category  Firas
 * @package   Firas_Grid
 * @author    Firas
 * @copyright Copyright (c) 2010-2016 Firas Software Private Limited (https://firas.com)
 * @license   https://store.firas.com/license.html
 */

namespace Firas\Grid\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;

        $installer->startSetup();

        /*
         * Create table 'firas_ship_cost'
         */

        $table = $installer->getConnection()->newTable(
            $installer->getTable('firas_ship_cost')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Grid Record Id'
        )->addColumn(
            'shipping_country_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Shipping Country Code'
        )->addColumn(
            'shipping_country_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Shipping Country Name'
        )->addColumn(
            'shipping_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Shipping Price'
        )->addColumn(
            'shipping_price_currency',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Shipping Price Currency'
        )->setComment(
            'Row Data Table'
        );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
