<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_XmlSitemap
 */


declare(strict_types=1);

namespace Amasty\XmlSitemap\Setup\UpgradeSchema;

use Magento\Framework\Setup\SchemaSetupInterface;

class AddProductType
{
    /**
     * @param SchemaSetupInterface $setup
     */
    public function addProductTypeColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->addColumn(
                $setup->getTable('amasty_xml_sitemap'),
                'exclude_product_type',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => false,
                    'comment' => 'Exclude Product Types'
                ]
            );
    }
}
