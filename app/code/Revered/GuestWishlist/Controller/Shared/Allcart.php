<?php

namespace Revered\GuestWishlist\Controller\Shared;

use Magento\Framework\App\Action\Context;
use Revered\GuestWishlist\Model\ItemCarrier;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Allcart
 * @package Revered\GuestWishlist\Controller\Shared
 */
class Allcart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var WishlistProvider
     */
    protected $wishlistProvider;

    /**
     * @var ItemCarrier
     */
    protected $itemCarrier;

    /**
     * Allcart constructor.
     * @param Context $context
     * @param WishlistProvider $wishlistProvider
     * @param ItemCarrier $itemCarrier
     */
    public function __construct(
        Context $context,
        \Revered\GuestWishlist\Controller\Shared\WishlistProvider $wishlistProvider,
        ItemCarrier $itemCarrier
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->itemCarrier = $itemCarrier;
        parent::__construct($context);
    }

    /**
     * Add all items from wishlist to shopping cart
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $redirectUrl = $this->itemCarrier
            ->setIsOwner(false)
            ->moveAllToCart($wishlist, $this->getRequest()->getParam('qty'));
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($redirectUrl);
        return $resultRedirect;
    }
}
