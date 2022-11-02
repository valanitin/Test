<?php
namespace Dynamic\StoreCreditSync\Setup;

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
				'store_credit_sync_flag',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 
					'default' => 0,
                	'comment' => 'Order Store Credit Sync Flag'
				]
			);
			$installer->getConnection()->addColumn(
				$installer->getTable('sales_order'),
				'store_credit_sync_flag',
				[
					'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT, 
					'default' => 0,
                	'comment' => 'Order Store Credit Sync Flag'
				]
			);
		}
		$installer->endSetup();
	}
}

