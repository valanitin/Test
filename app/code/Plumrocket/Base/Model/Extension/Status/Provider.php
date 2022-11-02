<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Status;

use Plumrocket\Base\Api\ExtensionStatusInterface;
use Plumrocket\Base\Model\Extension\GetModuleName;

/**
 * @since 2.4.1
 */
class Provider implements ExtensionStatusInterface
{

    /**
     * @var \Plumrocket\Base\Model\Extension\Status\Get
     */
    private $getExtensionStatus;

    /**
     * @var \Plumrocket\Base\Model\Extension\GetModuleName
     */
    private $getExtensionName;

    /**
     * @param \Plumrocket\Base\Model\Extension\Status\Get    $getExtensionStatus
     * @param \Plumrocket\Base\Model\Extension\GetModuleName $getExtensionName
     */
    public function __construct(
        Get $getExtensionStatus,
        GetModuleName $getExtensionName
    ) {
        $this->getExtensionStatus = $getExtensionStatus;
        $this->getExtensionName = $getExtensionName;
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(string $moduleName): bool
    {
        return self::ENABLED === $this->getExtensionStatus->execute($this->getExtensionName->execute($moduleName));
    }

    /**
     * @inheritDoc
     */
    public function isDisabled(string $moduleName): bool
    {
        return self::DISABLED === $this->getExtensionStatus->execute($this->getExtensionName->execute($moduleName));
    }

    /**
     * @inheritDoc
     */
    public function isDisabledFromCli(string $moduleName): bool
    {
        $status = $this->getExtensionStatus->execute($this->getExtensionName->execute($moduleName));
        return self::DISABLED_FROM_CLI === $status;
    }

    /**
     * @inheritDoc
     */
    public function isNotInstalled(string $moduleName): bool
    {
        $status = $this->getExtensionStatus->execute($this->getExtensionName->execute($moduleName));
        return self::NOT_INSTALLED === $status;
    }
}
