<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Amp v2.x.x
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Block\Customer\Account;

/**
 * Class View
 * @package Plumrocket\Amp\Block\Customer\Account
 */
class AuthorizationLink extends  \Magento\Customer\Block\Account\AuthorizationLink
{
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->isLoggedIn()
            ? $this->_customerUrl->getAccountUrl()
            : $this->_customerUrl->getLoginUrl();
    }
}
