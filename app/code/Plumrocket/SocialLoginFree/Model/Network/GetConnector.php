<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

declare(strict_types=1);

namespace Plumrocket\SocialLoginFree\Model\Network;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\ObjectManagerInterface;
use Plumrocket\SocialLoginFree\Api\GetNetworkConnectorInterface;
use Plumrocket\SocialLoginFree\Api\NetworkConnectorInterface;
use Plumrocket\SocialLoginFree\Helper\Config\Network;

/**
 * @since 3.2.0
 */
class GetConnector implements GetNetworkConnectorInterface
{

    /**
     * @var \Plumrocket\SocialLoginFree\Helper\Config\Network
     */
    private $networkConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param \Plumrocket\SocialLoginFree\Helper\Config\Network $networkConfig
     * @param \Magento\Framework\ObjectManagerInterface        $objectManager
     */
    public function __construct(Network $networkConfig, ObjectManagerInterface $objectManager)
    {
        $this->networkConfig = $networkConfig;
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function execute(string $networkCode): NetworkConnectorInterface
    {
        $connectorClass = $this->networkConfig->getConnectorClass($networkCode);
        if (! $connectorClass) {
            NoSuchEntityException::singleField('networkCode', $networkCode);
        }

        return $this->objectManager->get($connectorClass);
    }
}
