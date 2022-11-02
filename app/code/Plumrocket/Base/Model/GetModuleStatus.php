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

namespace Plumrocket\Base\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager as ModuleManager;
use Plumrocket\Base\Api\GetExtensionStatusInterface;
use Plumrocket\Base\Helper\Base;

/**
 * Class GetModuleVersion
 *
 * @since 2.3.9
 */
class GetModuleStatus implements GetExtensionStatusInterface
{
    /**
     * @var \Plumrocket\Base\Helper\Base
     */
    private $baseHelper;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @param \Plumrocket\Base\Helper\Base      $baseHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     */
    public function __construct(
        Base $baseHelper,
        ModuleManager $moduleManager
    ) {
        $this->baseHelper = $baseHelper;
        $this->moduleManager = $moduleManager;
    }

    public function execute(string $moduleName): int
    {
        $hasModule = $this->moduleManager->isEnabled("Plumrocket_$moduleName");
        if (! $hasModule) {
            return self::MODULE_STATUS_NOT_INSTALLED;
        }

        try {
            return $this->baseHelper->getConfigHelper($moduleName)->isModuleEnabled()
                ? self::MODULE_STATUS_ENABLED
                : self::MODULE_STATUS_DISABLED;
        } catch (NoSuchEntityException $e) {
            try {
                $dataHelper = $this->baseHelper->getModuleHelper($moduleName);
                if (method_exists($dataHelper, 'moduleEnabled')) {
                    return $dataHelper->moduleEnabled()
                        ? self::MODULE_STATUS_ENABLED
                        : self::MODULE_STATUS_DISABLED;
                }

                return self::MODULE_STATUS_ENABLED;
            } catch (NoSuchEntityException $e) {
                return self::MODULE_STATUS_ENABLED;
            }
        }
    }
}
