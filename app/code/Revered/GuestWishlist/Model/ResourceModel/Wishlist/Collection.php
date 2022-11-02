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
namespace Revered\GuestWishlist\Model\ResourceModel\Wishlist;

/**
 * Class Collection
 * @package Revered\GuestWishlist\Model\ResourceModel\Wishlist
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Revered\GuestWishlist\Model\Wishlist::class, \Revered\GuestWishlist\Model\ResourceModel\Wishlist::class);
    }

    /**
     * Filter collection by customer id
     *
     * @param int $customerId
     * @return $this
     */
    public function filterByGuestCustomerId($customerId)
    {
        $this->addFieldToFilter('guest_customer_id', $customerId);
        return $this;
    }

    /**
     * Filter collection by customer ids
     *
     * @param array $customerIds
     * @return $this
     */
    public function filterByGuestCustomerIds(array $customerIds)
    {
        $this->addFieldToFilter('guest_customer_id', ['in' => $customerIds]);
        return $this;
    }
}
