<?php
/**
 * @package     Plumrocket_Base
 * @copyright   Copyright (c) 2020 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license/  End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\Base\Model\Extension\Authorization;

use Magento\Framework\ObjectManagerInterface;
use Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface;

/**
 * @since 2.5.0
 */
class Factory
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $moduleName
     * @return \Plumrocket\Base\Api\Data\ExtensionAuthorizationInterface|Data\ExtensionAuthorization
     */
    public function create(string $moduleName): ExtensionAuthorizationInterface
    {
        return $this->objectManager->create(ExtensionAuthorizationInterface::class, ['name' => $moduleName]);
    }
}
