<?php

namespace Dynamic\Cmspagemanager\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
		$installer = $setup;
		$installer->startSetup();

		/**
		 * Creating table Dynamic_cmspagemanager
		 */
		$table = $installer->getConnection()->newTable(
			$installer->getTable('cmspagemanager')
		)->addColumn(
			'cmspagemanager_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
			null,
			['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
			'Entity Id'
		)->addColumn(
			'cms_id',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			null,
			['nullable' => false , 'default' => '0'],
			'CMS Id'
		)->addColumn(
			'status',
			\Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
			null,
			['nullable' => false , 'default' => '0'],
			'Status'
		)->addColumn(
			'extra_data',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true,'default' => null],
			'Extra Data'
		)->addColumn(
			'cms_text',
			\Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
			null,
			['nullable' => true,'default' => null],
			'CMS Text'
		)->addColumn(
			'created_at',
			\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
			null,
			['nullable' => false],
			'Created At'
		)->setComment(
			'Cmspagemanager'
		);
		$installer->getConnection()->createTable($table);
		$installer->endSetup();
	}
}