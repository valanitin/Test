<?php

namespace MultiStoreRestApi\StoreProductsRestApi\Model;

use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Store\Api\StoreWebsiteRelationInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Webapi\Rest\Request;

/**
 * Class CustomApi
 *
 * @package StoreRestApi\CustomRestApi\Model
 */
class StoreProductsApi implements \MultiStoreRestApi\StoreProductsRestApi\Api\StoreProductsApiInterface
{
	
	/*
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;
	
	/*
	 * @var \Magento\Catalog\Model\ResourceModel\Product
	 */
	protected $productResourceModel;
	
	/*
	 * @var \Magento\Framework\Serialize\Serializer\Json
	 */
	protected $json;
	
	/*
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resourceConnection;
  
    /*
	 * construct
	 */
	public function __construct(
		WebsiteRepositoryInterface $websiteRepository,
		StoreRepositoryInterface $StoreRepository,
		StoreWebsiteRelationInterface $storeWebsiteRelation,
		StoreManagerInterface $storeManager,
		Request $request,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Catalog\Model\ResourceModel\Product $productResourceModel,
		\Magento\Framework\Serialize\Serializer\Json $json,
		\Magento\Framework\App\ResourceConnection $resourceConnection
    ) {       
        $this->request = $request;
		$this->websiteRepository = $websiteRepository;
		$this->StoreRepository = $StoreRepository;
        $this->storeWebsiteRelation = $storeWebsiteRelation;
        $this->storeManager = $storeManager;
		$this->productFactory = $productFactory;
		$this->productResourceModel = $productResourceModel;
		$this->json = $json;
		$this->resourceConnection = $resourceConnection;
    }


    public function UpdateStoreProducts()
    {   
		$sku = $this->request->getParam('sku');
		if($sku) {
			$product_body = $this->request->getContent();
			$productJsonDecode = $this->json->unserialize($product_body);
			
			$name = isset($productJsonDecode['product']['name']) ? $productJsonDecode['product']['name'] : "";
			
			$status = isset($productJsonDecode['product']['status']) ? $productJsonDecode['product']['status'] : "";
			
			$storecode= explode(',',$productJsonDecode['storecode']);			
			$product_body_loop=array();
			
			foreach($storecode as  $storecodeloop){
				$productFactory = $this->productFactory->create();
				$product = $productFactory->loadByAttribute('sku', $sku);

				$store = $this->StoreRepository->get($storecodeloop);
				$storeId = $store->getId();

				$product->setStoreId($storeId);
				try{
					if($name) {
						$product->setName($name);
						$this->productResourceModel->saveAttribute($product, 'name');
					}
					
					if($status) {
						$product->setStatus($status);
						$this->productResourceModel->saveAttribute($product, 'status');
					}
					
					if(isset($productJsonDecode['product']['custom_attributes'])) {
						$customAttributes = $productJsonDecode['product']['custom_attributes'];
						foreach($customAttributes as $customAttribute) {
							$product->setData($customAttribute['attribute_code'], $customAttribute['value']);
							$this->productResourceModel->saveAttribute($product, $customAttribute['attribute_code']);
						}
					}
					
				} catch(\Exception $e){
					throw new \Exception($e->getMessage());
				}
			}
			return "Data updated successfully";
		} else {
			return "Reuired paramater 'sku' is not found in api url.";
		}

    }
      
    public function UpdateProductprice()
    {		
        $sku = $this->request->getParam('sku');
		if($sku) {
			$price_body = $this->request->getContent();
			$price_body = json_decode($price_body);
			
			$specialPrice = isset($price_body->prices->price) ? $price_body->prices->price : "";
			$specialDateFrom = isset($price_body->prices->price_from) ? $price_body->prices->price_from : "";
			$specialDateTo = isset($price_body->prices->price_to) ? $price_body->prices->price_to : "";
		
			$productFactory = $this->productFactory->create();
			$product = $productFactory->loadByAttribute('sku', $sku);
			
			$productMainPrice = isset($price_body->prices->base_price) ? $price_body->prices->base_price : "";
			
			if($productMainPrice) {
			
				$countrycode= explode(',',$price_body->countrycode);
				$price_body_loop=array();
				foreach($countrycode as  $countrycodeloop){
					$website = $this->websiteRepository->get($countrycodeloop);
					$websiteId = (int)$website->getId();
					$storeIdArray = $this->storeWebsiteRelation->getStoreByWebsiteId($websiteId);
					
					foreach ($storeIdArray as $storeId) {
						$product->setStoreId($storeId);
						try{
							$product->setPrice($productMainPrice);
							$this->productResourceModel->saveAttribute($product, 'price');
							
							if($specialPrice) {
								$product->setSpecialPrice($specialPrice);
								$this->productResourceModel->saveAttribute($product, 'special_price');
							}
							
							if($specialDateFrom) {
								$product->setSpecialFromDate($specialDateFrom);
								$this->productResourceModel->saveAttribute($product, 'special_from_date');
							}
							
							if($specialDateTo) {
								$product->setSpecialToDate($specialDateTo);
								$this->productResourceModel->saveAttribute($product, 'special_to_date');
							}
							
							return "Data updated successfully";
						}catch(\Exception $e){
							throw new \Exception($e->getMessage());
						}
					}
				}
			} else {
				return "Reuired paramater 'base_price' is not found.";
			}
		} else {
			return "Reuired paramater 'sku' is not found in api url.";
		}	
    }

	public function UpdateProductQty()
    {		
		$requestBody = $this->request->getContent();
		$requestBody = $this->json->unserialize($requestBody);
		$connection = $this->resourceConnection->getConnection();
		$cataloginventory_stock_item   = $connection->getTableName('cataloginventory_stock_item');
		$cataloginventory_stock_status   = $connection->getTableName('cataloginventory_stock_status');
		$response = [];
		if(isset($requestBody['stock'])) {
			foreach($requestBody['stock'] as $key => $val) {
				try{
					$sku = $val['sku'];
					$qty = $val['qty'];
					$isINStock = $qty > 0 ? 1 : 0;
					$productFactory = $this->productFactory->create();
					$product = $productFactory->loadByAttribute('sku', $sku);
					$productId = $product->getId();
					
					$sql  = "UPDATE ".$cataloginventory_stock_item." csi,".$cataloginventory_stock_status." css SET csi.is_in_stock = ".$isINStock.", csi.qty = ".$qty." WHERE csi.product_id = ".$productId ." AND csi.product_id = css.product_id";

					if ($connection->query($sql)) {
						$response[] = "Stock Updated for sku - ".$sku;
					}
				} catch(\Exception $e) {
					$response[] = "Stock Not Updated for sku - ".$sku. " Error Message - ".$e->getMessage();
				}
			}
		} else {
			$response[] = "Required 'stock' array is not defined";
		}
		return json_encode($response);
    }
}