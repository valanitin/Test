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
 * Class Wishlist
 * @package Revered\GuestWishlist\Helper
 */
class Wishlist extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $sessionCustomer;

    /**
     * @var \Revered\GuestWishlist\Controller\WishlistProviderInterface
     */
    protected $guestWishlistProvider;
    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * Wishlist constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $sessionCustomer
     * @param \Revered\GuestWishlist\Controller\WishlistProviderInterface $guestWishlistProvider
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $sessionCustomer,
        \Revered\GuestWishlist\Controller\WishlistProviderInterface $guestWishlistProvider,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
    ) {
        $this->guestWishlistProvider = $guestWishlistProvider;
        $this->wishlistProvider = $wishlistProvider;
        $this->sessionCustomer = $sessionCustomer;
        parent::__construct($context);
    }

    public function getProductIds(){
        $productIds = [];
        if($this->sessionCustomer->isLoggedIn()) {
            $wishlist = $this->wishlistProvider->getWishlist();
        } else {
            $wishlist = $this->guestWishlistProvider->getWishlist();
        }
        if($wishlist) {
            $items = $wishlist->getItemCollection();
            foreach ($items as $item) {
                /** @var $item \Magento\Wishlist\Model\Item */
                $productIds[] = $item->getProductId();
            }
        }
        return $productIds;
    }

}
