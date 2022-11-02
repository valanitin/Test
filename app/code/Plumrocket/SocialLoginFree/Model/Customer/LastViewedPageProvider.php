<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Customer;

use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * @since 3.2.0
 */
class LastViewedPageProvider
{

    public const REFERER_QUERY_PARAM_NAME = 'pslogin_referer';

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     */
    public function __construct(CookieManagerInterface $cookieManager)
    {
        $this->cookieManager = $cookieManager;
    }

    /**
     * Get last viewed url.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return $this->cookieManager->getCookie(self::REFERER_QUERY_PARAM_NAME, '');
    }

    /**
     * Redirect to exceptions.
     *
     * @return string[]
     */
    public function getRefererLinkSkipModules(): array
    {
        return ['customer/account', 'pslogin/account'];
    }
}
