<?php

namespace LuxuryUnlimited\QuoteProductImage\Observer;

use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\Data\CartItemExtensionFactory;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

class AddProductImage implements ObserverInterface
{
    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CartItemExtensionFactory
     */
    protected $extensionFactory;

    /**
     * AddProductImage constructor.
     * @param ProductRepository $productRepository
     * @param StoreManager $storeManager
     * @param CartItemExtensionFactory $extensionFactory
     */
    public function __construct(
        ProductRepository $productRepository,
        StoreManager $storeManager,
        CartItemExtensionFactory $extensionFactory
    ) {
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Add Extension attribute to items
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $quote = $observer->getQuote();
        /**
         * Code to add the items attribute to extension_attributes
         */
        foreach ($quote->getAllItems() as $quoteItem) {
            $product = $this->productRepository->create()->getById($quoteItem->getProductId());
            $itemExtAttr = $quoteItem->getExtensionAttributes();
            if ($itemExtAttr === null) {
                $itemExtAttr = $this->extensionFactory->create();
            }
            $imageurl = $this->getImageUrl($product);
            if ($imageurl == null) {
                return;
            }
            $itemExtAttr->setProductImage($imageurl);
            $quoteItem->setExtensionAttributes($itemExtAttr);
        }
        return;
    }

    /**
     * Helper function that provides image url
     *
     * @param mixed $product
     * @return string|null
     */
    protected function getImageUrl($product)
    {
        $currentStore = $this->storeManager->getStore();
        $customAttribute = $product->getImage();
        if ($customAttribute == null || $customAttribute == '') {
            return null;
        }
        // @phpstan-ignore-next-line
        $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $imageUrl =
            $customAttribute != 'no_selection' ? $mediaUrl . 'catalog/product' . $customAttribute : $customAttribute;
        return $imageUrl;
    }
}
