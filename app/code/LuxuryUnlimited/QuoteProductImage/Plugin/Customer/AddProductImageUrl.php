<?php

namespace LuxuryUnlimited\QuoteProductImage\Plugin\Customer;

use Magento\Catalog\Api\ProductRepositoryInterfaceFactory as ProductRepository;
use Magento\Catalog\Model\Product;
use Magento\Framework\UrlInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\TotalsItemExtensionInterfaceFactory;
use Magento\Quote\Model\Quote\ItemFactory;
use Magento\Quote\Model\ResourceModel\Quote\Item;
use Magento\Store\Model\StoreManagerInterface as StoreManager;

class AddProductImageUrl
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
     * @var TotalsItemExtensionInterfaceFactory
     */

    protected $totalsItemExtensionInterfaceFactory;
    /** @var ItemFactory  */

    private $quoteItemFactory;

    /** @var Item  */
    private $itemResourceModel;

    /**
     * AddProductImageUrl constructor.
     *
     * @param ItemFactory $quoteItemFactory
     * @param Item $itemResourceModel
     * @param ProductRepository $productRepository
     * @param StoreManager $storeManager
     * @param TotalsItemExtensionInterfaceFactory $totalsItemExtensionInterfaceFactory
     */
    public function __construct(
        ItemFactory $quoteItemFactory,
        Item $itemResourceModel,
        ProductRepository $productRepository,
        StoreManager $storeManager,
        TotalsItemExtensionInterfaceFactory $totalsItemExtensionInterfaceFactory
    ) {
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemResourceModel = $itemResourceModel;
        $this->productRepository = $productRepository;
        $this->storeManager = $storeManager;
        $this->totalsItemExtensionInterfaceFactory = $totalsItemExtensionInterfaceFactory;
    }

    /**
     * Add Product image url
     *
     * @param CartTotalRepositoryInterface $subject
     * @param mixed $result
     * @param int $cartId
     * @return mixed $result
     *
     * @SuppressWarnings(PHPMD)
     */

    public function afterGet(CartTotalRepositoryInterface $subject, $result, $cartId)
    {
        foreach ($result->getItems() as $item) {
            $productId = $this->getProduct($item->getItemId());
            if ($productId == null) {
                return $result;
            }
            $extensionAttributes = $item->getExtensionAttributes();
            $product = $this->productRepository->create()->getById($productId);
            $imageurl = $this->getImageUrl($product);
            if ($imageurl != null) {
                if (!$extensionAttributes) {
                    $extensionAttributes = $this->totalsItemExtensionInterfaceFactory->create();
                }
                $extensionAttributes->setProductImage(($imageurl != null) ? $imageurl : '');

                $item->setExtensionAttributes($extensionAttributes);
            }
        }

        return $result;
    }

    /**
     * Get product
     *
     * @param int $itemId
     * @return mixed
     */
    protected function getProduct($itemId)
    {
        $quoteItem = $this->quoteItemFactory->create();
        $this->itemResourceModel->load($quoteItem, $itemId);
        return $quoteItem->getProductId();
    }

    /**
     * Get image url
     *
     * @param mixed $product
     * @return string|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getImageUrl($product)
    {
        $currentStore = $this->storeManager->getStore();
        $customAttribute = $product->getImage();
        if ($customAttribute == '' || $customAttribute == null) {
            return null;
        }
        // @phpstan-ignore-next-line
        $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
        $imageUrl =
            $customAttribute != 'no_selection' ? $mediaUrl . 'catalog/product' . $customAttribute : $customAttribute;
        return $imageUrl;
    }
}
