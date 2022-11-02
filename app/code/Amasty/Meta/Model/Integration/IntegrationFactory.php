<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_Meta
 */


namespace Amasty\Meta\Model\Integration;

use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Module\Manager as ModuleManager;

class IntegrationFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleManager $moduleManager
    ) {
        $this->objectManager = $objectManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param string $type
     * @return mixed
     */
    public function get(string $type)
    {
        return $this->isModuleEnabled($type) && class_exists($type) ? $this->objectManager->get($type) : null;
    }

    private function isModuleEnabled(string $type): bool
    {
        $moduleName = implode('_', array_slice(explode('\\', $type), 0, 2));

        return $this->moduleManager->isEnabled($moduleName);
    }
}
