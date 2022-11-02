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
namespace Revered\GuestWishlist\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer as EventObserver;

/**
 * Class CustomerEventAbstract
 * @package Revered\GuestWishlist\Observer
 */
abstract class CustomerEventAbstract implements ObserverInterface
{
    /**
     * @var \Revered\GuestWishlist\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $wishlistProvider;

    /**
     * @var \Magento\Wishlist\Controller\WishlistProviderInterface
     */
    protected $guestWishlistProvider;

    /**
     * @var \Magento\Wishlist\Helper\Data
     */
    protected $wishlistHelper;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * CustomerAuthenticated constructor.
     * @param \Revered\GuestWishlist\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider
     * @param \Revered\GuestWishlist\Controller\WishlistProviderInterface $guestWishlistProvider
     * @param \Magento\Wishlist\Helper\Data $wishlistHelper
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Revered\GuestWishlist\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Revered\GuestWishlist\Controller\WishlistProviderInterface $guestWishlistProvider,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->helper = $helper;
        $this->customerSession = $customerSession;
        $this->wishlistProvider= $wishlistProvider;
        $this->guestWishlistProvider = $guestWishlistProvider;
        $this->wishlistHelper = $wishlistHelper;
        $this->_eventManager = $eventManager;
    }


    /**
     * @param $customer
     * @return $this
     */
    protected function mergeWishList($customer){

        $this->customerSession->loginById($customer->getId());
        $session = $this->customerSession;

        $guestWishlist = $this->guestWishlistProvider->getWishlist();

        if (!$guestWishlist) {
            return $this;
        }

        $wishlist = $this->wishlistProvider->getWishlist();
        if (!$wishlist) {
            return $this;
        }

        $collection = $guestWishlist->getItemCollection();
        if(count($collection)) {
            foreach ($collection as $item) {
                if (!$item->getOptionByCode('info_buyRequest')) {
                    continue;
                }
                try {
                    $buyRequest = $item->getOptionByCode('info_buyRequest');
                    $result = $wishlist->addNewItem($item->getProduct(), $buyRequest->getValue());
                    if (is_string($result)) {
                        continue;
                    }

                    $wishlist->save();
                    $this->_eventManager->dispatch(
                        'wishlist_add_product',
                        ['wishlist' => $wishlist, 'product' => $item->getProduct(), 'item' => $result]
                    );

                    $referer = $session->getBeforeWishlistUrl();
                    if ($referer) {
                        $session->setBeforeWishlistUrl(null);
                    }

                    $this->wishlistHelper->calculate();

                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    continue;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        $this->helper->resetGuestCustomerId();
    }
}