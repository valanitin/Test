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
 * @package    Bss_AjaxSocialLogin
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SocialLogin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 *
 * @package Bss\SocialLogin\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $table = $installer->getConnection()
            ->newTable($installer->getTable('bss_sociallogin'))
            ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                ], 'Id')
            ->addColumn('type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable'  => false,
                ], 'Login type')
            ->addColumn('token_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable'  => false,
                'default'   => null,
                ], 'Token Id')
            ->addColumn('password_fake_email', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, [
                'nullable'  => false,
                'default'   => null,
                ], 'Password Fake Email')
            ->addColumn('customer_id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                ], 'Customer Id')
            ->addIndex($installer->getIdxName('bss_sociallogin', ['type']), ['type'])
            ->addIndex($installer->getIdxName('bss_sociallogin', ['token_id']), ['token_id'])
            ->addIndex($installer->getIdxName('bss_sociallogin', ['password_fake_email']), ['password_fake_email'])
            ->addIndex($installer->getIdxName('bss_sociallogin', ['customer_id']), ['customer_id'])
            ->addForeignKey(
                $installer->getFkName('bss_sociallogin', 'customer_id', 'customer_entity', 'entity_id'),
                'customer_id',
                $installer->getTable('customer_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE,
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Social Login ');
        $installer->getConnection()->createTable($table);
        $installer->endSetup();
    }
}
