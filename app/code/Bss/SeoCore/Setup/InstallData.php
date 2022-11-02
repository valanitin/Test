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
 * @package    Bss_SeoCore
 * @author     Extension Team
 * @copyright  Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\SeoCore\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Bss\SeoCore\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var CategorySetupFactory
     */
    protected $categorySetupFactory;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param CategorySetupFactory $categorySetupFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CategorySetupFactory $categorySetupFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $this->addMainKeyword($eavSetup);

        $setup->getConnection()->addColumn(
            $setup->getTable('cms_page'),
            'seo_h1_title',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => '2M',
                'nullable' => true,
                'comment' => 'Custom H1 Page'
            ]
        );

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        $categorySetup->addAttribute(
            \Magento\Catalog\Model\Category::ENTITY,
            'seo_h1_title',
            [
                'type' => 'varchar',
                'label' => 'Custom H1 Page',
                'input' => 'text',
                'source' => '',
                'required' => false,
                'sort_order' => 610,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => 'Search Engine Optimization',
                'note' => 'This new heading 1 will replace the original h1 of your page'
            ]
        );
        $idg = $categorySetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Search Engine Optimization');
        $categorySetup->addAttributeToGroup(
            $entityTypeId,
            $attributeSetId,
            $idg,
            'seo_h1_title',
            46
        );
        $setup->endSetup();
    }

    /**
     * @param object $eavSetup
     */
    protected function addMainKeyword($eavSetup)
    {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'seo_h1_title',
            [
                'group' => 'Search Engine Optimization',
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Custom H1 Page',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '',
                'sort_order' => 610,
                'note' => 'This new heading 1 will replace the original h1 of your page'
            ]
        );
    }
}
