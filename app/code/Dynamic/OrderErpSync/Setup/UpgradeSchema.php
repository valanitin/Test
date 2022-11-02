<?php
namespace Dynamic\OrderErpSync\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
	public function upgrade( SchemaSetupInterface $setup, ModuleContextInterface $context ) {
		$installer = $setup;

		$installer->startSetup();

		if(version_compare($context->getVersion(), '1.0.1', '<=')) {
			$installer->getConnection()->addColumn(
				$installer->getTable('sales_order_grid'),
				'order_erp_sync_flag',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 
					'default' => 0,
                	'comment' => 'Order ERP Sync Flag'
				]
			);
			$installer->getConnection()->addColumn(
				$installer->getTable('sales_order'),
				'order_erp_sync_flag',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 
					'default' => 0,
                	'comment' => 'Order ERP Sync Flag'
				]
			);
		}
		$installer->endSetup();
	}
}

