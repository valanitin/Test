<?php
/**
 * Revered Technologies Pvt. Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   Revered
 * @package    Revered_GuestWishlist
 * @copyright  Copyright (c) Revered Technologies Pvt. Ltd. (http://www.reveredtech.com)
 * @license    http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 */
namespace Revered\GuestWishlist\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            $installer = $setup;
            /**
             * Create table 'wishlist'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('guest_wishlist')
            )->addColumn(
                'wishlist_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Wishlist ID'
            )->addColumn(
                'guest_customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Guest Customer ID'
            )->addColumn(
                'shared',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Sharing flag (0 or 1)'
            )->addColumn(
                'sharing_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                32,
                [],
                'Sharing encrypted code'
            )->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Last updated date'
            )->addIndex(
                $installer->getIdxName('guest_wishlist', 'shared'),
                'shared'
            )->addIndex(
                $installer->getIdxName(
                    'guest_wishlist',
                    'guest_customer_id',
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                'guest_customer_id',
                ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
            )->setComment(
                'Guest Wishlist main Table'
            );
            $installer->getConnection()->createTable($table);

            /**
             * Create table 'wishlist_item'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('guest_wishlist_item')
            )->addColumn(
                'wishlist_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Wishlist item ID'
            )->addColumn(
                'wishlist_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Wishlist ID'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Store ID'
            )->addColumn(
                'added_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Add date and time'
            )->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Short description of wish list item'
            )->addColumn(
                'qty',
                \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                '12,4',
                ['nullable' => false],
                'Qty'
            )->addIndex(
                $installer->getIdxName('guest_wishlist_item', 'wishlist_id'),
                'wishlist_id'
            )->addForeignKey(
                $installer->getFkName('guest_wishlist_item', 'wishlist_id', 'guest_wishlist', 'wishlist_id'),
                'wishlist_id',
                $installer->getTable('guest_wishlist'),
                'wishlist_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addIndex(
                $installer->getIdxName('guest_wishlist_item', 'product_id'),
                'product_id'
            )->addForeignKey(
                $installer->getFkName('guest_wishlist_item', 'product_id', 'catalog_product_entity', 'entity_id'),
                'product_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addIndex(
                $installer->getIdxName('guest_wishlist_item', 'store_id'),
                'store_id'
            )->addForeignKey(
                $installer->getFkName('guest_wishlist_item', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )->setComment(
                'Guest Wishlist items'
            );
            $installer->getConnection()->createTable($table);

            /**
             * Create table 'wishlist_item_option'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('guest_wishlist_item_option')
            )->addColumn(
                'option_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Option Id'
            )->addColumn(
                'wishlist_item_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Wishlist Item Id'
            )->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Product Id'
            )->addColumn(
                'code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Code'
            )->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => true],
                'Value'
            )->addForeignKey(
                $installer->getFkName('guest_wishlist_item_option', 'wishlist_item_id', 'guest_wishlist_item', 'wishlist_item_id'),
                'wishlist_item_id',
                $installer->getTable('guest_wishlist_item'),
                'wishlist_item_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Guest Wishlist Item Option Table'
            );
            $installer->getConnection()->createTable($table);
        }
        $setup->endSetup();
    }
}
