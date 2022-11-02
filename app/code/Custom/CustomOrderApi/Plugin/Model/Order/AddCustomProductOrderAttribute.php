<?php

namespace Custom\CustomOrderApi\Plugin\Model\Order;

use Magento\Catalog\Model\ProductFactory;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\Data\OrderItemExtensionFactory;
use Magento\Sales\Api\Data\OrderItemSearchResultInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddCustomProductOrderAttribute
{
    /**
     * @var OrderItemExtensionFactory
     */
    protected $orderItemExtensionFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManagerInterface;

    /**
     * @param OrderItemExtensionFactory $orderItemExtensionFactory
     * @param ProductFactory $productFactory
     */
    public function __construct(
        OrderItemExtensionFactory $orderItemExtensionFactory,
        ProductFactory $productFactory,
        StoreManagerInterface $storeManagerInterface
    ) {
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
        $this->productFactory = $productFactory;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * Add "my_custom_product_attribute" to order item
     *
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemInterface
     */
    protected function addProductImageData(OrderItemInterface $orderItem)
    {
        $product = $this->productFactory->create();
        $product->load($orderItem->getProductId());
        $customAttribute = $product->getImage();

        if (isset($customAttribute)) {
            $storeManager = $this->storeManagerInterface;
            $currentStore = $storeManager->getStore();
            $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $image = $customAttribute != 'no_selection' ? $mediaUrl .'catalog/product'. $customAttribute : $customAttribute;
            $orderItemExtension = $this->orderItemExtensionFactory->create();
            $orderItemExtension->setProductImage($image);

            $orderItem->setExtensionAttributes($orderItemExtension);
        } else {
            $orderItemExtension = $this->orderItemExtensionFactory->create();
            $orderItemExtension->setProductImage('no_selection');
            $orderItem->setExtensionAttributes($orderItemExtension);
        }
        
        return $orderItem;
    }

    /**
     * Add "my_custom_product_attribute" extension attribute to order data object
     * to make it accessible in API data
     *
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $orderItem
     *
     * @return OrderItemInterface
     */
    public function afterGet(OrderItemRepositoryInterface $subject, OrderItemInterface $orderItem)
    {
        $customAttribute = $orderItem->getData('product_image');
 
        $extensionAttributes = $orderItem->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        if (isset($customAttribute)) {
            $extensionAttributes->setProductImage($customAttribute);
        } else {
            $product = $this->productFactory->create();
            $product->load($orderItem->getProductId());
            if ($product) {
                $storeManager = $this->storeManagerInterface;
                $currentStore = $storeManager->getStore();
                $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $image = $product->getImage() != 'no_selection' ? $mediaUrl .'catalog/product'. $product->getImage() : $product->getImage();
                $extensionAttributes->setProductImage($image);
            } else {
                $extensionAttributes->setProductImage('no_selection');
            }
        }
        $orderItem->setExtensionAttributes($extensionAttributes);

        return $orderItem;
    }

    /**
     * Add "my_custom_product_attribute" extension attribute to order data object
     * to make it accessible in API data
     *
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemSearchResultInterface $searchResult
     *
     * @return OrderItemSearchResultInterface
     */
    public function afterGetList(OrderItemRepositoryInterface $subject, OrderItemSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();

        foreach ($orders as &$order) {
            $order = $this->addProductImageData($order);
        }

        return $searchResult;
    }
}