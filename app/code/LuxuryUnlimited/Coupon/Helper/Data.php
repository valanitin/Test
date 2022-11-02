<?php
/**
 * @author      LuxuryUnlimited
 * @copyright   Copyright © 2022. All rights reserved.
 */

namespace LuxuryUnlimited\Coupon\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;

class Data extends AbstractHelper
{
    const ERRORMESSAGE_ENABLED    = 'coupon/general/enable';
    const COUPON_EXIST    = 'coupon/general/coupon_exist';
    const CONDTION_FAILED    = 'coupon/general/condition_fail';
    const COUPON_EXPIRED    = 'coupon/general/coupon_expired';
    const COUPON_CUSTOMER_GROUP    = 'coupon/general/coupon_customer_group';
    const COUPON_WEBSITE_ID    = 'coupon/general/coupon_website_id';
    const COUPON_USAGES    = 'coupon/general/coupon_usages';
    const SUCCESS_MESSAGE    = 'coupon/general/success_message';
    const CANCEL_MESSAGE    = 'coupon/general/cancel_coupon';
    const EXCEPTION_MESSAGE    = 'coupon/general/exception_message';

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Context $context
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        Context $context, 
        EncryptorInterface $encryptor,
        StoreManagerInterface $storeManager,
        Session $customerSession)
    {
        parent::__construct($context);
        $this->encryptor = $encryptor;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
    }

    /**
     * @return bool
     */
    public function isEnabled($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->isSetFlag(self::ERRORMESSAGE_ENABLED, $scope);
    }

    /**
     * @return string
     */
    public function isCouponExits($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_EXIST, $scope);
    }

    /**
     * @return string
     */
    public function isConditionFail($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::CONDTION_FAILED, $scope);
    }

    /**
     * @return string
     */
    public function isCouponExpired($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_EXPIRED, $scope);
    }

    /**
     * @return string
     */
    public function isCouponCustomerGroup($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_CUSTOMER_GROUP, $scope);
    }

    /**
     * @return string
     */
    public function isCouponWebsite($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_WEBSITE_ID, $scope);
    }

    /**
     * @return string
     */
    public function isCouponUsage($scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT)
    {
        return $this->scopeConfig->getValue(self::COUPON_USAGES, $scope);
    }
    /**
     * @return string
     */
    public function getSuccessMessage($couponCode, $discount){
       $msg =  $this->scopeConfig->getValue(self::SUCCESS_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
       $msg = str_replace("%s", $couponCode, $msg);
       if($discount){
           $msg = str_replace("%c", $discount, $msg);
       }
       return $msg;
    }

    /**
     * @return string
     */
    public function getExceptionMessage($couponCode){
        $msg =  $this->scopeConfig->getValue(self::EXCEPTION_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $msg=str_replace("%s", $couponCode, $msg);
        return $msg;
     }

     /**
     * @return string
     */
    public function getCancelMessage($couponCode){
        $msg =  $this->scopeConfig->getValue(self::CANCEL_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
        $msg=str_replace("%s", $couponCode, $msg);
        return $msg;
     }

     /**
     * Get Current Customer group Id.
     *
     * @return int customerGroupId
     */
    public function getCustomerGroupId()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_customerSession->getCustomer()->getGroupId();
        } else {
            return 0;
        }
    }

    /**
     * Get Current Website Id.
     *
     * @return int websiteId
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }
}