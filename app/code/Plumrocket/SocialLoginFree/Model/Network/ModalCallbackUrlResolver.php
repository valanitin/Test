<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

/**
 * Retrieve callback for social network
 *
 * @since 3.2.0
 */
class ModalCallbackUrlResolver
{

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
     * @param \Magento\Store\Model\StoreManager                  $storeManager
     * @param \Magento\Backend\App\Area\FrontNameResolver        $frontNameResolver
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManager $storeManager,
        FrontNameResolver $frontNameResolver,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->frontNameResolver = $frontNameResolver;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get redirect url.
     *
     * @param string   $networkCode
     * @param int|null $websiteId
     * @param int|null $storeId
     * @return string
     */
    public function getUrl(string $networkCode, int $websiteId = null, int $storeId = null): string
    {
        $store = $this->getStore($websiteId, $storeId);

        $url = $store->getUrl(
            'pslogin/account/login',
            ['type' => $networkCode, 'key' => false, '_nosid' => true]
        );

        $url = str_replace(
            DIRECTORY_SEPARATOR . $this->frontNameResolver->getFrontName() . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $url
        );

        if (false !== ($length = strpos($url, '?'))) {
            $url = substr($url, 0, $length);
        }

        $url = preg_replace('/key\/(.*)/', '', $url);

        if ($websiteId && $this->scopeConfig->getValue('web/seo/use_rewrites')) {
            $url = str_replace('index.php/', '', $url);
        }

        return $url;
    }

    /**
     * Find store by website and store ids
     *
     * @param int|null $websiteId
     * @param int|null $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    private function getStore(int $websiteId = null, int $storeId = null): StoreInterface
    {
        if (! $storeId) {
            $storeId = $this->storeManager
                ->getWebsite($websiteId ?: null)
                ->getDefaultGroup()
                ->getDefaultStoreId();
        }

        if (! $storeId) {
            foreach ($this->storeManager->getWebsites(true) as $website) {
                if ($storeId = $website->getDefaultGroup()->getDefaultStoreId()) {
                    break;
                }
            }
        }

        if (! $storeId) {
            $storeId = Store::DEFAULT_STORE_ID;
        }

        return $this->storeManager->getStore($storeId);
    }
}
