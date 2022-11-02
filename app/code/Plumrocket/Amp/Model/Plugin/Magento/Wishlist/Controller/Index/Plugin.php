<?php
/**
 * Plumrocket Inc.
 * NOTICE OF LICENSE
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

namespace Plumrocket\Amp\Model\Plugin\Magento\Wishlist\Controller\Index;

use Plumrocket\Amp\Helper\Data as DataHelper;

class Plugin extends \Magento\Wishlist\Controller\Index\Plugin
{
    /**
     * Disable magento plugin for amp controllers
     *
     * @param \Magento\Framework\App\ActionInterface  $subject
     * @param \Magento\Framework\App\RequestInterface $request
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function beforeDispatch(
        \Magento\Framework\App\ActionInterface $subject,
        \Magento\Framework\App\RequestInterface $request
    ) {
        if (DataHelper::SECTION_ID !== $request->getModuleName()) {
            parent::beforeDispatch($subject, $request);
        }
    }
}
