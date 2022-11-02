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
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Product;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManager;
use Plumrocket\Base\Api\ProductStatusManagerInterface;
use Plumrocket\Base\Helper\Base;
use Plumrocket\Base\Model\Product\Command\Disable;
use Plumrocket\Base\Model\Utils\GetExtensionName;

/**
 * @since 2.3.7
 */
class StatusManager implements ProductStatusManagerInterface
{
    private $helper;

    /**
     * @var \Plumrocket\Base\Helper\Base
     */
    private $baseHelper;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * @var \Plumrocket\Base\Model\Product\Command\Disable
     */
    private $disableCommand;

    /**
     * @var \Plumrocket\Base\Model\Utils\GetExtensionName
     */
    private $getExtensionName;

    /**
     * @param \Plumrocket\Base\Helper\Base                   $baseHelper
     * @param \Magento\Store\Model\StoreManager              $storeManager
     * @param \Plumrocket\Base\Model\Product\Command\Disable $disableCommand
     * @param \Plumrocket\Base\Model\Utils\GetExtensionName  $getExtensionName
     */
    public function __construct(
        Base $baseHelper,
        StoreManager $storeManager,
        Disable $disableCommand,
        GetExtensionName $getExtensionName
    ) {
        $this->baseHelper = $baseHelper;
        $this->storeManager = $storeManager;
        $this->disableCommand = $disableCommand;
        $this->getExtensionName = $getExtensionName;
    }

    public function isEnabled(string $moduleName): bool
    {
        $moduleName = $this->getExtensionName->execute($moduleName);

        try {
            $config = $this->baseHelper->getConfigHelper($moduleName);
            foreach ($this->storeManager->getStores() as $store) {
                if ($store->getIsActive() && $config->isModuleEnabled($store->getId())) {
                    return true;
                }
            }
        } catch (NoSuchEntityException $e) {
            $helper = $this->getHelper($moduleName);
            if ($helper && method_exists($helper, 'moduleEnabled')) {
                foreach ($this->storeManager->getStores() as $store) {
                    if ($store->getIsActive() && $helper->moduleEnabled($store->getId())) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public function disable(string $moduleName): bool
    {
        $moduleName = $this->getExtensionName->execute($moduleName);

        // TODO: remove usage of 'disableExtension' after finished integration with all extensions
        $helper = $this->getHelper($moduleName);
        if ($helper && method_exists($helper, 'disableExtension')) {
            $helper->disableExtension();
            return true;
        }

        return $this->disableCommand->execute($moduleName);
    }

    /**
     * Receive helper
     *
     * @param string $moduleName
     * @return \Magento\Framework\App\Helper\AbstractHelper|\Plumrocket\Base\Helper\Main|false
     */
    public function getHelper(string $moduleName)
    {
        if (null === $this->helper) {
            try {
                $this->helper = $this->baseHelper->getModuleHelper($moduleName);
            } catch (NoSuchEntityException $e) {
                $this->helper = false;
            }
        }
        return $this->helper;
    }
}
