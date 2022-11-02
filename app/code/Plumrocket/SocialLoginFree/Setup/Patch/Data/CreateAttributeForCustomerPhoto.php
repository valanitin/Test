<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Setup\Patch\Data;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Plumrocket\SocialLoginFree\Model\Account\Photo;

/**
 * @since 3.2.0
 */
class CreateAttributeForCustomerPhoto implements DataPatchInterface, PatchRevertableInterface
{

    /**
     * @var \Magento\Framework\Setup\ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var \Magento\Eav\Setup\EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritdoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributeId = $eavSetup->getAttributeId(Customer::ENTITY, Photo::CUSTOMER_PHOTO_ATTRIBUTE_CODE);
        if (! $attributeId) {
            $eavSetup->addAttribute(
                Customer::ENTITY,
                Photo::CUSTOMER_PHOTO_ATTRIBUTE_CODE,
                [
                    'type'                      => 'varchar',
                    'backend'                   => '',
                    'label'                     => 'Customer Photo',
                    'input'                     => 'text',
                    'class'                     => '',
                    'global'                    => Attribute::SCOPE_STORE,
                    'visible'                   => false,
                    'frontend'                  => '',
                    'frontend_input'            => 'text',
                    'required'                  => false,
                    'user_defined'              => 0,
                    'default'                   => '',
                    'searchable'                => false,
                    'filterable'                => false,
                    'comparable'                => false,
                    'visible_on_front'          => false,
                    'used_in_product_listing'   => false,
                    'unique'                    => false,
                    'apply_to'                  => '',
                    'position'                  => 1000
                ]
            );
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public function revert()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $eavSetup->removeAttribute(Customer::ENTITY, Photo::CUSTOMER_PHOTO_ATTRIBUTE_CODE);
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
