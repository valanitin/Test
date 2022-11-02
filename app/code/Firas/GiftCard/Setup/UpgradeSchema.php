<?php
/**
 * @Author      Firas Developers
 * @package     Firas_GiftCard
 * @copyright   Copyright (c) 2019 MAGETOP (https://www.firas.com)
 * @terms       https://www.firas.com/terms
 * @license     https://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 **/
namespace Firas\GiftCard\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1', '<')) {
            // Get module table
            $giftDetails =  $setup->getTable('firas_gift');
            $giftUserDetails = $setup->getTable('firas_giftuser');

            // Check if the table already exists
            if ($setup->getConnection()->isTableExists($giftDetails) == true) {
                // Declare data
                $connection = $setup->getConnection();
                $connection->addColumn(
                    $giftDetails,
                    'message',
                    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'after' => 'from','comment'=>'Message in gift card ']
                );
                $connection->addColumn(
                    $giftDetails,
                    'duration',
                    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,'nullable' => false,'unsigned' => true,'after' => 'message','comment'=>'Active duration of gift card.']
                );
                $connection->addColumn(
                    $giftDetails,
                    'order_id',
                    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,'nullable' => false,'unsigned' => true,'after' => 'duration','comment'=>'Order id of gift card product.']
                );
            }
            if ($setup->getConnection()->isTableExists($giftUserDetails) == true) {
                $connection->addColumn(
                    $giftUserDetails,
                    'is_active',
                    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,'after' => 'remaining_amt','comment'=>'Is active gift card ?']
                );
                $connection->addColumn(
                    $giftUserDetails,
                    'is_expire',
                    ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,'nullable' => false,'unsigned' => true,'default'=>0,'after' => 'is_active','comment'=>'Is expired gift card ?']
                );
            }
        }
        $setup->endSetup();
    }
}
