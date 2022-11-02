<?php 

namespace Dynamic\CustomerDataSync\Setup; 

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Api\CustomerMetadataInterface;

class InstallData implements InstallDataInterface
{

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactor
     */
    private $attributeSetFactory;    

    /**
     * Init
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }    

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) {
         /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);

        $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();

        /** @var $attributeSet AttributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $customerSetup->addAttribute(Customer::ENTITY, 'account_sync', [
            'type' => 'int',
            'label' => 'Account Sync',
            'input' => 'select',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 1001,
            'position' => 1001,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => true,
            'system' => 0,
            'source' => 'Dynamic\CustomerDataSync\Model\Entity\Attribute\Source\AbstractSource\Options',
            'adminhtml_only' => 1,
            'default' => 0
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'account_sync')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address','adminhtml_checkout'],
        ]);

        $attribute->save();

        $customerSetup->addAttribute(Customer::ENTITY, 'address_sync', [
            'type' => 'int',
            'label' => 'Address Sync',
            'input' => 'select',
            'required' => false,
            'visible' => true,
            'user_defined' => true,
            'sort_order' => 1005,
            'position' => 1005,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => true,
            'system' => 0,
            'source' => 'Dynamic\CustomerDataSync\Model\Entity\Attribute\Source\AbstractSource\Options',
            'adminhtml_only' => 1,
            'default' => 0
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'address_sync')
        ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer','adminhtml_customer_address','customer_account_edit','customer_address_edit','customer_register_address','adminhtml_checkout'],
        ]);

        $attribute->save();
    }
}

