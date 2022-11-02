<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_GeoIPAutoSwitchStore
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */

namespace Bss\GeoIPAutoSwitchStore\Cookie;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\GenericFactory;
use Bss\GeoIPAutoSwitchStore\Helper\Data as Helper;

/**
 * Class GeoIp
 * @package Bss\GeoIPAutoSwitchStore\Cookie
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 */
class GeoIp
{
    /**
     * Name of cookie that holds private content version
     */
    const COOKIE_NAME = 'country_code';
    const COOKIE_URL = 'redirect_url';
    const COOKIE_STATUS_CURRENCY = 'status_currency';
    const COOKIE_REMEMBER_POPUP = 'remember_popup';

    /**
     * CookieManager
     *
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var GenericFactory
     */
    private $sessionGeneric;
    private $helper;

    /**
     * GeoIp constructor.
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param SessionManagerInterface $sessionManager
     * @param GenericFactory $sessionGeneric
     */
    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        GenericFactory $sessionGeneric,
        Helper $helper
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        $this->sessionGeneric = $sessionGeneric;
        $this->helper = $helper;
    }

    /**
     * Get form key cookie
     * @return string
     */
    public function get()
    {
        return $this->cookieManager->getCookie(self::COOKIE_NAME);
    }

    /**
     * @param string $value
     * @param int $duration
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function set($value, $duration = 86400)
    {
        $duration = (String)$duration;
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath("/")
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->setPublicCookie(
            self::COOKIE_NAME,
            $value,
            $metadata
        );
    }

    /**
     * Get form key cookie
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->cookieManager->getCookie(self::COOKIE_URL);
    }

    /**
     * @param string $value
     * @param int $duration
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function setUrl($value, $duration = 86400)
    {
        $duration = (String)$duration;
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath("/")
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->setPublicCookie(
            self::COOKIE_URL,
            $value,
            $metadata
        );
    }

    /**
     * Get form key cookie
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->cookieManager->getCookie(self::COOKIE_STATUS_CURRENCY);
    }

    /**
     * @param string $value
     * @param int $duration
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function setCurrency($value, $duration = 86400)
    {
        $duration = (String)$duration;
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath("/")
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->setPublicCookie(
            self::COOKIE_STATUS_CURRENCY,
            $value,
            $metadata
        );
    }

    /**
     * Get form key cookie
     *
     * @return string
     */
    public function getRememberPopup()
    {
        return $this->cookieManager->getCookie(self::COOKIE_REMEMBER_POPUP);
    }

    /**
     * @param string $value
     * @param int $duration
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function setRememberPopup($value, $duration = 86400)
    {
        $duration = (String)$duration;
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration($duration)
            ->setPath("/")
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->setPublicCookie(
            self::COOKIE_REMEMBER_POPUP,
            $value,
            $metadata
        );
    }

    /**
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function delete()
    {
        $this->cookieManager->deleteCookie(
            self::COOKIE_NAME,
            $this->cookieMetadataFactory
                ->createCookieMetadata()
                ->setPath($this->sessionManager->getCookiePath())
                ->setDomain($this->sessionManager->getCookieDomain())
        );
    }

    /**
     * @param string $countryCode
     * @param string $enableCookie
     * @param string $ipForTester
     * @param int $storeId
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException
     * @throws \Magento\Framework\Stdlib\Cookie\FailureToSendException
     */
    public function saveCountryCookie($countryCode, $enableCookie, $ipForTester, $storeId)
    {
        $this->sessionGeneric->create()->setCountryCode($countryCode);

        // Save Country Code in cookie?
        if ($enableCookie && $ipForTester == null) {
            $timeCookie = (int)$this->helper->getTimeCookie($storeId);
            $timeCookie = $timeCookie * 24 * 60 * 60;
            $this->set($countryCode, $timeCookie);
        }
    }
}
