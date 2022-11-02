<?php

namespace StoreRestApi\CustomRestApi\Model;


use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\GroupFactory;
use Magento\Store\Model\ResourceModel\Group;
use Magento\Store\Model\ResourceModel\Store;
use Magento\Store\Model\ResourceModel\Website;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\WebsiteFactory;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class CustomApi
 *
 * @package StoreRestApi\CustomRestApi\Model
 */
class CustomApi implements \StoreRestApi\CustomRestApi\Api\CustomApiInterface
{
     /**
     * @var WebsiteFactory
     */
    private $websiteFactory;
    /**
     * @var Website
     */
    private $websiteResourceModel;
    /**
     * @var StoreFactory
     */
    private $storeFactory;
    /**
     * @var GroupFactory
     */
    private $groupFactory;
    /**
     * @var Group
     */
    private $groupResourceModel;
    /**
     * @var Store
     */
    private $storeResourceModel;
    /**
     * @var ManagerInterface
     */
    private $eventManager;

    protected $_customerFactory;
    protected $_websiteFactory;

    public function __construct(
        WebsiteFactory $websiteFactory,
        Website $websiteResourceModel,
        Store $storeResourceModel,
        Group $groupResourceModel,
        StoreFactory $storeFactory,
        GroupFactory $groupFactory,
        ManagerInterface $eventManager,
        Request $request
    ) {
        $this->websiteFactory = $websiteFactory;
        $this->websiteResourceModel = $websiteResourceModel;
        $this->storeFactory = $storeFactory;
        $this->groupFactory = $groupFactory;
        $this->groupResourceModel = $groupResourceModel;
        $this->storeResourceModel = $storeResourceModel;
        $this->eventManager = $eventManager;
        $this->request = $request;
    }

    /**
     * Get Customer List
     * @return string
     */
    public function getCustomerList()
    {
        $customerCollection = $this->_customerFactory->create();
        $response = ['status' => false, 'message' => 'Error while fetching data'];
        if (count($customerCollection->getData())) {
            $customerList = $customerCollection->getData();
            $response = ['status' => true, 'data' => $customerList];
        } else {
            $response = ['status' => false, 'message' => 'No customer found'];
        }
        //echo 'hello';
        die();
        return json_encode($response);
    }

    /**
     * Save website
     * @return string
     */

    public function saveWebsite()
    {
      $filter = $this->request->getParam('type');
      $code = $this->request->getParam('code');
      $name = $this->request->getParam('name');
      if($filter == 'website'){


        if(!empty($name) && !empty($code)){
            $website = $this->websiteFactory->create();
            $website->setCode($code);
            $website->setName($name);
            $website->setDefaultGroupId(1);
            try {
               $this->websiteResourceModel->save($website); 
            } catch (\Exception $e) {
                
            }
           
            /*if($website->getId()){
                $response = ["status" => true, "message" => "Website Saved Successfully","id" => $website->getId()];
                return json_encode($response);
            }*/

            $response = ["status" => true, "message" => "Website Already Saved Successfully"];
            return json_encode($response);
            die();
            
        }
         
      }
      if($filter == 'store'){
            $websiteId = $this->request->getParam('website_id');
            if($websiteId){
                $group = $this->groupFactory->create();
                $group->setWebsiteId($websiteId);
                $group->setName($name);
                $group->setCode($code);
                $group->setRootCategoryId(2);
                $group->setDefaultStoreId(3);
                
                try {
                   $this->groupResourceModel->save($group);
                } catch (\Exception $e) {
                    
                }
               
                if($group->getId()){
                    $response = ["status" => true, "message" => "Group Saved Successfully","id" => $group->getId()];
                    return json_encode($response);
                }

                $response = ["status" => true, "message" => "Group Already Saved Successfully"];
                return json_encode($response);
                die();
            }else{
                $response = ["status" => false, "message" => "Please Send Website Id"];
                return json_encode($response);
                die();
            }
           
            
      }
      if($filter == 'store_view'){
            $websiteId = $this->request->getParam('website_id');
            $groupId = $this->request->getParam('group_id');
            if(!empty($websiteId) && !empty($groupId)){
                $store = $this->storeFactory->create();
                $store->setCode($code);
                $store->setName($name);
                //$store->setWebsite(int($websiteId));
                $store->setGroupId($groupId);
                $store->setData('is_active','1');
                try {
                    $this->storeResourceModel->save($store);
                } catch (\Exception $e) {
                    
                }
                if($store->getId()){
                    $response = ["status" => true, "message" => "Store Saved Successfully","id" => $store->getId()];
                    return json_encode($response);
                }

                $response = ["status" => true, "message" => "Store Already Saved Successfully"];
                return json_encode($response);
                die();
            }else{
                $response = ["status" => false, "message" => "Please Send Website Id"];
                return json_encode($response);
                die();
            }
      }
      
      $response = ['status' => false, 'message' => 'Please Send Proper Type'];
      return json_encode($response);
        
    }

    /**
     * Save website
     * @return string
     */

    public function editWebsite()
    {
      $filter = $this->request->getParam('type');
      $code = $this->request->getParam('code');
      $name = $this->request->getParam('name');
      if($filter == 'website'){

        $websiteId = $this->request->getParam('website_id');
        if(!empty($name) && !empty($code)){
            $website = $this->websiteResourceModel->load($websiteId,'');
            
            
        }
         
      }
      if($filter == 'store'){
            
           
            
      }
      
      
      $response = ['status' => false, 'message' => 'Please Send Proper Type'];
      return json_encode($response);
        
    }
}