<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extensions;

use Plumrocket\Base\Model\Extension\GetModuleName;

/**
 * @since 2.3.0
 * @deprecated since 2.5.0
 * @see \Plumrocket\Base\Model\Extension\Information\Get
 */
class GetInformation
{
    /**
     * @var \Plumrocket\Base\Api\ModuleInformationInterface[]
     */
    private $extensions;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getExtensionName;

    /**
     * @param \Plumrocket\Base\Model\Extension\GetModuleName $getModuleName
     * @param array                                          $extensions
     */
    public function __construct(
        GetModuleName $getModuleName,
        array $extensions = []
    ) {
        $this->getExtensionName = $getModuleName;
        $this->extensions = $extensions;
    }

    /**
     * @param string $moduleName
     * @return \Plumrocket\Base\Api\ModuleInformationInterface|null
     */
    public function execute(string $moduleName)
    {
        $moduleName = $this->getExtensionName->execute($moduleName);
        return $this->extensions[$moduleName] ?? null;
    }
}
