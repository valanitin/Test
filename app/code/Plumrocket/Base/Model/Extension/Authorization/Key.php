<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Plumrocket\Base\Api\GetExtensionInformationInterface;

/**
 * @since 2.5.0
 */
class Key
{
    /**
     * @var string[]
     */
    private $keyCache = [];

    /**
     * @var string[]
     */
    private $keyPathCache = [];

    /**
     * @var \Plumrocket\Base\Api\GetExtensionInformationInterface
     */
    private $getExtensionInformation;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param \Plumrocket\Base\Api\GetExtensionInformationInterface $getExtensionInformation
     * @param \Magento\Framework\ObjectManagerInterface             $objectManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface    $scopeConfig
     */
    public function __construct(
        GetExtensionInformationInterface $getExtensionInformation,
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->getExtensionInformation = $getExtensionInformation;
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve key from config
     *
     * @param string $moduleName
     * @return string
     */
    public function get(string $moduleName): string
    {
        if (! isset($this->keyCache[$moduleName]) || ! $this->keyCache[$moduleName]) {
            $this->keyCache[$moduleName] = (string) $this->scopeConfig->getValue(
                $this->getPath($moduleName),
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                0
            );
        }

        return $this->keyCache[$moduleName];
    }

    /**
     * @param string $moduleName
     * @param string $key
     * @return $this
     */
    public function set(string $moduleName, string $key): Key
    {
        $this->keyCache[$moduleName] = $key;
        return $this;
    }

    /**
     * Retrieve config path to key
     *
     * @param string $moduleName
     * @return string
     */
    public function getPath(string $moduleName): string
    {
        if (! isset($this->keyPathCache[$moduleName])) {
            $info = $this->getExtensionInformation->execute($moduleName);
            if ($configSection = $info->getConfigSection()) {
                $path = "$configSection/general/serial";
            } else {
                $mtd = 'getConfigSectionId';
                $helper = $this->getHelper($moduleName);
                if ($helper && method_exists($helper, $mtd)) {
                    $path = $helper->$mtd() . '/general/serial';
                } else {
                    $path = 'custom/general/serial';
                }
            }

            $this->keyPathCache[$moduleName] = $path;
        }

        return $this->keyPathCache[$moduleName];
    }

    /**
     * Retrieve data helper
     * TODO: remove after adding pr_extensions.xml to all modules
     * @deprecated
     *
     * @param string $moduleName
     * @return \Plumrocket\Base\Helper\Base|false
     */
    private function getHelper(string $moduleName)
    {
        try {
            return $this->objectManager->get("Plumrocket\\{$moduleName}\Helper\Data");
        } catch (\ReflectionException $e) {
            return false;
        }
    }
}
