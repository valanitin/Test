<?php
namespace Dynamic\Mytickets\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
	
	
   public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
   {
	      $setup->startSetup();	
	      
	       if (version_compare($context->getVersion(), '1.0.3') < 0) {
			 $setup->getConnection()
            ->addColumn(
                $setup->getTable('mytickets'),
                'orderreturn_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Orderreturn Id'
                ]
            ); 
		}	  	
	      
          if (version_compare($context->getVersion(), '1.0.2') < 0) {
			 $setup->getConnection()
            ->addColumn(
                $setup->getTable('mytickets'),
                'customer_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 11,
                    'nullable' => false,
                    'default' => '0',
                    'comment' => 'Customer ID'
                ]
            ); 
            
            $setup->getConnection()
            ->addColumn(
                $setup->getTable('mytickets'),
                'ticket_type',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'length' => 3,
                    'nullable' => true,
                    'default' => '1',
                    'comment' => '1=> Special, request 2=>Retrun Item, 3=> cacncel Order'
                ]
            ); 	
            
            
       
        
         $setup->endSetup();
    }
   }  


}
