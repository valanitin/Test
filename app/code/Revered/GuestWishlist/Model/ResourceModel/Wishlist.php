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
namespace Revered\GuestWishlist\Model\ResourceModel;

/**
 * Class Wishlist
 * @package Revered\GuestWishlist\Model\ResourceModel
 */
class Wishlist extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Store wishlist items count
     *
     * @var null|int
     */
    protected $_itemsCount = null;

    /**
     * Store customer ID field name
     *
     * @var string
     */
    protected $_customerIdFieldName = 'guest_customer_id';

    /**
     * Set main entity table name and primary key field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('guest_wishlist', 'wishlist_id');
    }

    /**
     * Prepare wishlist load select query
     *
     * @param string $field
     * @param mixed $value
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return \Magento\Framework\DB\Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        if ($field == $this->_customerIdFieldName) {
            $select->order('wishlist_id ' . \Magento\Framework\DB\Select::SQL_ASC)->limit(1);
        }
        return $select;
    }

    /**
     * Getter for customer ID field name
     *
     * @return string
     */
    public function getGuestCustomerIdFieldName()
    {
        return $this->_customerIdFieldName;
    }

    /**
     * Setter for customer ID field name
     *
     * @param string $fieldName
     * @return $this
     */
    public function setGuestCustomerIdFieldName($fieldName)
    {
        $this->_customerIdFieldName = $fieldName;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setHasDataChanges(true);
        return parent::save($object);
    }
}
