<?php
namespace Zealousweb\AppleLogin\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;

class AddSocialIconAttribute implements DataPatchInterface
{
    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /**
     * @var \Magento\Customer\Setup\CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
    * @param ModuleDataSetupInterface $moduleDataSetup
    * @param CustomerSetupFactory $customerSetupFactory
    */
    public function __construct(
       ModuleDataSetupInterface $moduleDataSetup,
       CustomerSetupFactory $customerSetupFactory
    ) {
       $this->moduleDataSetup = $moduleDataSetup;
       $this->_customerSetupFactory = $customerSetupFactory;
    }

    /**
    * {@inheritdoc}
    */
    public function apply()
    {
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->_customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $customerSetup->addAttribute(Customer::ENTITY, 'social_account',  [
            'label'    => 'Social Account',
            'input'    => 'text',
            'source'   => '',
            'visible'  => true,
            'required' => false,
            'default' => 'null',
            'frontend' => '',
            'unique'     => false,
            'note'       => '',
            'adminhtml_only' => 1,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => false,
            'position' => 130
        ]);

        $socialAccount = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'social_account');
        $used_in_forms = array();
        $used_in_forms[]='adminhtml_customer';
        $socialAccount->setData('used_in_forms', $used_in_forms)
            ->setData('is_used_for_customer_segment', true)
            ->setData('is_system', 0)
            ->setData('is_user_defined', 1)
            ->setData('is_visible', 1)
            ->setData('sort_order', 130)
            ->save();
    }

    /**
    * {@inheritdoc}
    */
    public static function getDependencies()
    {
       return [];
    }

    /**
    * {@inheritdoc}
    */
    public function getAliases()
    {
       return [];
    }

    /**
    * {@inheritdoc}
    */
    public static function getVersion()
    {
      return '1.0.3';
    }
}