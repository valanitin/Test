<?php
/**
 * Revered Technologies Pvt. Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 *
 ********************************************************************
 * @category   Revered
 * @package    Revered_GuestWishlist
 * @copyright  Copyright (c) Revered Technologies Pvt. Ltd. (http://www.reveredtech.com)
 * @license    http://store.reveredtech.com/Revered-LICENSE-COMMUNITY.txt
 */
namespace Revered\GuestWishlist\Helper;

/**
 * Class WishlistData
 * @package Revered\GuestWishlist\Helper
 */
class WishlistData extends \Magento\Wishlist\Helper\Data
{
    /**
     * @var \Revered\GuestWishlist\Controller\WishlistProviderInterface
     */
    protected $guestWishlistProvider;

    /**
     * @var Data
     */
    protected $dataHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Revered\GuestWishlist\Controller\WishlistProviderInterface $guestWishlistProvider,
        \Revered\GuestWishlist\Helper\Data $dataHelper
    ) {
        parent::__construct($context, $coreRegistry, $customerSession, $wishlistFactory, $storeManager, $postDataHelper,
            $customerViewHelper, $wishlistProvider, $productRepository);
        $this->guestWishlistProvider = $guestWishlistProvider;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Retrieve wishlist by logged in customer
     *
     * @return \Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist()
    {
        if(!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            if ($this->_coreRegistry->registry('shared_wishlist')) {
                $this->_wishlist = $this->_coreRegistry->registry('shared_wishlist');
            } else {
                $this->_wishlist = $this->guestWishlistProvider->getWishlist();
            }
        } else {
            $this->_wishlist = parent::getWishlist();
        }
        return $this->_wishlist;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item $item|\Revered\GuestWishlist\Model\Item
     * @param array $params
     * @return string
     */
    public function getAddParams($item, array $params = [])
    {
        if(!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            $productId = null;
            if ($item instanceof \Magento\Catalog\Model\Product) {
                $productId = $item->getEntityId();
            }
            if ($item instanceof \Revered\GuestWishlist\Model\Item) {
                $productId = $item->getProductId();
            }

            $url = $this->_getUrlStore($item)->getUrl('guestwishlist/index/add');
            if ($productId) {
                $params['product'] = $productId;
            }
            return $this->_postDataHelper->getPostData($url, $params);
        } else {
            return parent::getAddParams($item,$params);
        }
    }


    /**
     * Retrieve params for removing item from wishlist
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item $item
     * @param bool $addReferer
     * @return string
     */
    public function getRemoveParams($item, $addReferer = false)
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            $url = $this->_getUrl('guestwishlist/index/remove');
            $params = ['item' => $item->getWishlistItemId()];
            if ($addReferer) {
                $params = $this->addRefererToParams($params);
            }
            return $this->_postDataHelper->getPostData($url, $params);
        } else {
            return parent::getRemoveParams($item, $addReferer);
        }
    }


    /**
     * Retrieve URL for configuring item from wishlist
     *
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item $item
     * @return string
     */
    public function getConfigureUrl($item)
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            return $this->_getUrl(
                'guestwishlist/index/configure',
                [
                    'id' => $item->getWishlistItemId(),
                    'product_id' => $item->getProductId()
                ]
            );
        } else {
            return parent::getConfigureUrl($item);
        }

    }
    /**
     * Retrieve params for adding product to wishlist
     *
     * @param int $itemId
     *
     * @return string
     */
    public function getMoveFromCartParams($itemId)
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            $url = $this->_getUrl('guestwishlist/index/fromcart');
            $params = ['item' => $itemId];
            return $this->_postDataHelper->getPostData($url, $params);
        } else {
            return parent::getMoveFromCartParams($itemId);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item $item
     * @return bool|false|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUpdateParams($item)
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            $itemId = null;
            if ($item instanceof \Magento\Catalog\Model\Product) {
                $itemId = $item->getWishlistItemId();
                $productId = $item->getId();
            }
            if ($item instanceof \Revered\GuestWishlist\Model\Item) {
                $itemId = $item->getId();
                $productId = $item->getProduct()->getId();
            }

            $url = $this->_getUrl('guestwishlist/index/updateItemOptions');
            if ($itemId) {
                $params = ['id' => $itemId, 'product' => $productId, 'qty' => $item->getQty()];
                return $this->_postDataHelper->getPostData($url, $params);
            }
        } else {
            return parent::getUpdateParams($item);
        }
        return false;
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item|string $item
     * @return string
     */
    public function getAddToCartUrl($item)
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            return $this->_getUrlStore($item)->getUrl('guestwishlist/index/cart', $this->_getCartUrlParameters($item));
        } else {
            return parent::getAddToCartUrl($item);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item|string $item
     * @param bool $addReferer
     * @return string
     */
    public function getAddToCartParams($item, $addReferer = false)
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            $params = $this->_getCartUrlParameters($item);
            if ($addReferer) {
                $params = $this->addRefererToParams($params);
            }
            return $this->_postDataHelper->getPostData(
                $this->_getUrlStore($item)->getUrl('guestwishlist/index/cart'),
                $params
            );
        } else {
            return parent::getAddToCartParams($item,$addReferer);

        }
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item|string $item
     * @return string
     */
    public function getSharedAddToCartUrl($item)
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            return $this->_postDataHelper->getPostData(
                $this->_getUrlStore($item)->getUrl('guestwishlist/shared/cart'),
                $this->_getCartUrlParameters($item)
            );
        } else {
            return parent::getSharedAddToCartUrl($item);
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product|\Magento\Wishlist\Model\Item|string $item
     * @return array
     */
    protected function _getCartUrlParameters($item)
    {
        $params = [
            'item' => is_string($item) ? $item : $item->getWishlistItemId(),
        ];
        if ($item instanceof \Magento\Wishlist\Model\Item) {
            $params['qty'] = $item->getQty();
        }

        if ($item instanceof \Revered\GuestWishlist\Model\Item) {
            $params['qty'] = $item->getQty();
        }

        return $params;
    }

    /**
     * Retrieve wishlist item count (include config settings)
     * Used in top link menu only
     *
     * @return int
     */
    public function getItemCount()
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            $wishlist = $this->guestWishlistProvider->getWishlist();
            if($wishlist)
            {
                return $wishlist->getItemsCount();
            }
            return 0;
        } else {
            return parent::getItemCount();
        }
    }

    /**
     * Retrieve customer wishlist url
     *
     * @param int $wishlistId
     * @return string
     */
    public function getListUrl($wishlistId = null)
    {
        if (!$this->_customerSession->isLoggedIn() && $this->dataHelper->isEnabled()) {
            return $this->_getUrl('guestwishlist');
        } else {
            return parent::getListUrl($wishlistId);
        }
    }
}
