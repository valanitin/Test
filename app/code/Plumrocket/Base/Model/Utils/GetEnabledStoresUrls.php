<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Utils;

use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Plumrocket\Base\Api\ConfigUtilsInterface;

/**
 * @since 2.5.0
 */
class GetEnabledStoresUrls
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $configUtils;

    /**
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface  $configUtils
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        ConfigUtilsInterface $configUtils,
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        $this->configUtils = $configUtils;
    }

    /**
     * Retrieve list of unique enabled stores urls
     *
     * @return array
     */
    public function execute(): array
    {
        $storesUrls = [];
        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getIsActive()) {
                $storesUrls[] = $this->configUtils->getStoreConfig(
                    Store::XML_PATH_SECURE_BASE_URL,
                    $store->getId()
                );
            }
        }

        return array_unique($storesUrls);
    }
}
