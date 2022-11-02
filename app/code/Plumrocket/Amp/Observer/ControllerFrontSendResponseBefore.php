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
 * @package     Plumrocket_Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Observer;

use Magento\Framework\Event\ObserverInterface;

class ControllerFrontSendResponseBefore implements ObserverInterface
{
    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Plumrocket\Amp\Model\AllowedAmpUrlCacheInterface
     */
    private $cache;

    /**
     * ControllerFrontSendResponseBefore constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data $dataHelper
     * @param \Plumrocket\Amp\Model\AllowedAmpUrlCacheInterface $cache
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $dataHelper,
        \Plumrocket\Amp\Model\AllowedAmpUrlCacheInterface $cache
    ) {
        $this->dataHelper = $dataHelper;
        $this->cache = $cache;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $observer->getRequest();
        $this->addRequestUriToCache(parse_url($request->getRequestUri(), PHP_URL_PATH)); // phpcs:ignore

        if ($this->dataHelper->canForce() && ! $this->dataHelper->isOnlyOptionsRequest()) {
            $this->dataHelper->redirectToAmpPageVersion($observer->getResponse());
        }
    }

    /**
     * @param string $requestUri
     * @return $this
     */
    private function addRequestUriToCache($requestUri)
    {
        if ($this->dataHelper->isAllowedPage()) {
            $this->cache->add((string) $requestUri)
                ->save();
        }

        return $this;
    }
}
