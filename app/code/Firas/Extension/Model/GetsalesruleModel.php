<?php
/**
 *Author: Firas
 *Email: firasath90@gmail.com
 */

namespace Firas\Extension\Model;
use Firas\Extension\Api\Getruleslist;
use Magento\Framework\Json\Helper\Data;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollection;

class GetsalesruleModel implements Getruleslist
{

    /**
     * @var jsonHelper
     */
    protected $jsonHelper;


    /**
     * CustomerOrder constructor.
     * @param Data $jsonHelper
     */

    public function __construct(
      \Magento\Store\Model\StoreManagerInterface $storeManager,
      \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory,
      \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
      Data $jsonHelper)
    {

        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->_websiteCollectionFactory = $websiteCollectionFactory;
        $this->groupRepository = $groupRepository;
    }

    /**
     * Returns orders data to user
     *
     * @api
     * @param  string $phoneNumber customer phone number
     * @param  string $websiteCode Website Code..
     * @return return order array collection.
     */
    public function getList()
    {

      $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

      $rule_id = 1;

      $rule = $objectManager->create('Magento\SalesRule\Model\Rule')->getCollection();
      // echo '<pre>';print_r($rule->getData());
      $ruleCount = count($rule);
      if ($ruleCount == 0)
      {
          $errorMsg[] = array(
              ['status' => 'No Rules',
              'message' => __('There are no rules in this website.') ]
          );
          return $errorMsg;

      }
      else
      {
          $data = [];
          $websiteNames = [];
          $customerGroupNames = [];
          $i = 0;
          foreach ($rule as $orderDetails)
          {
              $ruleDataById = $objectManager->create('Magento\SalesRule\Model\Rule')->load($orderDetails->getRuleId());
              $websiteIds = $ruleDataById->getData('website_ids');
              $customerGroupIds = $ruleDataById->getData('customer_group_ids');

              //loop to getch customer group name by groupId
              foreach($customerGroupIds as $customerGroupId){
                $customerGroupNames[] = $this->groupRepository->getById($customerGroupId)->getCode();
              }
              //end

              //loop to getch website name by website id
              foreach($websiteIds as $websiteId){
                $websiteCollection = $this
                    ->_websiteCollectionFactory
                    ->create()
                    ->addFieldToFilter('website_id', $websiteId)->load();
                    $websiteData = $websiteCollection->getData();
                    if(!empty($websiteData)){
                      $websiteNames[] = $websiteData[0]['name'];
                    }
                    else{
                      $websiteNames[] = 'Null';
                    }
                  }
                //end


              $data[$i]['rule_id'] = $ruleDataById->getData('rule_id');
              $data[$i]['name'] = $ruleDataById->getData('name');
              $data[$i]['description'] = $ruleDataById->getData('description');
              $data[$i]['from_date'] = $ruleDataById->getData('from_date');
              $data[$i]['to_date'] = $ruleDataById->getData('to_date');
              $data[$i]['uses_per_customer'] = $ruleDataById->getData('uses_per_customer');
              $data[$i]['is_active'] = $ruleDataById->getData('is_active');
              $data[$i]['conditions_serialized'] = $ruleDataById->getData('conditions_serialized');
              $data[$i]['actions_serialized'] = $ruleDataById->getData('actions_serialized');
              $data[$i]['stop_rules_processing'] = $ruleDataById->getData('stop_rules_processing');
              $data[$i]['is_advanced'] = $ruleDataById->getData('is_advanced');
              $data[$i]['product_ids'] = $ruleDataById->getData('product_ids');
              $data[$i]['sort_order'] = $ruleDataById->getData('sort_order');
              $data[$i]['simple_action'] = $ruleDataById->getData('simple_action');
              $data[$i]['discount_amount'] = $ruleDataById->getData('discount_amount');
              $data[$i]['discount_qty'] = $ruleDataById->getData('discount_qty');
              $data[$i]['discount_step'] = $ruleDataById->getData('discount_step');
              $data[$i]['apply_to_shipping'] = $ruleDataById->getData('apply_to_shipping');
              $data[$i]['times_used'] = $ruleDataById->getData('times_used');
              $data[$i]['is_rss'] = $ruleDataById->getData('is_rss');
              $data[$i]['coupon_type'] = $ruleDataById->getData('coupon_type');
              $data[$i]['use_auto_generation'] = $ruleDataById->getData('use_auto_generation');
              $data[$i]['uses_per_coupon'] = $ruleDataById->getData('uses_per_coupon');
              $data[$i]['simple_free_shipping'] = $ruleDataById->getData('simple_free_shipping');
              $data[$i]['code'] = $ruleDataById->getData('coupon_code');
              $data[$i]['customer_group_ids'] = $customerGroupNames;
              $data[$i]['website_ids'] = $websiteNames;

              $i++;
          }

          return $data;
      }

    }
}
