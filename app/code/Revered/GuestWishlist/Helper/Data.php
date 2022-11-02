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
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
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
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $sessionCustomer;

    /**
     * @var Config
     */
    protected $config;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $sessionCustomer
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $sessionCustomer,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Revered\GuestWishlist\Helper\Config $config
    ) {
        $this->sessionConfig = $sessionConfig;
        $this->_cookieManager = $cookieManager;
        $this->_cookieMetadataFactory = $cookieMetadataFactory;
        $this->formKey = $formKey;
        $this->sessionCustomer = $sessionCustomer;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->config->isEnabled();
    }

    /**
     * @return bool
     */
    public function isSharingEnabled()
    {
        return $this->config->isSharingEnabled();
    }

    /**
     * @return bool
     */
    public function isMerged() {
        return $this->config->isMerged();
    }

    /**
     * @return int|mixed
     */
    public function getCookieLifeTime()
    {
        return $this->config->getCookieLifeTime();
    }

    public function resetGuestCustomerId(){
        return $this->config->resetGuestCustomerId();
    }

    /**
     * @return null|string
     */
    public function getGuestCustomerId(){
        return $this->config->getGuestCustomerId();
    }

    /**
     * @return string
     */
    public function getFormKey(){
        return $this->formKey->getFormKey();
    }

    /**
     * @return string
     */
    public function getWishlistUrl() {
        if($this->sessionCustomer->isLoggedIn()) {
            return $this->_getUrl('wishlist/index/index');
        } else {
            return $this->_getUrl('guestwishlist/index/index');
        }
    }

}
