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
 * @package     Plumrocket Amp
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Amp\Model\Plugin\Magento\PageCache\Model\App\FrontController;

class BuiltinPluginPlugin
{
    const CACHE_IDENTIFIER = 'PRAMP_MAGENTO_VERSION';

    /**
     * @var \Plumrocket\Amp\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * BuiltinPluginPlugin constructor.
     *
     * @param \Plumrocket\Amp\Helper\Data                            $data
     * @param \Magento\Framework\Stdlib\CookieManagerInterface       $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\RequestInterface                $request
     */
    public function __construct(
        \Plumrocket\Amp\Helper\Data $data,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->dataHelper = $data;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->request = $request;
    }

    /**
     * Fix for Magento FPC.
     *
     * TODO: remove after left support magento 2.2
     *
     * @param \Magento\PageCache\Model\App\FrontController\BuiltinPlugin $subject
     * @param                                                            $result
     * @return mixed
     */
    public function afterAroundDispatch(\Magento\PageCache\Model\App\FrontController\BuiltinPlugin $subject, $result)
    {
        if ($this->dataHelper->isAmpRequest()
            || $this->request->getFullActionName() === 'pramp_cart_add'
            || ($this->request->getParam('amp') === '1' && $this->dataHelper->moduleEnabled())
        ) {
            $this->deleteCookie(\Plumrocket\Amp\Block\Page\Html\Messages::MESSAGES_COOKIES_NAME);
        }

        return $result;
    }

    /**
     * @param $coolieName
     * @return $this
     */
    private function deleteCookie($coolieName)
    {
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDuration(0);
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setDomain('');

        $this->cookieManager->deleteCookie(
            $coolieName,
            $publicCookieMetadata
        );

        return $this;
    }
}
