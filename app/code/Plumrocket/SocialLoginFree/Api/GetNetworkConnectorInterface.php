<?php
/**
 * @package     Plumrocket_SocialLoginFree
 * @copyright   Copyright (c) 2022 Plumrocket Inc. (https://plumrocket.com)
 * @license     https://plumrocket.com/license   End-user License Agreement
 */

namespace Plumrocket\SocialLoginFree\Api;

/**
 * @since 3.2.0
 */
interface GetNetworkConnectorInterface
{

    /**
     * Get network connector.
     *
     * @param string $networkCode
     * @return \Plumrocket\SocialLoginFree\Api\NetworkConnectorInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(string $networkCode): NetworkConnectorInterface;
}
