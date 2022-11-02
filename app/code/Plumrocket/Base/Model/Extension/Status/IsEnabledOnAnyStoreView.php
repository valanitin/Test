<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Status;

use Magento\Store\Model\StoreManager;
use Plumrocket\Base\Api\ConfigUtilsInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;
use Plumrocket\Base\Model\Extension\GetModuleName;

/**
 * Check if module is enabled in configurations on any store view
 *
 * @since 2.5.0
 */
class IsEnabledOnAnyStoreView
{

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getExtensionName;

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Plumrocket\Base\Api\ConfigUtilsInterface
     */
    private $configUtils;

    /**
     * @param \Magento\Store\Model\StoreManager                     $storeManager
     * @param \Plumrocket\Base\Model\Extension\GetModuleName        $getExtensionName
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     * @param \Plumrocket\Base\Api\ConfigUtilsInterface             $configUtils
     */
    public function __construct(
        StoreManager $storeManager,
        GetModuleName $getExtensionName,
        GetExtensionInformationInterface $getExtensionInformation,
        ConfigUtilsInterface $configUtils
    ) {
        $this->storeManager = $storeManager;
        $this->getExtensionName = $getExtensionName;
        $this->getExtensionInformation = $getExtensionInformation;
        $this->configUtils = $configUtils;
    }

    /**
     * Check if module is enabled on any store view.
     *
     * @param string $moduleName
     * @return bool
     */
    public function execute(string $moduleName): bool
    {
        $moduleName = $this->getExtensionName->execute($moduleName);
        $configPath = $this->getExtensionInformation->execute($moduleName)->getIsEnabledFieldConfigPath();

        foreach ($this->storeManager->getStores() as $store) {
            if (! $store->getIsActive()) {
                continue;
            }

            if ($configPath && $this->configUtils->getStoreConfig($configPath, $store->getId())) {
                return true;
            }
        }

        return false;
    }
}
