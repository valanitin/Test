<?php
declare(strict_types=1);
/**
 * Mage360_Brands extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Mage360
 * @package   Mage360_Brands
 * @copyright 2018 Mage360
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Qaiser Bashir
 */
namespace Mage360\Brands\Controller\Adminhtml\Brands;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mage360\Brands\Model\Brands as BrandsModel;
use Magento\UrlRewrite\Model\UrlRewrite as BaseUrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteService;

class Sync extends Action
{
    /**
     * @var string
     */
    const ACTION_RESOURCE = 'Mage360_Brands::brands';
    /**
     * @var ForwardFactory
     */
    public $resultForwardFactory;
    public $eavAttributes;
    public $scopeConfig;
    public $brandModel;

    private $urlPrefix;

    private $urlExtension;

    /**
     * constructor
     *
     * @param Context        $context
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        StoreManagerInterface $storeManager,
        UrlRewriteFactory $urlRewriteFactory,
        UrlFinderInterface $urlFinder,
        BaseUrlRewrite $urlRewrite,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Mage360\Brands\Model\Brands $brandModel
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->eavAttributes = $eavAttribute;
        $this->scopeConfig = $scopeConfig;
        $this->brandModel = $brandModel;

        $this->storeManager = $storeManager;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlRewrite = $urlRewrite;
        $this->urlFinder = $urlFinder;
        $this->urlPrefix = BrandsModel::URL_PREFIX;
        $this->urlExtension = BrandsModel::URL_EXT;
        parent::__construct($context);
    }


public function cleanStrt($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

   return strtolower(preg_replace('/[^A-Za-z0-9\-]/', '', $string)); // Removes special chars.
}
    /**
     * forward to edit
     *
     * @return \Magento\Backend\Model\View\Result\Forward
     */
    public function execute()
    {

        //log file for cron
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron_brandsync.log');
    		$logger = new \Zend\Log\Logger();
    		$logger->addWriter($writer);
        //end

        $attributeid = $this->scopeConfig->getValue('mage360_brands/brands_general/brand_attribute', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if($attributeid){
         $model = $this->eavAttributes->setEntityTypeId(
            \Magento\Catalog\Model\Product::ENTITY
        );
       $model->load($attributeid);


       $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('mage360_brands'); //gives table name with prefix

        //Select Data from table
        $sql = "Select attribute_id FROM " . $tableName;
        $result = $connection->fetchAll($sql);

        $BrandArray = array();
        foreach ($result as $key => $value) {
            $BrandArray[] = $value['attribute_id'];
        }
        $deleteBrandArray = array();
        $optionArray      = array();
		foreach($model->getOptions() as $option)
		{

		  $item = $this->brandModel;
			if($option->getValue())
			{
				$attribute_id = (int)$option->getValue();
                $optionArray[]= $attribute_id;
				if ($attribute_id) {
                    $item = $this->brandModel->getCollection()->addFieldToFilter("attribute_id",$attribute_id)->getFirstItem();


                    if(!$item->getId()){
                       $item = $this->brandModel;
                       $data = array(
                					'name' => $option->getLabel(),
                					'attribute_id' => $option->getValue(),
                					'is_active' => 1,
                                    'url_key' => $this->cleanStrt($option->getLabel())
                				);
        				$item->setData($data);
        				try{

        						$item->save();


                                $storeManagerDataList = $this->storeManager->getStores();
                                 foreach ($storeManagerDataList as $key => $value) {

                                 $this->saveUrlRewrite($this->cleanStrt($option->getLabel()), $item->getId(), $key);
                                 }



        				}
        				catch(\Exception $e){
        				$this->messageManager->addError(__('Error Try again : %1',$e->getMessage()));
        				}
                    }
                }


			}
		}


        $result=array_diff($BrandArray,$optionArray);

        if(count($result)>0){
            $sql = "Delete FROM " . $tableName." Where attribute_id IN (".implode(',',$result).")";
            $connection->query($sql);
        }

        $this->messageManager->addSuccess(__('Data Re-Synced Successfully'));

        //log file for sync brand cron
        //$logger->info(__METHOD__);
    		$logger->info('Funciton executed = '.__METHOD__.' = Brands synced successfully.');
        //end

        }else{
          $this->messageManager->addError(__('Select Attribute From Configuration First...'));
          //log file for sync brand cron
          $logger->info('Brand sync failed by cron, please check.');
          //end
        }




		$this->_redirect('*/*/index');
    }


    /**
     * Saves the url rewrite for that specific store
     *
     * @param  $link string
     * @param  $id int
     * @param  $storeIds string
     * @return void
     */
    private function saveUrlRewrite($link, $id, $storeId)
    {

        $getCustomUrlRewrite = $this->urlPrefix . "/" . $link.$this->urlExtension;

        $brandId = $this->urlPrefix . "-" . $id;

        $filterData = [
            UrlRewriteService::STORE_ID => $storeId,
            UrlRewriteService::REQUEST_PATH => $getCustomUrlRewrite,
            UrlRewriteService::ENTITY_ID => $id,

        ];

        // check if there is an entity with same url and same id
        $rewriteFinder = $this->urlFinder->findOneByData($filterData);

        // if there is then do nothing, otherwise proceed
        if ($rewriteFinder === null) {
            // check maybe there is an old url with this target path and delete it
            $filterDataOldUrl = [
                UrlRewriteService::STORE_ID => $storeId,
                UrlRewriteService::REQUEST_PATH => $getCustomUrlRewrite,
            ];
            $rewriteFinderOldUrl = $this->urlFinder->findOneByData($filterDataOldUrl);

            if ($rewriteFinderOldUrl !== null) {
                $this->urlRewrite->load($rewriteFinderOldUrl->getUrlRewriteId())->delete();
            }

            // check maybe there is an old id with different url, in this case load the id and update the url
            $filterDataOldId = [
                UrlRewriteService::STORE_ID => $storeId,
                UrlRewriteService::ENTITY_TYPE => $brandId,
                UrlRewriteService::ENTITY_ID => $id
            ];
            $rewriteFinderOldId = $this->urlFinder->findOneByData($filterDataOldId);

            if ($rewriteFinderOldId !== null) {
                $this->urlRewriteFactory->create()->load($rewriteFinderOldId->getUrlRewriteId())
                    ->setRequestPath($getCustomUrlRewrite)
                    ->save();
            }
			else
			{
                // now we can save
                $this->urlRewriteFactory->create()
                    ->setStoreId($storeId)
                    ->setIdPath(rand(1, 100000))
                    ->setRequestPath($getCustomUrlRewrite)
                    ->setTargetPath("brands/view/index")
                    ->setEntityType($brandId)
                    ->setEntityId($id)
                    ->setIsAutogenerated(0)
                    ->save();
            }
        }
    }
}
