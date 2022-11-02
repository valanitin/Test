<?php 
namespace Dynamic\Notifyme\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $installer->getConnection();

        
            $connection->addColumn(
            $installer->getTable('notifyme'),
               'phone',
               [
                   'type' => Table::TYPE_TEXT,
                   'length' => 256,
                   'nullable' => false,
                   'default' => '',
                   'comment' => 'phone'
               ]
           );
        
         $installer->endSetup();
    }
}