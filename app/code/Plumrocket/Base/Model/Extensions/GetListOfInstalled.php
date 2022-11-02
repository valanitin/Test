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

namespace Plumrocket\Base\Model\Extensions;

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
