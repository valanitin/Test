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
namespace Revered\GuestWishlist\Helper;

/**
 * Class Data
 * @package Revered\GuestWishlist\Helper
 */
class Config extends \Magento\Framework\App\Helper\AbstractHelper
{
    const COOKIE_NAME = 'guest_customer_id';
    const CONFIG_ENABLED = 'guestwishlist/settings/enabled';
    const CONFIG_COOKIE_LIFETIME = 'guestwishlist/settings/cookie_lifetime';
    const CONFIG_MERGE = 'guestwishlist/settings/merge';
    const CONFIG_SHARING_ENABLED = 'guestwishlist/settings/sharing';

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    protected $_cookieManager;
    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $_cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Session\Config\ConfigInterface
     */
    private $sessionConfig;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * Config constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Math\Random $random
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Math\Random $random
    ) {
        $this->sessionConfig = $sessionConfig;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->mathRandom = $random;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return ($this->scopeConfig->getValue(self::CONFIG_ENABLED)) ? TRUE : FALSE;
    }


    /**
     * @return bool
     */
    public function isSharingEnabled()
    {
        return ($this->scopeConfig->getValue(self::CONFIG_SHARING_ENABLED)) ? TRUE : FALSE;
    }

    /**
     * @return bool
     */
    public function isMerged() {
        return ($this->scopeConfig->getValue(self::CONFIG_MERGE)) ? true : false;
    }

    /**
     * @return int|mixed
     */
    public function getCookieLifeTime()
    {
        return (int)($this->scopeConfig->getValue(self::CONFIG_COOKIE_LIFETIME) != '') ? $this->scopeConfig->getValue(self::CONFIG_COOKIE_LIFETIME) : 30 ;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function resetGuestCustomerId(){
        $guestCustomerId = $this->mathRandom->getUniqueHash();
        $this->_cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            $guestCustomerId,
            $this->getCookieMetaData()
        );
        return $guestCustomerId;
    }

    /**
     * @return null|string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function getGuestCustomerId(){
        $guestCustomerId =  $this->_cookieManager
            ->getCookie(self::COOKIE_NAME,null);

        if($guestCustomerId == null) {
            $guestCustomerId = $this->resetGuestCustomerId();
        }

        return $guestCustomerId;
    }

    /**
     * @return \Magento\Framework\Stdlib\Cookie\PublicCookieMetadata
     */
    protected function getCookieMetaData() {
        $cookieMetadata = $this->_cookieMetadataFactory
            ->createPublicCookieMetadata();
        $cookieMetadata->setDomain($this->sessionConfig->getCookieDomain());
        $cookieMetadata->setPath($this->sessionConfig->getCookiePath());
        $cookieMetadata->setDuration($this->getCookieLifetime()*86400);
        return $cookieMetadata;
    }

}
