<?php
namespace Ethnic\Customfooter\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    
  public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
  {
      
    $newsTableName = $setup->getTable('ethnic_customfooter');

    if($setup->getConnection()->isTableExists($newsTableName) != true) {

      $newsTable = $setup->getConnection()
          ->newTable($newsTableName)
          ->addColumn(
              'id',
              \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
              null,
              ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
              'ID'
          )
          ->addColumn(
              'enabled',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false, 'default' => ''],
              'Enabled'
          )
          ->addColumn(
              'title',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false, 'default' => ''],
                'Title'
          )
          ->addColumn(
              'cms_page_id',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false, 'default' => ''],
                'Cms Page Id'
          )
          ->addColumn(
              'is_parent',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false, 'default' => ''],
                'Is Parent'
          )
          ->addColumn(
              'parent_id',
              \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
              255,
              ['nullable' => false, 'default' => ''],
                'Parent Id'
          )
          ->addIndex(
            $setup->getIdxName('ethnic_customfooter', ['title']),
            ['title']
          )
          ->setComment("News Table");

      $setup->getConnection()->createTable($newsTable);
    }
  }
}
?>