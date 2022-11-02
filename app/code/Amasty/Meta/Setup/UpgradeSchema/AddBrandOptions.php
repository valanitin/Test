<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


declare(strict_types=1);

namespace Amasty\Meta\Setup\UpgradeSchema;

use Amasty\Meta\Api\Data\ConfigInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;

class AddBrandOptions
{
    public function execute(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable(ConfigInterface::TABLE_NAME);
        $connection = $setup->getConnection();
        $connection->dropColumn($table, 'stores');

        $connection->addColumn(
            $table,
            ConfigInterface::IS_BRAND_CONFIG,
            [
                'type' => Table::TYPE_SMALLINT,
                'default' => 0,
                'comment' => 'Is Brand Config'
            ]
        );
        $connection->addColumn(
            $table,
            ConfigInterface::BRAND_META_TITLE,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'nullable' => true,
                'comment' => 'Brand Meta Title'
            ]
        );
        $connection->addColumn(
            $table,
            ConfigInterface::BRAND_META_DESCRIPTION,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'nullable' => true,
                'comment' => 'Brand Meta Description'
            ]
        );
        $connection->addColumn(
            $table,
            ConfigInterface::BRAND_META_KEYWORDS,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'nullable' => true,
                'comment' => 'Brand Meta Keywords'
            ]
        );
        $connection->addColumn(
            $table,
            ConfigInterface::BRAND_H1_TAG,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'nullable' => true,
                'comment' => 'Brand H1 Tag'
            ]
        );
        $connection->addColumn(
            $table,
            ConfigInterface::BRAND_DESCRIPTION,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'nullable' => true,
                'comment' => 'Brand Description'
            ]
        );
        $connection->addColumn(
            $table,
            ConfigInterface::BRAND_AFTER_PRODUCT_TEXT,
            [
                'type' => Table::TYPE_TEXT,
                'default' => null,
                'nullable' => true,
                'comment' => 'Brand After Product Text'
            ]
        );
    }
}
