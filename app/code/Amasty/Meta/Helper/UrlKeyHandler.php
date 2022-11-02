<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Helper;

use Amasty\Meta\Model\ConfigProvider;
use Amasty\Meta\Model\ResourceModel\EavResource;
use Amasty\Meta\Model\ResourceModel\Product;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;
use Magento\UrlRewrite\Model\UrlRewrite;
use Magento\UrlRewrite\Model\UrlRewriteFactory;

class UrlKeyHandler extends AbstractHelper
{
    const BASE_PRODUCT_TARGET_PATH = 'catalog/product/view/id/%d';

    const BASE_PRODUCT_CATEGORY_TARGET_PATH = 'catalog/product/view/id/%d/category/%d';

    /**
     * @var int
     */
    protected $productTypeId;

    /**
     * @var int
     */
    protected $urlPathId;

    /**
     * @var int
     */
    protected $urlKeyId;

    /**
     * @var int
     */
    protected $pageSize = 100;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var UrlRewriteCollectionFactory
     */
    private $rewriteCollectionFactory;

    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var UrlRewriteFactory
     */
    private $urlRewriteFactory;

    /**
     * @var \Amasty\Meta\Model\ResourceModel\UrlRewrite
     */
    private $urlRewrite;

    /**
     * @var EavResource
     */
    private $eavResource;

    /**
     * @var Product
     */
    private $productResource;

    public function __construct(
        Context $context,
        Data $helperData,
        UrlRewriteCollectionFactory $rewriteCollectionFactory,
        ProductMetadataInterface $productMetadata,
        ConfigProvider $configProvider,
        UrlRewriteFactory $urlRewriteFactory,
        EavResource $eavResource,
        Product $productResource,
        \Amasty\Meta\Model\ResourceModel\UrlRewrite $urlRewrite
    ) {
        $this->helperData = $helperData;
        $this->eavResource = $eavResource;
        parent::__construct($context);
        $this->_construct();
        $this->rewriteCollectionFactory = $rewriteCollectionFactory;
        $this->productMetadata = $productMetadata;
        $this->configProvider = $configProvider;
        $this->urlRewriteFactory = $urlRewriteFactory;
        $this->urlRewrite = $urlRewrite;
        $this->productResource = $productResource;
    }

    public function _construct()
    {
        $this->productTypeId = $this->eavResource->getProductTypeId();
        $this->urlPathId = $this->eavResource->getUrlPathId($this->productTypeId);
        $this->urlKeyId = $this->eavResource->getUrlKeyId($this->productTypeId);
    }

    /**
     * @param        $product
     * @param        $store
     * @param string $urlKeyTemplate
     */
    public function processProduct($product, $store, $withRedirect = false, $urlKeyTemplate = '')
    {
        if (empty($urlKeyTemplate)) {
            $urlKeyTemplate = $this->configProvider->getProductTemplate($store->getCode());
        }

        if (empty($urlKeyTemplate)) {
            return;
        }

        $storeId = ($store && $store->getId()) ? $store->getId() : 0;
        $product->setStoreId($storeId);
        $urlKey = $this->helperData->cleanEntityToCollection()
            ->addEntityToCollection($product)
            ->parse($urlKeyTemplate, true);

        $urlKey = $product->formatUrlKey($urlKey);

        //update url_key
        $this->_updateUrlKey($product, $storeId, $urlKey);

        $urlSuffix = $this->scopeConfig->getValue(
            'catalog/seo/product_url_suffix',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        //update url_path
        $this->updateUrlPath($product, $storeId, $urlKey, $urlSuffix);
        $this->updateUrlRewrite($product, $storeId, $urlKey, $withRedirect, $urlSuffix);

        $product->setUrlKey($urlKey);
    }

    /**
     * @param $product
     * @param $storeId
     * @param $urlKey
     * @param string $urlSuffix
     */
    protected function _updateUrlKey($product, $storeId, $urlKey, $urlSuffix = '')
    {
        $this->_updateAttribute($this->urlKeyId, $product, $storeId, $urlKey, $urlSuffix);
    }

    /**
     * @param $product
     * @param $storeId
     * @param $urlKey
     * @param string $urlSuffix
     */
    protected function updateUrlPath($product, $storeId, $urlKey, $urlSuffix = '')
    {
        $this->_updateAttribute($this->urlPathId, $product, $storeId, $urlKey, $urlSuffix);
    }

    /**
     * @param $product
     * @param $storeId
     * @param $urlKey
     * @param bool $withRedirect
     * @param string $urlSuffix
     */
    protected function updateUrlRewrite($product, $storeId, $urlKey, $withRedirect, $urlSuffix = '')
    {
        /** @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $collection */
        $collection = $this->rewriteCollectionFactory->create();
        $collection->addStoreFilter([$storeId]);
        $productPath = 'catalog/product/view/id/';
        $collection->getSelect()->where(
            '(target_path = ?',
            $productPath . $product->getId()
        )
        ->orWhere('target_path like ?)', $productPath . $product->getId() . '/%');

        if ($collection->getSize()) {
            foreach ($collection as $urlRewrite) {
                $requestPath = $urlRewrite->getRequestPath();
                $requestPathArray = explode('/', $requestPath);
                $oldPath = end($requestPathArray);
                $newPath = $urlKey;
                if ($urlSuffix && strpos($oldPath, $urlSuffix) !== false) {
                    $newPath .= $urlSuffix;
                }

                $newPath = str_replace($oldPath, $newPath, $requestPath);
                $urlRewrite->setRequestPath($newPath);
                try {
                    $this->saveUrlRewrites(
                        (int)$product->getId(),
                        $requestPath,
                        (int)$storeId,
                        $urlRewrite,
                        $withRedirect,
                        $newPath
                    );
                } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/amasty_meta.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $message = __(
                        'Request path "%1" for product with ID %2 is already exists',
                        $urlRewrite->getRequestPath(),
                        $urlRewrite->getEntityId()
                    );
                    $logger->warn($message);
                    continue;
                }
            }
        }
    }

