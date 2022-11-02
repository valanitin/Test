<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Model\Provider;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

/**
 * @deprecated since 3.2.0
 * @see \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver
 */
class Callback
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Magento\Backend\App\Area\FrontNameResolver
     */
    private $frontNameResolver;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Callback constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Backend\App\Area\FrontNameResolver $frontNameResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        RequestInterface $request,
        StoreManager $storeManager,
        FrontNameResolver $frontNameResolver,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->frontNameResolver = $frontNameResolver;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get redirect url by provider
     *
     * @param string
     * @param boolean
     * @return string
     * @deprecated since 3.2.0
     * @see \Plumrocket\SocialLoginFree\Model\Network\ModalCallbackUrlResolver::getUrl()
     */
    public function getUrl($provider, $byRequest = false)
    {
        $storeCode = $this->request->getParam('store');

        if (! $storeCode) {
            $websiteCode = $this->request->getParam('website');
            $storeCode = $this->storeManager
                ->getWebsite($byRequest ? $websiteCode : null)
                ->getDefaultGroup()
                ->getDefaultStoreId();

            if (! $storeCode) {
                $websites = $this->storeManager->getWebsites(true);

                foreach ($websites as $website) {
                    $storeCode = $website->getDefaultGroup()->getDefaultStoreId();

                    if ($storeCode) {
                        break;
                    }
                }
            }

            if (! $storeCode) {
                $storeCode = Store::DEFAULT_STORE_ID;
            }
        }
        $url = $this->storeManager->getStore($storeCode)->getUrl(
            'pslogin/account/login',
            ['type' => $provider, 'key' => false, '_nosid' => true]
        );

        $url = str_replace(
            DIRECTORY_SEPARATOR . $this->frontNameResolver->getFrontName() . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $url
        );

        if (false !== ($length = stripos($url, '?'))) {
            $url = substr($url, 0, $length);
        }

        $url = preg_replace('/key\/(.*)/', '', $url);

        if ($byRequest && $this->scopeConfig->getValue('web/seo/use_rewrites')) {
            $url = str_replace('index.php/', '', $url);
        }

        return $url;
    }
}
