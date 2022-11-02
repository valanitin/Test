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
namespace Revered\GuestWishlist\Model\ResourceModel\Item;

/**
 * Class Option
 * @package Revered\GuestWishlist\Model\ResourceModel\Item
 */
class Option extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('guest_wishlist_item_option', 'option_id');
    }
}
