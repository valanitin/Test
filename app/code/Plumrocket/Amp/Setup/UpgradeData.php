<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Setup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Category;

/**
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $_eavSetupFactory;

    /**
     * @var \Plumrocket\Amp\Helper\Google\Analytics
     */
    private $analytics;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory                         $eavSetupFactory
     * @param \Plumrocket\Amp\Helper\Google\Analytics $analytics
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Plumrocket\Amp\Helper\Google\Analytics $analytics
    ) {
        $this->_eavSetupFactory = $eavSetupFactory;
        $this->analytics = $analytics;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            /**
             * @var EavSetup $eavSetup
             */
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

            $entityTypeId = $eavSetup->getEntityTypeId(Category::ENTITY);
            $attributeSetId   = $eavSetup->getDefaultAttributeSetId($entityTypeId);
            $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Content');

            /**
             * AMP Homepage Image attribute for category
             */
            $eavSetup->addAttribute(
                Category::ENTITY,
                'amp_homepage_image',
                [
                    'type' => 'varchar',
                    'frontend' => '',
                    'label' => 'AMP Homepage Image',
                    'input' => 'image',
                    'group' => 'General Information',
                    'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'default' => 0,
                    'position' => 30
                ]
            );

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $attributeGroupId,
                'amp_homepage_image',
                '270'
            );
        }

        if (version_compare($context->getVersion(), '2.2.3', '<')) {
            /**
             * @var EavSetup $eavSetup
             */
            $eavSetup = $this->_eavSetupFactory->create(['setup' => $setup]);

            $entityTypeId = $eavSetup->getEntityTypeId(Category::ENTITY);
            $attributeSetId   = $eavSetup->getDefaultAttributeSetId($entityTypeId);
            $attributeGroupId = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, 'Display Settings');

            /**
             * AMP Display Mode attribute for category
             */
            $eavSetup->addAttribute(
                Category::ENTITY,
                'amp_display_mode',
                [
                    'type' => 'varchar',
                    'label' => 'AMP Display Mode',
                    'input' => 'select',
                    'group' => 'Display Settings',
                    'source' => 'Plumrocket\Amp\Model\Attribute\Source\Mode',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'visible' => true,
                    'required' => false,
                    'sort_order' => 15
                ]
            );

            $eavSetup->addAttributeToGroup(
                $entityTypeId,
                $attributeSetId,
                $attributeGroupId,
                'amp_display_mode',
                '270'
            );
        }

        if (version_compare($context->getVersion(), '2.3.0', '<')) {
            $this->analytics->copyConfig();
        }

        $setup->endSetup();
    }
}
