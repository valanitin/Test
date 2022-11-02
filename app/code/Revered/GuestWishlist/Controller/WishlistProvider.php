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
namespace Revered\GuestWishlist\Controller;

use Revered\GuestWishlist\Helper\Data;
use Magento\Framework\App\RequestInterface;

/**
 * Class WishlistProvider
 * @package Revered\GuestWishlist\Controller
 */
class WishlistProvider implements WishlistProviderInterface
{
    /**
     * @var \Revered\GuestWishlist\Model\Wishlist
     */
    protected $wishlist;

    /**
     * @var \Revered\GuestWishlist\Model\WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * WishlistProvider constructor.
     * @param \Revered\GuestWishlist\Model\WishlistFactory $wishlistFactory
     * @param Data $dataHelper
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param RequestInterface $request
     */
    public function __construct(
        \Revered\GuestWishlist\Model\WishlistFactory $wishlistFactory,
        \Revered\GuestWishlist\Helper\Data $dataHelper,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        RequestInterface $request
    ) {
        $this->request = $request;
        $this->wishlistFactory = $wishlistFactory;
        $this->messageManager = $messageManager;
        $this->dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getWishlist()
    {
        if ($this->wishlist) {
            return $this->wishlist;
        }
        try {
            $guestCustomerId = $this->dataHelper->getGuestCustomerId();
            $wishlist = $this->wishlistFactory->create();
            $wishlist->loadByGuestCustomerId($guestCustomerId, true);
            if (!$wishlist->getId() || $wishlist->getGuestCustomerId() != $guestCustomerId) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('The requested Wish List doesn\'t exist.')
                );
            }
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $this->messageManager->addError($e->getMessage());
            return false;
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t create the Guest Wish List right now.'));
            return false;
        }
        $this->wishlist = $wishlist;
        return $wishlist;
    }
}
