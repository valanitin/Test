<?php
namespace Revered\GuestWishlist\Controller\Shared;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry = null;

    /**
     * @var WishlistProvider
     */
    protected $wishlistProvider;

    /**
     * Index constructor.
     * @param Context $context
     * @param WishlistProvider $wishlistProvider
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        \Revered\GuestWishlist\Controller\Shared\WishlistProvider $wishlistProvider,
        \Magento\Framework\Registry $registry
    ) {
        $this->wishlistProvider = $wishlistProvider;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * Shared wishlist view page
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
        $this->registry->register('shared_wishlist', $wishlist);
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}
