<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\GeoIPAutoSwitchStore\Setup;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $this->installMaxmindV4($setup);
        $this->installMaxmindV6($setup);
        $this->installGeoIPData($setup);
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function installMaxmindV4($setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('bss_geoip_maxmind_v4')
            )
            ->addColumn(
                'geoip_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'GeoIP Id'
            )
            ->addColumn(
                'network',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Network'
            )
            ->addColumn(
                'geoname_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Geoname ID'
            )
            ->addColumn(
                'begin_ip',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Begin IP'
            )
            ->addColumn(
                'end_ip',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'End IP'
            )

            ->addIndex(
                $installer->getIdxName('bss_geoip_maxmind_v4', ['geoip_id']),
                ['geoip_id']
            )
            ->setComment(
                'Database of Country IPv4 from MaxMind'
            );
        $installer->getConnection()->createTable($table);
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function installMaxmindV6($setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('bss_geoip_maxmind_v6')
            )
            ->addColumn(
                'geoip_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'GeoIP Id'
            )
            ->addColumn(
                'network',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Network'
            )
            ->addColumn(
                'geoname_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Geoname ID'
            )
            ->addColumn(
                'begin_ip',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Begin IP'
            )
            ->addColumn(
                'end_ip',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'End IP'
            )

            ->addIndex(
                $installer->getIdxName('bss_geoip_maxmind_v6', ['geoip_id']),
                ['geoip_id']
            )
            ->setComment(
                'Database of Country IPv6 from MaxMind'
            );
        $installer->getConnection()->createTable($table);
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }

    /**
     * @param \Magento\Framework\Setup\SchemaSetupInterface $setup
     */
    public function installGeoIPData($setup)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('bss_geoip_maxmind_locations')
            )
            ->addColumn(
                'local_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Locations Id'
            )
            ->addColumn(
                'geoname_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                10,
                ['nullable' => false],
                'Geoname ID'
            )
            ->addColumn(
                'locale_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Locale Code'
            )
            ->addColumn(
                'continent_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Continent Code'
            )
            ->addColumn(
                'continent_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Continent Name'
            )
            ->addColumn(
                'country_iso_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Contry ISO code'
            )

            ->addIndex(
                $installer->getIdxName('bss_geoip_maxmind_locations', ['local_id']),
                ['local_id']
            )
            ->setComment(
                'Database of Country Locations'
            );
        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()
            ->newTable(
                $installer->getTable('bss_geo_ip')
            )
            ->addColumn(
                'geoip_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'GeoIP Id'
            )
            ->addColumn(
                'geoip_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'GeoType'
            )->addColumn(
                'geoip_value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Value'
            )
            ->addIndex(
                $installer->getIdxName('bss_geo_ip', ['geoip_id']),
                ['geoip_id']
            )
            ->setComment(
                'Database of GeoIP Config'
            );

        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
