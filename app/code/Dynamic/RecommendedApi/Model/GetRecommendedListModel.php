<?php

namespace Dynamic\RecommendedApi\Model;

use Dynamic\RecommendedApi\Api\GetRecommendedList;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Json\Helper\Data;
use Magento\Store\Model\App\Emulation;

class GetRecommendedListModel implements GetRecommendedList
{
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\productFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $appEmulation;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Recommended data constructor.
     * @param Context $context
     * @param RequestInterface $request
     * @param Data $jsonHelper
     * @param Image $imageHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param CategoryFactory $categoryFactory
     * @param ProductFactory $productFactory
     * @param Emulation $appEmulation
     * @param CollectionFactory $productCollectionFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\App\Emulation $appEmulation,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
    ) {
        $this->request = $request;
        $this->jsonHelper = $jsonHelper;
        $this->imageHelper = $imageHelper;
        $this->priceHelper = $priceHelper;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->appEmulation = $appEmulation;
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    /**
     * @return array|array[]
     */
    public function getList()
    {
        $data = [];

        $productSku = $this->request->getParam("sku");

        if (empty($productSku)) {
            $data = [['status' => 'No Data', 'message' => __('Enter the SKU and try again.')]];
            return $data;
        }

        $product = $this->productFactory->create();
        $product->load($product->getIdBySku($productSku));

        if (!$product->getId()) {
            $data = [['status' => 'No Data', 'message' => __('There are no Recommended data in this website.')]];
            return $data;
        }

        $percentage = $product->getFinalPrice() * 70 / 100;
        $min_price = $product->getFinalPrice() - $percentage;
        $max_price = $product->getFinalPrice() + $percentage;

        $categories = $product->getCategoryIds();

        if (!empty($categories) && count($categories) > 0) {

            $productcollection = $this->_productCollectionFactory->create();
            $categoryProducts = $productcollection->addAttributeToSelect('*')->addAttributeToSort('position',
                'ASC')->addCategoriesFilter(['in' => $categories]);
            $categoryProducts->addAttributeToFilter('price', array('gteq' => $min_price));
            $categoryProducts->addAttributeToFilter('price', array('lteq' => $max_price));
            $categoryProducts->addAttributeToFilter('entity_id', array('nin' => array($product->getId())));
            $categoryProducts->setPageSize(18);

            if (!empty($categoryProducts) && count($categoryProducts) > 0) {

                foreach ($categoryProducts as $productData) {

                    $this->appEmulation->startEnvironmentEmulation($productData->getStoreId(),
                        \Magento\Framework\App\Area::AREA_FRONTEND, true);

                    $imageUrl = $this->imageHelper->init($productData,
                        'product_page_image_small')->setImageFile($productData->getThumbnail())->resize(234)->getUrl();
                    $data[] = [
                        "product_id" => $productData->getId(),
                        "sku" => $productData->getSku(),
                        "type_id" => $productData->getTypeId(),
                        "name" => $productData->getName(),
                        "product_url" => $productData->getProductUrl(),
                        "image_url" => $imageUrl,
                        "brand_name" => $productData->getResource()->getAttribute('brands')->getFrontend()->getValue($productData),
                        "price" => $this->priceHelper->currency($productData->getFinalPrice(), true, false),
                        "status" => $productData->getStatus(),
                        "store_id" => $productData->getStoreId(),
                    ];

                    $this->appEmulation->stopEnvironmentEmulation();
                }
            } else {
                $data = [['status' => 'No Data', 'message' => __('There are no Recommended data in this website.')]];
                return $data;
            }
        } else {
            $data = [['status' => 'No Data', 'message' => __('There are no Recommended data in this website.')]];
            return $data;
        }
        return $data;
    }
}
