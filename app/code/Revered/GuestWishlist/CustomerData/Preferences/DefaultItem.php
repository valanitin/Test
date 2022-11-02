<?php
namespace Revered\GuestWishlist\CustomerData\Preferences;
use Magento\Framework\App\ObjectManager;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;

class DefaultItem extends \Magento\Checkout\CustomerData\DefaultItem
{
/**
 * @var \Magento\Catalog\Helper\Image
 */
protected $imageHelper;

/**
 * @var \Magento\Msrp\Helper\Data
 */
protected $msrpHelper;

/**
 * @var \Magento\Framework\UrlInterface
 */
protected $urlBuilder;

/**
 * @var \Magento\Catalog\Helper\Product\ConfigurationPool
 */
protected $configurationPool;

/**
 * @var \Magento\Checkout\Helper\Data
 */
protected $checkoutHelper;

/**
 * @var \Magento\Framework\Escaper
 */
private $escaper;

/**
 * @var ItemResolverInterface
 */
private $itemResolver;

/**
 * @var \Magento\Wishlist\Helper\Data
 */
private $wishlistHelper;

/**
 * @param \Magento\Catalog\Helper\Image $imageHelper
 * @param \Magento\Msrp\Helper\Data $msrpHelper
 * @param \Magento\Framework\UrlInterface $urlBuilder
 * @param \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool
 * @param \Magento\Checkout\Helper\Data $checkoutHelper
 * @param \Magento\Wishlist\Helper\Data $wishlistHelper
 * @param \Magento\Framework\Escaper|null $escaper
 * @param ItemResolverInterface|null $itemResolver
 * @codeCoverageIgnore
 */
public function __construct(
    \Magento\Catalog\Helper\Image $imageHelper,
    \Magento\Msrp\Helper\Data $msrpHelper,
    \Magento\Framework\UrlInterface $urlBuilder,
    \Magento\Catalog\Helper\Product\ConfigurationPool $configurationPool,
    \Magento\Checkout\Helper\Data $checkoutHelper,
    \Magento\Wishlist\Helper\Data $wishlistHelper,
    \Magento\Framework\Escaper $escaper = null,
    ItemResolverInterface $itemResolver = null
) {
    $this->wishlistHelper = $wishlistHelper;
    $this->escaper = $escaper ?: ObjectManager::getInstance()->get(\Magento\Framework\Escaper::class);
    $this->itemResolver = $itemResolver ?: ObjectManager::getInstance()->get(ItemResolverInterface::class);
    parent::__construct($imageHelper, $msrpHelper, $urlBuilder, $configurationPool, $checkoutHelper);
}

/**
 * {@inheritdoc}
 */
protected function doGetItemData()
{
    $imageHelper = $this->imageHelper->init($this->getProductForThumbnail(), 'mini_cart_product_thumbnail');
    $productName = $this->escaper->escapeHtml($this->item->getProduct()->getName());

    return [
        'options' => $this->getOptionList(),
        'qty' => $this->item->getQty() * 1,
        'item_id' => $this->item->getId(),
        'configure_url' => $this->getConfigureUrl(),
        'wishlist_data' => $this->getAddToWishlistParams(),
        'is_visible_in_site_visibility' => $this->item->getProduct()->isVisibleInSiteVisibility(),
        'product_id' => $this->item->getProduct()->getId(),
        'product_name' => $productName,
        'product_sku' => $this->item->getProduct()->getSku(),
        'product_url' => $this->getProductUrl(),
        'product_has_url' => $this->hasProductUrl(),
        'product_price' => $this->checkoutHelper->formatPrice($this->item->getCalculationPrice()),
        'product_price_value' => $this->item->getCalculationPrice(),
        'product_image' => [
            'src' => $imageHelper->getUrl(),
            'alt' => $imageHelper->getLabel(),
            'width' => $imageHelper->getWidth(),
            'height' => $imageHelper->getHeight(),
        ],
        'canApplyMsrp' => $this->msrpHelper->isShowBeforeOrderConfirm($this->item->getProduct())
            && $this->msrpHelper->isMinimalPriceLessMsrp($this->item->getProduct()),
    ];
}

protected function getAddToWishlistParams()
{
    $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/custom.log');
 $logger = new \Zend_Log();
 $logger->addWriter($writer);
 $logger->info(print_r('item id '. $this->item->getId(),true));
    $params = ['qty' => $this->item->getQty()];
    $postData = $this->wishlistHelper->getMoveFromCartParams($this->item->getId());
    $postData = json_decode($postData);
    $postData->data->confirmation = 1;
    $postData->data->confirmationMessage = 'Are you sure, you want to add this item to Wishlist?';

    return json_encode($postData);
}


}
