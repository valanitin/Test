<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Retrieve names of installed plumrocket extensions
 * For example ['Base', 'SocialLoginFree']
 *
 * @since 2.3.0
 */
class GetListOfInstalled
{
    /**
     * @var \Magento\Framework\Module\ModuleListInterface
     */
    private $moduleList;

    /**
     * GetPlumrocketInstalledExtensions constructor.
     *
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     */
    public function __construct(ModuleListInterface $moduleList)
    {
        $this->moduleList = $moduleList;
    }

    /**
     * @return string[] only module name, without vendor name
     */
    public function execute(): array
    {
        $plumrocketModuleNames = [];
        foreach ($this->moduleList->getNames() as $moduleFullName) {
            list($vendor, $module) = explode('_', $moduleFullName);
            if ('Plumrocket' !== $vendor) {
                continue;
            }

            $plumrocketModuleNames[] = $module;
        }

        return $plumrocketModuleNames;
    }
}