    /**
     * @param int $productId
     * @param string $requestPath
     * @param int $storeId
     * @param UrlRewrite $urlRewrite
     * @param bool $withRedirect
     * @param string$newPath
     * @throws \Exception
     */
    private function saveUrlRewrites($productId, $requestPath, $storeId, $urlRewrite, $withRedirect, $newPath)
    {
        $this->urlRewrite->deleteByRequestPathAndStore($requestPath, $storeId);
        $this->urlRewrite->save($urlRewrite);
        if ($withRedirect) {
            $this->addRewriteUrls($productId, $requestPath, $newPath, $storeId);
        }
    }

    /**
     * @param int $productId
     * @param string $oldPath
     * @param string $newPath
     * @param int $storeId
     * @throws \Exception
     */
    private function addRewriteUrls($productId, $oldPath, $newPath, $storeId)
    {
        if ($newPath !== $oldPath) {
            $urlRewrite = $this->urlRewriteFactory->create();
            $urlRewrite->setTargetPath($newPath);
            $urlRewrite->setRequestPath($oldPath);
            $urlRewrite->setEntityType('product');
            $urlRewrite->setEntityId($productId);
            $urlRewrite->setRedirectType(\Magento\UrlRewrite\Model\OptionProvider::TEMPORARY);
            $urlRewrite->setStoreId($storeId);
            $this->urlRewrite->save($urlRewrite);
        }
    }

    /**
     * @param $attributeId
     * @param $product
     * @param $storeId
     * @param $urlKey
     * @param $urlSuffix
     */
    protected function _updateAttribute($attributeId, $product, $storeId, $urlKey, $urlSuffix)
    {
        $entityField = $this->productMetadata->getEdition() != 'Community' ? 'row_id' : 'entity_id';
        $entityValue = $product->getData($entityField);
        $attributeId = (int)$attributeId;
        $storeId = (int)$storeId;

        $row = $this->productResource->getAttributeValue($attributeId, $entityField, $entityValue, $storeId);

        $value = $urlKey . $urlSuffix;
        if ($row) {
            $this->productResource->updateAttributeValue($value, $attributeId, $entityField, $entityValue, $storeId);
        } else {
            $data = [
                'attribute_id' => $attributeId,
                $entityField => $entityValue,
                'store_id' => $storeId,
                'value' => $value
            ];
            $this->productResource->createAttributeValue($data);
        }
    }

    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param int $productId
     * @param int|null $categoryId
     * @return string
     */
    protected function _getProductTargetPath($productId, $categoryId = null)
    {
        return empty($categoryId) ?
            sprintf(self::BASE_PRODUCT_TARGET_PATH, $productId) :
            sprintf(self::BASE_PRODUCT_CATEGORY_TARGET_PATH, $productId, $categoryId);
    }
}
