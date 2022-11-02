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
namespace Revered\GuestWishlist\Cron;

use Revered\GuestWishlist\Model\WishlistFactory;

/**
 * Class WishlistCleaner
 * @package Revered\GuestWishlist\Cron
 */
class WishlistCleaner {

    /**
     * @var WishlistFactory
     */
    protected $wishlistFactory;

    /**
     * @var \Revered\GuestWishlist\Helper\Data
     */
    protected $helper;

    /**
     * WishlistCleaner constructor.
     * @param \Revered\GuestWishlist\Helper\Data $helper
     * @param WishlistFactory $wishlistFactory
     */
    public function __construct(
        \Revered\GuestWishlist\Helper\Data $helper,
        WishlistFactory $wishlistFactory
    )
    {
        $this->helper = $helper;
        $this->wishlistFactory = $wishlistFactory;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $clearTime = date ( 'Y-m-d', strtotime('-'.$this->helper->getCookieLifeTime().'days') );
        $collection = $this->wishlistFactory->create()
            ->getCollection()
            ->addFieldToFilter('updated_at',
                [
                    'lteq' =>$clearTime.' 00:00:00',
                ]
            );
        $collection->walk('delete');
        return $this;
    }
}