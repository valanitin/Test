<?php
/**
 * A Magento 2 module named Firas/DonationProduct
 * Copyright (C) 2017 Derrick Heesbeen
 *
 * This file is part of Firas/DonationProduct.
 *
 * Firas/DonationProduct is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Firas\DonationProduct\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Firas\DonationProduct\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $tableFirasDonationProductDonations = $setup->getConnection()->newTable(
            $setup->getTable('firas_donations')
        );

        $tableFirasDonationProductDonations->addColumn(
            'donations_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'nullable' => false,
                'primary' => true,
                'unsigned' => true,
            ],
            'Entity ID'
        );

        $tableFirasDonationProductDonations->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            [],
            'name'
        );

        $tableFirasDonationProductDonations->addColumn(
            'sku',
            Table::TYPE_TEXT,
            255,
            [],
            'sku'
        );

        $tableFirasDonationProductDonations->addColumn(
            'order_item_id',
            Table::TYPE_INTEGER,
            null,
            [],
            'order_item_id'
        );

        $tableFirasDonationProductDonations->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            [],
            'order_id'
        );

        $tableFirasDonationProductDonations->addColumn(
            'order_status',
            Table::TYPE_TEXT,
            null,
            [],
            'order_status'
        );

        $tableFirasDonationProductDonations->addColumn(
            'amount',
            Table::TYPE_DECIMAL,
            '12,4',
            [],
            'amount'
        );

        $tableFirasDonationProductDonations->addColumn(
            'invoiced',
            Table::TYPE_BOOLEAN,
            null,
            [],
            'invoiced'
        );

        $tableFirasDonationProductDonations->addColumn(
            'created_at',
            Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation date'
        );

        $tableFirasDonationProductDonations->addIndex(
            $installer->getIdxName('firas_donations', ['order_item_id']),
            ['order_item_id']
        );

        $setup->getConnection()->createTable($tableFirasDonationProductDonations);

        $setup->endSetup();
    }
}
