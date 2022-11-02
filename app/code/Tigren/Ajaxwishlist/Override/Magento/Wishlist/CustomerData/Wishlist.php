<?php
/**
 * @author    Tigren Solutions <info@tigren.com>
 * @copyright Copyright (c) 2022 Tigren Solutions <https://www.tigren.com>. All rights reserved.
 * @license   Open Software License ("OSL") v. 3.0
 */

namespace Tigren\Ajaxwishlist\Override\Magento\Wishlist\CustomerData;

use Magento\Catalog\Helper\ImageFactory;
use Magento\Framework\App\ViewInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\Render;
use Magento\Wishlist\Block\Customer\Sidebar;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\Item;
use Tigren\Ajaxwishlist\Block\Js;

/**
 * Class Wishlist
 *
 * @package Tigren\Ajaxwishlist\Override\Magento\Wishlist\CustomerData
 */
class Wishlist extends \Magento\Wishlist\CustomerData\Wishlist
{
    /**
     * @var Js
     */
    protected $_customerWishlistItemsNumber;

    /**
     * Wishlist constructor.
     *
     * @param Data          $wishlistHelper
     * @param Sidebar       $block
     * @param ImageFactory  $imageHelperFactory
     * @param ViewInterface $view
     * @param Js            $customerWishlistItemsNumber
     */
    public function __construct(
        Data $wishlistHelper,
        Sidebar $block,
        ImageFactory $imageHelperFactory,
        ViewInterface $view,
        Js $customerWishlistItemsNumber
    ) {
        $this->_customerWishlistItemsNumber = $customerWishlistItemsNumber;
        parent::__construct($wishlistHelper, $block, $imageHelperFactory, $view);
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    protected function getItems()
    {
        $this->view->loadLayout();

        $collection = $this->wishlistHelper->getWishlistItemCollection();
        $collection->clear()->setPageSize($this->_customerWishlistItemsNumber->getProductsNumber())
            ->setInStockFilter(true)->setOrder('added_at');

        $items = [];
        foreach ($collection as $wishlistItem) {
            $items[] = $this->getItemData($wishlistItem);
        }
        return $items;
    }

    /**
     * @param  Item $wishlistItem
     * @return array
     * @throws LocalizedException
     */
    protected function getItemData(Item $wishlistItem)
    {
        $product = $wishlistItem->getProduct();
        return [
            'image' => $this->getImageData($product),
            'product_url' => $this->wishlistHelper->getProductUrl($wishlistItem),
            'product_name' => $product->getName(),
            'product_id' => $product->getEntityId(),
            'product_price' => $this->block->getProductPriceHtml(
                $product,
                'wishlist_configured_price',
                Render::ZONE_ITEM_LIST,
                ['item' => $wishlistItem]
            ),
            'product_is_saleable_and_visible' => $product->isSaleable() && $product->isVisibleInSiteVisibility(),
            'product_has_required_options' => $product->getTypeInstance()->hasRequiredOptions($product),
            'add_to_cart_params' => $this->wishlistHelper->getAddToCartParams($wishlistItem, true),
            'delete_item_params' => $this->wishlistHelper->getRemoveParams($wishlistItem, true),
        ];
    }
}
