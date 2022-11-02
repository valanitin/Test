<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\SystemConfig;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;

/**
 * @since 3.1.0
 */
class GetCurrentStoreCode
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
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Store\Model\StoreManager       $storeManager
     */
    public function __construct(
        RequestInterface $request,
        StoreManager $storeManager
    ) {
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(): string
    {
        $storeCode = $this->request->getParam('store');

        if (! $storeCode) {
            $websiteCode = $this->request->getParam('website');
            $storeCode = $this->storeManager
                ->getWebsite($websiteCode)
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

        return $storeCode;
    }
}
