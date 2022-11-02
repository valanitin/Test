<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2021 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization\Status;

use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;

/**
 * Get new status from store
 *
 * @since 2.5.0
 */
class Refresh
{
    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Status\Load
     */
    private $loadStatus;

    /**
     * @var \Plumrocket\Base\Model\Extension\Authorization\Status\Update
     */
    private $updateStatus;

    /**
     * @param \Plumrocket\Base\Model\Extension\Authorization\Status\Load   $loadStatus
     * @param \Plumrocket\Base\Model\Extension\Authorization\Status\Update $updateStatus
     */
    public function __construct(
        Load $loadStatus,
        Update $updateStatus
    ) {
        $this->loadStatus = $loadStatus;
        $this->updateStatus = $updateStatus;
    }

    /**
     * @param \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface $extensionAuthorization
     * @return mixed|string
     */
    public function execute(ExtensionAuthorizationInterface $extensionAuthorization)
    {
        $status = $this->loadStatus->execute($extensionAuthorization->getModuleName());
        if ($extensionAuthorization->getStatus() !== $status) {
            $this->updateStatus->execute($extensionAuthorization, $status);
        }

        return $extensionAuthorization;
    }
}
