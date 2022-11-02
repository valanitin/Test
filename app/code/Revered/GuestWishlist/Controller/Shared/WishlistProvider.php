<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Revered\GuestWishlist\Controller\Shared;

use Revered\GuestWishlist\Controller\WishlistProviderInterface;

class WishlistProvider implements WishlistProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Revered\GuestWishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Revered\GuestWishlist\Model\Wishlist
     */
    protected $wishlist;

    /**
     * WishlistProvider constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Revered\GuestWishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Revered\GuestWishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->request = $request;
        $this->wishlistFactory = $wishlistFactory;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param null $wishlistId
     * @return \Revered\GuestWishlist\Model\Wishlist|bool|\Magento\Wishlist\Model\Wishlist
     */
    public function getWishlist($wishlistId = null)
    {
        if ($this->wishlist) {
            return $this->wishlist;
        }
        $code = (string)$this->request->getParam('code');
        if (empty($code)) {
            return false;
        }

        $wishlist = $this->wishlistFactory->create()->loadByCode($code);
        if (!$wishlist->getId()) {
            return false;
        }

        $this->checkoutSession->setSharedWishlist($code);
        $this->wishlist = $wishlist;
        return $wishlist;
    }
}
