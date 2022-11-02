<?php

namespace Dynamic\OrdercancelComment\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{

   public function install(SchemaSetupInterface $setup, ModuleContextInterface $context) 
   {

       $setup->startSetup();
       $this->addOrderCancelCommentToSalesOrder($setup);
       $setup->endSetup();

    }

    protected function addOrderCancelCommentToSalesOrder($setup){
    
        $connection = $setup->getConnection();
        $connection->addColumn(

            $setup->getTable('sales_order'),
            'cancel_comment',
            [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 
            'nullable' => true,
            'comment' => 'Order Cancel Comment',
            ]

        );
    

    }
}
